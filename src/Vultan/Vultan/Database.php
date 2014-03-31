<?php
/**
 * @file
 * The database connection and tools.
 *
 * @copyright Copyright(c) 2013 Chris Skene
 * @license GPL v2 http://www.fsf.org/licensing/licenses/gpl.html
 * @author Chris Skene chris at xtfer dot com
 */

namespace Vultan\Vultan;

use Vultan\Config\Config;
use Vultan\Document\DocumentFactory;
use Vultan\Document\DocumentInterface;
use Vultan\Exception\VultanDataException;
use Vultan\Exception\VultanException;

use Vultan\Traits\ConfigTrait;
use MongoDB;
use MongoCollection;
use MongoCursor;
use MongoId;

/**
 * The Database connection and tools
 */
class Database {

  const OP_INSERT = 'insert';
  const OP_UPDATE = 'update';
  const OP_UPSERT = 'upsert';

  use ConfigTrait;

  /**
   * The currently active collection
   *
   * @var MongoCollection
   */
  public $collection;

  /**
   * The MongoDB object
   *
   * @var MongoDB
   */
  protected $mongoDB;

  /**
   * The last retrieved find result.
   *
   * @var \MongoCursor
   */
  protected $lastCursor;

  /**
   * Result of the last operation.
   *
   * @var \vultan\Vultan\Result
   */
  protected $lastResult;

  /**
   * Constructor function.
   */
  public function __construct(Config\Config $config) {
    $this->config = $config;
  }

  /**
   * Delete items matching a filter.
   *
   * @param array $filter
   *   A normal MongoDB filter array
   * @param bool $safe
   *   If TRUE, conduct a safe insert. Note that this returns an exception on
   *   error, which you will need to catch.
   *
   * @return mixed
   *   Result of the Remove operation.
   */
  public function delete($filter, $safe = FALSE) {

    return $this->getCollection()
      ->remove($filter, array("safe" => $safe));
  }

  /**
   * Delete an item.
   *
   * @param string $identifier
   *   The ID of the item to delete
   * @param bool $safe
   *   If TRUE, conduct a safe insert. Note that this returns an exception on
   *   error, which you will need to catch.
   *
   * @return mixed
   *   Result of the Remove operation.
   */
  public function deleteByID($identifier, $safe = FALSE) {

    $filter = $this->filterID($identifier);

    return $this->getCollection()
      ->remove($filter, array("justOne" => TRUE, "safe" => $safe));
  }

  /**
   * Shortcut to return all results for a query in an array.
   *
   * @param array $filter
   *   A standard MongoDB filter
   *
   * @return array
   *   An array of results
   */
  public function findAll(array $filter) {

    $results = array();

    $this->find($filter);
    if ($this->getLastCursor()->hasNext()) {
      foreach ($this->getLastCursor() as $result) {
        $results[] = $result;
      }
    }

    return $results;
  }

