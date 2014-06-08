<?php

/**
 * @file
 * Contains a BaseQuery
 */

namespace Vultan\Query;

use Vultan\Config;
use Vultan\Document\DocumentFactory;
use Vultan\Document\DocumentInterface;
use Vultan\Exception\VultanDataException;
use Vultan\Exception\VultanException;
use Vultan\Traits\ConfigTrait;
use Vultan\Traits\MongoIDHelper;
use Vultan\Vultan\Collection;
use Vultan\Vultan\Result;

use MongoCursor;

/**
 * Class BaseQuery
 *
 * @package Vultan\BaseQuery
 */
abstract class BaseQuery implements QueryBaseInterface {

  use ConfigTrait;
  use MongoIDHelper;

  /**
   * Define the BaseQuery Type.
   */
  const QUERY_TYPE = NULL;

  /**
   * Define write concerns.
   */
  const WRITE_SAFE = 1;
  const WRITE_UNSAFE = 0;
  const WRITE_REPLICA_MAJORITY = 'majority';

  /**
   * The collection variable.
   *
   * @var Collection
   */
  public $collection;

  /**
   * Whether to return results as a MongoCursor or an array.
   *
   * @var bool
   */
  protected $asArray;

  /**
   * The distinct variable.
   *
   * @var string
   */
  protected $distinct;

  /**
   * The document variable.
   *
   * @var DocumentInterface
   */
  protected $document;

  /**
   * The fields to return.
   *
   * Should be in the format array("fieldname" => TRUE).
   *
   * @var array
   */
  protected $fields;

  /**
   * The filter variable.
   *
   * @var array
   */
  protected $filter;

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
   * The options variable.
   *
   * @var array
   */
  protected $options;

  /**
   * The result variable.
   *
   * @var
   */
  protected $result;

  /**
   * Public constructor.
   *
   * @param Config $config
   *   A Config object.
   * @param Collection $collection
   *   A Collection object.
   */
  public function __construct(Config $config, Collection $collection) {

    $this->config = $config;
    $this->collection = $collection;

    $this->setup();
  }

  /**
   * Public constructor.
   *
   * @param Config $config
   *   A Config object.
   * @param Collection $collection
   *   A Collection object.
   *
   * @return QueryInterface
   *   A QueryObject
   */
  static public function create(Config $config, Collection $collection) {

    return new static($config, $collection);
  }

  /**
   * Add a filter condition.
   *
   * @param string $key
   *   Name of the field to return. Nested fields can be separated with a .
   *   (full stop), as per the MongoDB query syntax.
   * @param string $value
   *   Value to match on.
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function addCondition($key, $value) {

    $this->filter[$key] = $value;

    return $this;
  }

  /**
   * A special filter to reduce to distinct values.
   *
   * @param string $key
   *   Key to return distinct values for.
   */
  public function distinct($key) {

    $this->distinct = $key;
  }

  /**
   * Shortcut to set up a filter for filtering by ID.
   *
   * @param mixed $identifier
   *   An identifier.
   *
   * @throws \Vultan\Exception\VultanException
   * @return QueryInterface
   *   This Query object.
   */
  public function addFilterMongoID($identifier) {

    if (is_object($identifier)) {
      if (get_class($identifier) != 'MongoID') {
        throw new VultanException('Invalid object passed to addFilterMongoID');
      }

      $mid = $identifier;
    }
    else {
      $mid = $this->createMongoIdentifier($identifier);
    }

    $this->addCondition('_id', $mid);

    return $this;
  }

  /**
   * Execute the query.
   *
   * @return \Vultan\Vultan\Result|MongoCursor|array
   *   The Result object.
   */
  public function execute() {

    // Data should not be empty. This will return an Exception, so lets just
    // catch it now.
    // see http://www.php.net/manual/en/mongocollection.insert.php#111848
    if (empty($this->document)) {
      return $this->processFailedResult('No data provided', static::QUERY_TYPE, FALSE);
    }

    try {

      $this->preExecute();

      $this->runQuery();

      $this->postExecute();
    }
    catch (\MongoCursorTimeoutException $e) {

      // Throws MongoCursorTimeoutException if the "w" option is set to a value
      // greater than one and the operation takes longer than
      // MongoCursor::$timeout milliseconds to complete. This does not kill the
      // operation on the server, it is a client-side timeout. The operation in
      // MongoCollection::$wtimeout is milliseconds.
      return $this->processFailedResult('Mongo timed out', static::QUERY_TYPE, FALSE);
    }
    catch (\MongoCursorException $e) {

      // Throws MongoCursorException if the "w" option is set and the write
      // fails or if an item with an ID already set is passed.
      return $this->processFailedResult('Mongo write failed', static::QUERY_TYPE, FALSE);
    }
    catch (\MongoException $e) {

      // Throws MongoException if the inserted document is empty or if it
      // contains zero-length keys. Attempting to insert an object with
      // protected and private properties will cause a zero-length key error.
      return $this->processFailedResult('MongoException', static::QUERY_TYPE, FALSE);
    }

    $results = $this->processWriteResult();

    return $this->postProcess($results);
  }

