<?php
/**
 * @file
 * Provides a Broker for Vultan.
 *
 * @copyright Copyright(c) 2013 - 2014 Chris Skene
 * @license GPL v2 http://www.fsf.org/licensing/licenses/gpl.html
 * @author Chris Skene chris at xtfer dot com
 */

namespace Vultan;

use Vultan\Document\DocumentFactory;
use Vultan\Document\DocumentInterface;
use Vultan\Query\QueryInterface;
use Vultan\Query\Types\Find;
use Vultan\Query\Types\Insert;
use Vultan\Query\BaseQuery;
use Vultan\Query\Types\Remove;
use Vultan\Query\Types\Update;
use Vultan\Traits\MongoIDHelper;
use Vultan\Vultan\Collection;
use Vultan\Vultan\Connection;
use Vultan\Vultan\Database;
use Vultan\Traits\ConfigTrait;

/**
 * Class Vultan
 *
 * @package Vultan\Vultan
 */
class Vultan {

  use ConfigTrait;
  use MongoIDHelper;

  /**
   * The Connection.
   *
   * @var Connection
   */
  protected $connection;

  /**
   * The Database.
   *
   * @var Database
   */
  protected $database;

  /**
   * The collection variable.
   *
   * @var Collection
   */
  protected $collection;

  /**
   * Constructor.
   *
   * @param Config $config
   *   A Vultan Config object.
   * @param Connection $connection
   *   A Vultan Connection object.
   */
  public function __construct(Config $config, Connection $connection) {

    $this->connection = $connection;
    $this->config = $config;
  }

  /**
   * Static factory method.
   *
   * @param Config $config
   *   A Vultan Config object.
   *
   * @return \Vultan\Vultan
   *   This controller, for chaining.
   */
  static public function init(Config $config) {

    $connection = Connection::init($config);

    $vultan = new static($config, $connection);

    $vultan->useDatabase($config->getDatabaseName());

    return $vultan;
  }

  /**
   * Create a new connection.
   *
   * This method automatically handles creation of the config object.
   *
   * @param string|null $host
   *   The database host.
   * @param string|null $port
   *   The database port.
   * @param string|null $user
   *   The database username.
   * @param string|null $pass
   *   The database password.
   *
   * @return \Vultan\Vultan
   *   This controller, for chaining.
   */
  static public function connect($host = NULL, $port = NULL, $user = NULL, $pass = NULL) {

    $config = Config::prepare('default', $host, $port, $user, $port);

    $connection = Connection::init($config);

    $vultan = new static($config, $connection);

    $vultan->useDatabase($config->getDatabaseName());

    return $vultan;
  }

  /**
   * Select a database to use.
   *
   * @param string $database_name
   *   Name of the database. This database must be a valid database for the
   *   current connection.
   *
   * @return Vultan
   *   This.
   */
  public function useDatabase($database_name) {

    $mongo_db = $this->connection->useDatabase($database_name);
    $this->database = Database::init($this->config, $mongo_db);

    return $this;
  }

  /**
   * Returns a collection object from a db.
   *
   * @param string $collection_name
   *   The collection name.
   *
   * @return Vultan
   *   This.
   */
  public function useCollection($collection_name) {
    $this->collection = $this->database->useCollection($collection_name);

    return $this;
  }

  /**
   * Get the Vultan Database.
   *
   * @return \Vultan\Vultan\Database
   *   The value of Connection.
   */
  public function getDatabase() {

    return $this->database;
  }

  /**
   * Get the value for Connection.
   *
   * @return \Vultan\Vultan\Connection
   *   The value of Connection.
   */
  public function getConnection() {

    return $this->connection;
  }

  /**
   * Get the collection.
   *
   * @return Collection
   *   The collection.
   */
  public function getCollection() {

    return $this->collection;
  }

  /**
   * Return the Document Factory service.
   *
   * @return DocumentFactory
   *   A DocumentFactory object.
   */
  public function getDocumentFactory() {

    return DocumentFactory::init($this->getConfig());
  }