  /**
   * Query the current collection.
   *
   * Simply a wrapper around MongoCollection::find(), this function returns a
   * MongoCursor object which should be foreach()ed to get its results. We
   * also store the returned value in $last_cursor just in case we need it
   * again.
   *
   * Notes on the MongoCursor.
   *
   * A MongoCursor has two "life stages": pre- and post- query. When a cursor
   * is created, it has not yet contacted the database, so it is in its
   * pre-query state. In this state, the client can further specify what they
   * want the query to do, including adding limits, skips, sorts, and more
   * advanced options.
   *
   * When the client attempts to get a result (by calling MongoCursor::next(),
   * directly or indirectly), the cursor moves into the post-query stage. At
   * this point, the query has been executed by the database and cannot be
   * modified anymore.
   *
   * MongoCursors returned by this find method are in the pre-query stage, and
   * can be modified.
   *
   * @param array $filter
   *   MongoDB's query language is quite extensive. The PHP driver will in
   *   almost all cases pass the query straight through to the server, so
   *   reading the MongoDB core docs on » find is a good idea.
   * @param array $fields
   *   (Optional) Fields of the results to return. The array is in the format
   *   array('fieldname' => true, 'fieldname2' => true), or simply a list of
   *   field names. The _id field is always returned.
   *
   * @return MongoCursor|bool
   *   A MongoCursor, or FALSE.
   *
   * @see http://docs.mongodb.org/manual/reference/method/db.collection.find/
   * @see http://www.php.net/manual/en/class.mongocursor.php
   */
  public function find(array $filter, $fields = array()) {

    if (!empty($fields)) {
      foreach ($fields as $key => $value) {
        if (is_numeric($key) && !is_bool($value)) {
          $fields[$value] = TRUE;
          unset($fields[$key]);
        }
      }
    }
    else {
      $fields = array();
    }

    $result = $this->getCollection()
      ->find($filter, $fields);

    if (!empty($result) && $result instanceof \MongoCursor) {
      $this->setLastCursor($result);

      return $this->getLastCursor();
    }

    return FALSE;
  }

  /**
   * Query by ID.
   *
   * As finding by ID should only return one result, we shortcut any cursor
   * foreach handling and simply translate the result into an array on return.
   *
   * @param string $identifier
   *   An ID of a Mongo document. We convert to a MongoID automatically.
   *
   * @return array|bool
   *   Either the result of the find as an array, or FALSE
   */
  public function findByID($identifier) {

    $filter = $this->filterID($identifier);
    $result = $this->getCollection()
      ->find($filter);

    if (!empty($result) && $result instanceof \MongoCursor) {

      // @todo: Should this be an array?
      return iterator_to_array($this->getLastCursor());
    }

    return FALSE;
  }

  /**
   * Get the value for LastCursor.
   *
   * @return \MongoCursor
   *   The value of LastCursor.
   */
  public function getLastCursor() {

    return $this->lastCursor;
  }

  /**
   * Set the value for LastCursor.
   *
   * @param \MongoCursor $cursor
   *   The value to set.
   */
  public function setLastCursor(\MongoCursor $cursor) {

    $this->lastCursor = $cursor;
  }


  /**
   * Insert into a collection.
   *
   * @todo Support optional parameters j (formerly fsync), wtimeout, timeout
   * @todo Error handling for return values
   *
   * @param array|object $document
   *   Preferably a Vultan Document, however we also support an array of data,
   *   and other objects public properties will be passed, or objects can
   *   implement the DocumentCompatibilityInterface.
   * @param bool $safe
   *   Whether the insert should be "safe" or not.
   *
   * @return \Vultan\Vultan\Result
   *   A result object.
   *
   * @link http://php.net/manual/en/mongocollection.insert.php
   */
  public function insert($document, $safe = FALSE) {

    // @todo: Handle creation of IDs.
    // http://www.php.net/manual/en/mongocollection.insert.php#111848
    // Data should not be empty. This will return an Exception, so lets just
    // catch it now.
    if (empty($document)) {
      return $this->processWriteResult(static::OP_INSERT, FALSE);
    }

    $document = $this->prepareDocument($document);

    $collection = $document->getCollection();
    if (!empty($collection)) {
      $this->useCollection($collection);
    }

    try {

      $options = array('w' => $safe);
      $data = $document->getValues();

      $result = $this->getCollection()
        ->insert($data, $options);

      $document->setIdentifier($this->extractID($data));
    }
    catch (\MongoCursorTimeoutException $e) {

      // Throws MongoCursorTimeoutException if the "w" option is set to a value
      // greater than one and the operation takes longer than
      // MongoCursor::$timeout milliseconds to complete. This does not kill the
      // operation on the server, it is a client-side timeout. The operation in
      // MongoCollection::$wtimeout is milliseconds.
      return $this->processWriteResult(static::OP_INSERT, FALSE);
    }
    catch (\MongoCursorException $e) {

      // Throws MongoCursorException if the "w" option is set and the write
      // fails or if an item with an ID already set is passed.
      return $this->processWriteResult(static::OP_INSERT, FALSE);
    }
    catch (\MongoException $e) {

      // Throws MongoException if the inserted document is empty or if it
      // contains zero-length keys. Attempting to insert an object with
      // protected and private properties will cause a zero-length key error.
      return $this->processWriteResult(static::OP_INSERT, FALSE);
    }

    return $this->processWriteResult(static::OP_INSERT, $result, $document);
  }