  /**
   * Specify Fields to retrieve.
   *
   * @param array $fields
   *   (optional) An array of field keys. Omitting this value will return ALL
   *   fields.
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function fields($fields = array()) {

    if (!empty($fields)) {
      foreach ($fields as $row_key => $row_value) {
        if (is_bool($row_value)) {
          $this->addField($row_key);
        }
        elseif (is_string($row_value)) {
          $this->addField($row_value);
        }
      }
    }
    else {
      $this->fields = NULL;
    }

    return $this;
  }

  /**
   * Add a specific field.
   *
   * @param string $field_key
   *   Name of the field to add. Nested fields can be added using the . (dot)
   *   syntax.
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function addField($field_key) {
    $this->fields[$field_key] = TRUE;

    return $this;
  }

  /**
   * Get the value for AsArray.
   *
   * @return bool
   *   The value of AsArray.
   */
  public function getAsArray() {

    if (isset($this->asArray)) {
      return $this->asArray;
    }

    return FALSE;
  }

  /**
   * Get the set fields for this query.
   *
   * @return array
   *   An array of fields.
   */
  public function getFields() {
    if (isset($this->fields) && is_array($this->fields)) {
      return $this->fields;
    }

    return array();
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
      if (array_key_exists('_id', $data)) {
        throw new VultanDataException('Mongo IDs can not be used in modifier operations');
      }
    }

    return array('$' . $modifier => $data);
  }

  /**
   * Pre-execution step.
   */
  public function preExecute() {

  }

  /**
   * Post-execution step.
   */
  public function postExecute() {

  }

  /**
   * Post process some results.
   *
   * @param mixed $results
   *   The original results.
   *
   * @return \Vultan\Vultan\Result
   *   The Result object.
   */
  public function postProcess($results) {

    return $results;
  }

  /**
   * Process a failed result with a message.
   *
   * @param string $message
   *   A message.
   * @param string $operation
   *   The operation.
   * @param mixed $result
   *   The result returned by the write operation.
   * @param DocumentInterface $document
   *   (Optional) The data passed to the write operation.
   *
   * @return Result
   *   A result object.
   */
  public function processFailedResult($message, $operation, $result, DocumentInterface $document = NULL) {
    $result = $this->processWriteResult();
    $result->message = $message;
    $result->setSuccess(FALSE);

    return $result;
  }

  /**
   * Prepare the query.
   *
   * @param mixed $data
   *   The data.
   * @param array $filter
   *   Any filter values to pass to mongo.
   * @param array $options
   *   Any options to pass to Mongo.
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function query($data, array $filter = array(), array $options = array()) {

    if (!empty($options)) {
      foreach ($options as $key => $value) {
        $this->setOption($key, $value);
      }
    }

    $this->filter = $filter;

    // Ensure we have a properly prepared Document.
    $this->document = DocumentFactory::init($this->getConfig())
      ->prepareDocument($data);

    return $this;
  }

  /**
   * Return results as an array rather than a MongoCursor.
   *
   * This uses iterator_to_array(), which forces the driver to load all of the
   * results into memory, so do not do this for result sets that are larger
   * than memory!
   *
   * Additionally, calling resultsAsArray() forces Mongo to run the query, so
   * it is not possible to alter the cursor once executed.
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function resultsAsArray() {

    $this->asArray = TRUE;

    return $this;
  }

  /**
   * Execute the prepared query.
   *
   * @return mixed
   *   Result of the query.
   */
  abstract public function runQuery();

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
   * Set the value for LastCursor.
   *
   * @param \MongoCursor $cursor
   *   The value to set.
   */
  public function setLastCursor(\MongoCursor $cursor) {

    $this->lastCursor = $cursor;
  }

  /**
   * Set an option to pass to Mongo.
   *
   * @param string $key
   *   Key of the option.
   * @param mixed $value
   *   The value.
   *
   * @return QueryInterface
   *   The Query object.
   */
  public function setOption($key, $value) {

    $this->options[$key] = $value;

    return $this;
  }

  /**
   * Set the write concern.
   *
   * @param string|int $write_concern
   *   Write concern to use. Any valid Write Concern will work. Common ones are:
   *   - Database::WRITE_SAFE: The default. Acknowledge the write and return a
   *     result.
   *   - Database:WRITE_UNSAFE: Optional. Use only for "unimportant data", such
   *     as click tracking et al.
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function setWriteConcern($write_concern) {

    $this->setOption('w', $write_concern);

    return $this;
  }

  /**
   * Do any necessary query setup.
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function setup() {

    return $this;
  }

  /**
   * Process the result of a write operation.
   *
   * @return \Vultan\Vultan\Result
   *   The Result object.
   */
  protected function processWriteResult() {

    $this->lastResult = new Result();
    $this->getLastResult()->setOperation(static::QUERY_TYPE);

    // A typical find operation should return a MongoCursor.
    if (get_class($this->result) == 'MongoCursor') {
      $this->getLastResult()->setSuccess(TRUE);
      if ($this->getAsArray() == TRUE) {
        $this->getLastResult()->setResult(iterator_to_array($this->result));
      }
      else {
        $this->getLastResult()->setResult($this->result);
      }
    }
    elseif (is_bool($this->result)) {
      $this->getLastResult()->setSuccess($this->result);
      $this->getLastResult()->setResult($this->result);
    }

    if (!empty($this->document)) {
      if (is_object($this->document) && $this->document instanceof DocumentInterface) {
        $id = $this->document->getId();
      }
      else {
        isset($this->document['id']) ? $id = $this->document['id'] : $id = NULL;
      }
      $this->getLastResult()->setId($id);
      $this->getLastResult()->setDocument($this->document);
    }

    return $this->getLastResult();
  }
}