  /**
   * Delete items matching a filter.
   *
   * @param array $filter
   *   A normal MongoDB filter array
   * @param string|int $write_concern
   *   Write concern to use. Any valid Write Concern will work. Common ones are:
   *   - Database::WRITE_SAFE: The default. Acknowledge the write and return a
   *     result.
   *   - Database:WRITE_UNSAFE: Optional. Use only for "unimportant data", such
   *     as click tracking et al.
   *
   * @see http://www.php.net/manual/en/mongo.writeconcerns.php
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function delete($filter, $write_concern = BaseQuery::WRITE_SAFE) {

    $query = Remove::create($this->config, $this->collection);

    $query->setWriteConcern($write_concern);

    return $query->query(array(), $filter);
  }

  /**
   * Delete an item.
   *
   * @param string $identifier
   *   The ID of the item to delete
   * @param string|int $write_concern
   *   Write concern to use. Any valid Write Concern will work. Common ones are:
   *   - Database::WRITE_SAFE: The default. Acknowledge the write and return a
   *     result.
   *   - Database:WRITE_UNSAFE: Optional. Use only for "unimportant data", such
   *     as click tracking et al.
   *
   * @see http://www.php.net/manual/en/mongo.writeconcerns.php
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function deleteByID($identifier, $write_concern = BaseQuery::WRITE_SAFE) {

    $query = Remove::create($this->config, $this->collection);

    $query->setWriteConcern($write_concern);
    $query->setOption('justOne', TRUE);

    $query->addFilterMongoID($identifier);

    return $query->query(array());
  }

  /**
   * Find all results for a query in an array.
   *
   * @param array $filter
   *   A standard MongoDB filter
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function findAll(array $filter) {

    $query = Find::create($this->config, $this->collection);

    return $query->query(array(), $filter);
  }

  /**
   * BaseQuery the current collection.
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
   * @return QueryInterface
   *   This Query object.
   *
   * @see http://docs.mongodb.org/manual/reference/method/db.collection.find/
   * @see http://www.php.net/manual/en/class.mongocursor.php
   */
  public function find(array $filter = array(), $fields = array()) {

    $query = Find::create($this->config, $this->collection);

    return $query->query($filter, $fields);
  }

  /**
   * BaseQuery by ID.
   *
   * As finding by ID should only return one result, we shortcut any cursor
   * foreach handling and simply translate the result into an array on return.
   *
   * @param string $identifier
   *   An ID of a Mongo document. We convert to a MongoID automatically.
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function findByID($identifier) {

    $query = Find::create($this->config, $this->collection);

    $query->addFilterMongoID($identifier);

    return $query->query(array());
  }

  /**
   * Insert into a collection.
   *
   * @todo: Handle creation of IDs.
   *
   * @param array|object $document
   *   Preferably a Vultan Document, however we also support an array of data,
   *   and other objects public properties will be passed, or objects can
   *   implement the DocumentCompatibilityInterface.
   * @param string|int $write_concern
   *   Write concern to use. Any valid Write Concern will work. Common ones are:
   *   - Database::WRITE_SAFE: The default. Acknowledge the write and return a
   *     result.
   *   - Database:WRITE_UNSAFE: Optional. Use only for "unimportant data", such
   *     as click tracking et al.
   * @param array $options
   *   This parameter is an associative array of the form
   *   array("optionname" => boolean, ...). See the link below for possible
   *   options. Note that 'multiple' is set explicitly to FALSE, however this
   *   can be overridden.
   *
   * @see http://www.php.net/manual/en/mongo.writeconcerns.php
   * @see http://php.net/manual/en/mongocollection.insert.php
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function insert($document, $write_concern = BaseQuery::WRITE_SAFE, $options = array()) {

    $query = Insert::create($this->config, $this->collection);

    /* @var $query \Vultan\Query\Types\Insert */
    $query->setWriteConcern($write_concern);