  /**
   * Process the result of a write operation.
   *
   * @param string $op
   *   The operation.
   * @param mixed $result
   *   The result returned by the write operation.
   * @param DocumentInterface $document
   *   (Optional) The data passed to the write operation.
   *
   * @return \Vultan\Vultan\Result
   *   The Result object.
   */
  protected function processWriteResult($op, $result, DocumentInterface $document = NULL) {

    $this->lastResult = new Result();

    $this->getLastResult()->setOperation($op);

    // Results as array indicate the operation was processed with a writeConcern
    // == TRUE, and the operation is in safe mode.
    if (is_array($result)) {

      $this->getLastResult()->setResult($result);

      if (is_null($result['err'])) {
        $this->getLastResult()->setSuccess(TRUE);
      }
      else {
        $this->getLastResult()->setError($result['err']);
        $this->getLastResult()->setSuccess(FALSE);
      }
    }
    elseif (is_bool($result)) {
      $this->getLastResult()->setSuccess($result);
    }

    if (!empty($document)) {
      $this->getLastResult()->setId($document->getId());
      $this->getLastResult()->setDocument($document);
    }

    return $this->getLastResult();
  }

  /**
   * Given a MongoID, return the ID number.
   *
   * @param array $data
   *   An array containing a MongoID.
   *
   * @return string|bool
   *   A string ID, or FALSE.
   */
  public function extractID($data) {

    $item = NULL;
    if (is_array($data) && isset($data['_id'])) {
      $item = (array) $data['_id'];
    }
    elseif (is_object($data)) {
      $item = (array) $data;
    }
    else {
      return FALSE;
    }

    if (isset($item['$id'])) {
      return $item['$id'];
    }

    return FALSE;
  }

  /**
   * Get the Collection.
   *
   * @return MongoCollection
   *   A Mongo Collection.
   */
  public function getCollection() {

    return $this->collection;
  }

  /**
   * Helper to add a modifier to data.
   *
   * @todo Individual validation for modifier types
   *
   * @param string $modifier
   *   The Mongo modifier to set
   * @param array $data
   *   The data to use
   *
   * @throws \Vultan\Exception\VultanDataException
   * @return array
   *   An array suitable for insertion in to Mongo
   */
  public function mod($modifier, $data) {

    if (is_array($data)) {
      foreach (array_keys($data) as $key) {
        if ($key == '_id') {
          unset($data[$key]);
          throw new VultanDataException('Mongo IDs can not be used in modifier operations');
        }
      }
    }

    return array('$' . $modifier => $data);
  }

  /**
   * Sanitize user input before using it in a filter.
   *
   * To avoid request injection attacks, user data used in queries should be in
   * the form of strings before sending it to the query functions (like find(),
   * for example). This function can be used to quickly sanitize data before
   * use.
   *
   * @param mixed $data
   *   The data to sanitize
   *
   * @return string
   *   A string.
   */
  public function sanitize($data) {

    return (string) $data;
  }

  /**
   * Set the database.
   *
   * @param MongoDB $database
   *   A MongoDB database.
   */
  public function setDataSource(MongoDB $database) {

    $this->mongoDB = $database;
  }

