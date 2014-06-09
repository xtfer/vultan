<?php
/**
 * @file
 * The database connection and tools.
 *
 * @copyright Copyright(c) 2013 - 2014 Chris Skene
 * @license GPL v2 http://www.fsf.org/licensing/licenses/gpl.html
 * @author Chris Skene chris at xtfer dot com
 */

namespace Vultan\Vultan;

use Vultan\Config;
use Vultan\Exception\VultanException;
use Vultan\Traits\ConfigTrait;
use MongoDB;
use MongoCollection;

/**
 * The Database connection and tools
 */
class Database {

  use ConfigTrait;

  /**
   * The currently active collection
   *
   * @var Collection
   */
  public $collection;

  /**
   * The MongoDB object
   *
   * @var MongoDB
   */
  protected $mongoDB;

  /**
   * Constructor function.
   *
   * @param Config $config
   *   The Config object.
   * @param \MongoDB $mongo_db
   *   A MongoDB database.
   */
  public function __construct(Config $config, MongoDB $mongo_db) {
    $this->config = $config;
    $this->mongoDB = $mongo_db;

    // This is not necessary UNLESS this is a new database, in which case
    // selectDB() won't actually create the database.
    // @see http://stackoverflow.com/questions/4508529/
    // create-a-mongodb-database-with-php
    $this->mongoDB->listCollections();
  }

  /**
   * Main constructor.
   *
   * @param Config $config
   *   The Config object.
   * @param \MongoDB $mongo_db
   *   A MongoDB database.
   *
   * @return Database
   *   This Database object.
   */
  static public function init(Config $config, MongoDB $mongo_db) {

    return new static($config, $mongo_db);
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
   * Set the database.
   *
   * @param MongoDB $database
   *   A MongoDB database.
   */
  public function setDataSource(MongoDB $database) {

    $this->mongoDB = $database;
  }

  /**
   * Returns a collection object from a db.
   *
   * @param string $collection_name
   *   The collection name.
   *
   * @throws VultanException
   * @return Collection
   *   A Collection object
   */
  public function useCollection($collection_name) {

    if (empty($collection_name)) {
      throw new VultanException('Could not select collection: No collection name provided.');
    }

    $database = $this->getDataSource();
    if (empty($database)|| !$database instanceof MongoDB) {
      throw new VultanException('Could not select collection: No database loaded.');
    }

    try {
      $this->collection = new Collection($this->config, $database->selectCollection($collection_name));
    }
    catch (\Exception $e) {
      throw new VultanException('Could not select collection: ' . $e->getMessage());
    }

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

}