    return $query->query($document, array(), $options);
  }

  /**
   * Update the first matched item.
   *
   * For updating properties where only one item should exist.
   *
   * Technically, this can also perform upserts by passing the correct option,
   * however Vultan provides a separate method for this, so it can be called
   * explicitly when an upsert is a valid operation.
   *
   * @param object|array $document
   *   Preferably a Vultan Document, however we also support an array of data,
   *   and other objects public properties will be passed, or objects can
   *   implement the DocumentCompatibilityInterface.
   * @param array $filter
   *   An array of keys to match on. e.g. array('marque' => 'Porsche').
   * @param bool $partial
   *   If TRUE, this is a partial update and Vultan will only update the fields
   *   provided in $data. If FALSE, $data completely overwrites the object,
   *   including deleting unset keys.
   * @param array $options
   *   This parameter is an associative array of the form
   *   array("optionname" => boolean, ...). See the link below for possible
   *   options. Note that 'multiple' is set explicitly to FALSE, however this
   *   can be overridden.
   *
   * @see http://www.php.net/manual/en/mongocollection.update.php
   *
   * @return QueryInterface
   *   This Query object.
   *
   * @see \Mongo\Core\Database::insert()
   */
  public function update($document, array $filter = array(), $partial = FALSE, $options = array()) {

    $query = Update::create($this->config, $this->collection);

    $query->setOption('partial', $partial);

    return $query->query($document, $filter, $options);
  }

  /**
   * Update all matched items.
   *
   * Useful for changing properties across a range of items simultaneously.
   *
   * @param object|array $document
   *   Preferably a Vultan Document, however we also support an array of data,
   *   and other objects public properties will be passed, or objects can
   *   implement the DocumentCompatibilityInterface.
   * @param array $filter
   *   An array of keys to match on
   * @param bool $partial
   *   If TRUE, this is a partial update and Vultan will only update the fields
   *   provided in $data. If FALSE, $data completely overwrites the object.
   * @param array $options
   *   Any other options to pass to Mongo
   *
   * @return QueryInterface
   *   This Query object.
   *
   * @see \Mongo\Core\Database::insert()
   */
  public function updateAll($document, array $filter = array(), $partial = FALSE, $options = array()) {

    $query = Update::create($this->config, $this->collection);

    $query->setOption('partial', $partial);
    $query->setOption('multiple', TRUE);

    return $query->query($document, $filter, $options);
  }

  /**
   * Update an individual item by ID.
   *
   * For updating properties on an item when you already know the item's $id
   *
   * @param string $identifier
   *   The ID to update.
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
   * @return QueryInterface
   *   This Query object.
   *
   * @see \Mongo\Core\Database::insert()
   */
  public function updateByID($identifier, $document, $partial = FALSE, $options = array()) {

    $query = Update::create($this->config, $this->collection);

    $query->setOption('partial', $partial);

    $query->addFilterMongoID($identifier);

    return $query->query($document, array(), $options);
  }

  /**
   * Upsert an item.
   *
   * For updating or creating an object if it does not already exist
   *
   * @param DocumentInterface|object|array $document
   *   Preferably a Vultan Document, however we also support an array of data,
   *   and other objects public properties will be passed, or objects can
   *   implement the DocumentCompatibilityInterface.
   * @param array $filter
   *   A normal MongoDB filter array
   * @param int|string $write_concern
   *   Write concern to use. Any valid Write Concern will work. Common ones are:
   *   - Database::WRITE_SAFE: The default. Acknowledge the write and return a
   *     result.
   *   - Database:WRITE_UNSAFE: Optional. Use only for "unimportant data", such
   *     as click tracking et al.
   * @param array $options
   *   Any other options to pass to Mongo
   *
   * @see http://www.php.net/manual/en/mongo.writeconcerns.php
   *
   * @return QueryInterface
   *   This Query object.
   *
   * @see \Mongo\Core\Database::insert()
   */
  public function upsert($document = NULL, $filter = array(), $write_concern = BaseQuery::WRITE_SAFE, $options = array()) {

    $query = Update::create($this->config, $this->collection);

    $query->setOption('upsert', TRUE);
    // @todo: better multiple support.
    $query->setOption('multiple', FALSE);
    $query->setOption('w', $write_concern);

    $mongo_id = $this->extractID($document);
    if (!empty($mongo_id)) {
      $query->addFilterMongoID($mongo_id);
    }

    return $query->query($document, $filter, $options);
  }
}