  /**
   * Update the first matched item.
   *
   * For updating properties where only one item should exist
   *
   * @param array $filter
   *   An array of keys to match on
   * @param object|array $document
   *   Preferably a Vultan Document, however we also support an array of data,
   *   and other objects public properties will be passed, or objects can
   *   implement the DocumentCompatibilityInterface.
   * @param bool $partial
   *   If TRUE, this is a partial update and Vultan will only update the fields
   *   provided in $data. If FALSE, $data completely overwrites the object.
   * @param array $options
   *   This parameter is an associative array of the form
   *   array("optionname" => boolean, ...). Possible options are 'upsert',
   *   'multiple', 'safe', 'fsync' and 'timeout'.
   *
   * @see http://www.php.net/manual/en/mongocollection.update.php
   *
   * @return \Vultan\Vultan\Result
   *   A result object.
   *
   * @see \Mongo\Core\Database::insert()
   */
  public function update(array $filter, $document, $partial = FALSE, $options = array()) {

    // @todo: Handle creation of IDs.
    // http://www.php.net/manual/en/mongocollection.insert.php#111848
    // Data should not be empty. This will return an Exception, so lets just
    // catch it now.
    if (empty($document)) {
      return $this->processWriteResult(static::OP_UPDATE, FALSE);
    }

    // Ensure we have a properly prepared Document.
    $document = $this->prepareDocument($document);

    // Update single - this is the default behaviour, however the Mongo docs
    // suggest that this should be set explicitly for future-proofing
    $options = array('multiple' => FALSE) + $options;

    // Extract the values to insert.
    $data = $document->getValues();

    // Fix partial keys.
    if (isset($partial) && $partial == TRUE) {
      // Add a 'set' modifier.
      $data = $this->mod('set', $data);
    }

    $result = $this->getCollection()
      ->update($filter, $data, $options);

    $document->setIdentifier($this->extractID($data));

    return $this->processWriteResult(static::OP_UPDATE, $result, $document);
  }

  /**
   * Update all matched items.
   *
   * Useful for changing properties across a range of items simultaneously.
   *
   * @todo Add support for atomic updates
   * @todo $partial is not respected
   *
   * @param array $filter
   *   An array of keys to match on
   * @param object|array $document
   *   Preferably a Vultan Document, however we also support an array of data,
   *   and other objects public properties will be passed, or objects can
   *   implement the DocumentCompatibilityInterface.
   * @param bool $partial
   *   If TRUE, this is a partial update and Vultan will only update the fields
   *   provided in $data. If FALSE, $data completely overwrites the object.
   * @param array $options
   *   Any other options to pass to Mongo
   *
   * @return \Vultan\Vultan\Result
   *   A result object.
   *
   * @see \Mongo\Core\Database::insert()
   */
  public function updateAll(array $filter, $document, $partial = FALSE, $options = array()) {

    // @todo: Handle creation of IDs.
    // http://www.php.net/manual/en/mongocollection.insert.php#111848
    // Data should not be empty. This will return an Exception, so lets just
    // catch it now.
    if (empty($document)) {
      return $this->processWriteResult(static::OP_UPDATE, FALSE);
    }

    // Ensure we have a properly prepared Document.
    $document = $this->prepareDocument($document);
    $data = $document->getValues();

    // Update multiple.
    $options['multiple'] = TRUE;

    // Fix partial keys.
    if (isset($partial) && $partial == TRUE) {
      // Add a 'set' modifier.
      $data = $this->mod('set', $data);
    }

    // Run the update.
    $result = $this->getCollection()
      ->update($filter, $data, $options);

    $document->setIdentifier($this->extractID($data));

    return $this->processWriteResult(static::OP_UPDATE, $result, $document);
  }

  /**
   * Update an individual item by ID.
   *
   * For updating properties on an item when you already know the item's $id
   *
   * @param string $identifier
   *   The ID to update.
   * @param object|array $document
   *   Preferably a Vultan Document, however we also support an array of data, and
   *   other objects public properties will be passed, or objects can implement
   *   the DocumentCompatibilityInterface.
   * @param bool $partial
   *   If TRUE, this is a partial update and Vultan will only update the fields
   *   provided in $data. If FALSE, $data completely overwrites the object.
   * @param array $options
   *   Any other options to pass to Mongo
   *
   * @return \Vultan\Vultan\Result
   *   A result object.
   *
   * @see \Mongo\Core\Database::insert()
   */
  public function updateByID($identifier, $document, $partial = FALSE, $options = array()) {

    $filter = $this->filterID($identifier);

    return $this->update($filter, $document, $partial, $options);
  }

  /**
   * Shortcut to set up a filter for filtering by ID.
   *
   * @param string $identifier
   *   An identifier.
   *
   * @return array
   *   The Mongo filter.
   */
  public function filterID($identifier) {

    $mid = new MongoId($identifier);
    $filter = array('_id' => $mid);

    return $filter;
  }

  /**
   * Upsert an item.
   *
   * For updating or creating an object if it does not already exist
   *
   * @param array $filter
   *   A normal MongoDB filter array
   * @param object|array $document
   *   Preferably a Vultan Document, however we also support an array of data,
   *   and other objects public properties will be passed, or objects can
   *   implement the DocumentCompatibilityInterface.
   * @param bool $safe
   *   If TRUE, conduct a safe insert. Note that this returns an exception on
   *   error, which you will need to catch.
   *
   * @return \Vultan\Vultan\Result
   *   A result object. See \Mongo\Core\Database::insert() for possible values
   *
   * @see \Mongo\Core\Database::insert()
   */
  public function upsert($filter, $document, $safe = FALSE) {

    $options = array("upsert" => TRUE, "multiple" => FALSE, "safe" => $safe);

    // @todo: Handle creation of IDs.
    // http://www.php.net/manual/en/mongocollection.insert.php#111848
    // Data should not be empty. This will return an Exception, so lets just
    // catch it now.
    if (empty($document)) {
      return $this->processWriteResult(static::OP_UPSERT, FALSE);
    }

    // Ensure we have a properly prepared Document.
    $document = $this->prepareDocument($document);
    $data = $document->getValues();

    $result = $this->getCollection()
      ->update($filter, $data, $options);

    $document->setIdentifier($this->extractID($data));
    return $this->processWriteResult(static::OP_UPSERT, $result, $document);
  }

  /**
   * Get the value for LastResult.
   *
   * @return \Vultan\Vultan\Result
   *   The value of LastResult.
   */
  public function getLastResult() {

    if (isset($this->lastResult)) {
      return $this->lastResult;
    }

    return NULL;
  }

  /**
   * Returns a collection object from a db.
   *
   * @param string $collection_name
   *   The collection name.
   *
   * @throws VultanException
   * @return bool|MongoCollection
   *   A MongoCollection object or FALSE.
   */
  public function useCollection($collection_name) {

    $db = $this->getDataSource();
    if (empty($db)) {

    }
    if (!$db instanceof MongoDB) {
      throw new VultanException('Could not load database');
    }

    $this->collection = $db->selectCollection($collection_name);

    return $this->collection;
  }

  /**
   * Get the database.
   *
   * @return MongoDB
   *   A MongoDB database object.
   */
  public function getDataSource() {

    return $this->mongoDB;
  }

  /**
   * Prepare the document data.
   *
   * @param array|object $document
   *   Convert the document data into a valid Document object.
   *
   * @return \Vultan\Document\DocumentInterface
   *   A Vultan Document.
   */
  protected function prepareDocument($document) {

    $values = $document;

    // All of our objects MUST be valid Vultan Documents.
    // If this isn't, convert it to an array now, before we blow our
    // stack with a private or protected Exception.
    if (is_object($document)) {

      if (!$document instanceof DocumentInterface) {
        $values = get_object_vars($document);
      }
    }

    if (is_array($values)) {
      $document = DocumentFactory::init($this->getConfig())
        ->createDocument($values);
    }

    return $document;
  }

}
