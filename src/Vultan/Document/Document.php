<?php
/**
 * @file
 * A base class for Items to be managed in and out of the Vultan system.
 */

namespace Vultan\Document;

use Vultan\Config\Config;
use Vultan\Vultan\Database;
use Vultan\VultanBuilder;
use Vultan\Traits\ConfigTrait;

/**
 * Class Document
 *
 * Base class for a Document
 *
 * @package Vultan\Document
 */
class Document implements DocumentInterface {

  use ConfigTrait;

  /**
   * Mongo ID
   *
   * @var string
   */
  protected $id;

  /**
   * Properties to be written to the database
   *
   * @var array
   */
  protected $properties;

  /**
   * Collection to use
   *
   * @var string
   */
  protected $collection;

  /**
   * The active database connection
   *
   * @var \Vultan\Vultan\Database
   */
  protected $database;

  /**
   * Constructor.
   */
  public function __construct(Config $config, array $data = array()) {

    $this->setConfig($config);

    if (!empty($data)) {
      foreach ($data as $key => $value) {

        if ($key == '_id') {
          $this->setIdentifier($value);
          continue;
        }

        $this->set($key, $value);
      }
    }
  }

  /**
   * Static factory method.
   *
   * @param \Vultan\Config\Config $config
   *   A Vultan Configuration object
   * @param array $data
   *   (Optional) An array containing any object properties to set.
   *
   * @return \Vultan\Document\DocumentInterface
   *   The Document object.
   */
  static public function create(Config $config, array $data = array()) {

    return new static($config, $data);
  }

  /**
   * Set id.
   *
   * This will check whether the provided ID is a MongoId object before setting
   * the value.
   *
   * @param string|\MongoId $identifier
   *   A suitable MongoDB identifier.
   *
   * @return \Vultan\Document\DocumentInterface
   *   The Document object.
   */
  public function setIdentifier($identifier) {
    if (is_object($identifier) && get_class($identifier) == 'MongoId') {
      $this->id = $identifier->id;
    }
    else {
      $this->id = $identifier;
    }

    return $this;
  }

  /**
   * Get the value for Id.
   *
   * @return string
   *   The value of Id.
   */
  public function getId() {

    return $this->id;
  }

  /**
   * Set a property.
   *
   * @param string $key
   *   The property key.
   * @param mixed $value
   *   The value.
   *
   * @return \Vultan\Document\DocumentInterface
   *   The Document object.
   */
  public function set($key, $value) {

    $this->properties[$key] = $value;

    return $this;
  }

  /**
   * Retrieve a property.
   *
   * @param string $key
   *   The property to return
   */
  public function get($key) {

    if (isset($this->properties[$key])) {

      return $this->properties[$key];
    }
  }

  /**
   * Unset a property.
   *
   * @param string $key
   *   Property to unset.
   *
   * @return \Vultan\Document\DocumentInterface
   *   The Document object.
   */
  public function remove($key) {

    if (isset($this->properties[$key])) {
      unset($this->properties[$key]);
    }

    return $this;
  }

  /**
   * Save the current item to the database.
   *
   * This will either create or update an existing item, using the Mongo upsert
   * functionality. It also sets time created (if new) and time updated.
   *
   * @return array
   *   Result of the Upsert
   */
  public function save() {

    // Load a database.
    $this->invokeDatabaseConnection();
    $this->getDatabase()->useCollection($this->getCollection());

    // Set default created/updated properties.
    if (!isset($this->properties['time_created'])) {
      $this->set('time_created', time());
    }
    $this->set('time_updated', time());

    if (isset($this->id)) {
      $filter = $this->getDatabase()->filterID($this->id);
    }
    else {
      $filter = array();
    }

    return $this->getDatabase()->upsert($filter, $this->getValues());
  }

  /**
   * Access a Vultan DB connection.
   */
  public function invokeDatabaseConnection() {

    $database = VultanBuilder::initAndConnect($this->getConfig())->getDatabase();
    $this->setDatabase($database);

    return $this;
  }

  /**
   * Set the value for Database.
   *
   * @param \Vultan\Vultan\Database $database
   *   The value to set.
   */
  public function setDatabase(Database $database) {

    $this->database = $database;
  }

  /**
   * Return the Database.
   *
   * @return Database
   *   A Database object.
   */
  public function getDatabase() {

    return $this->database;
  }

  /**
   * Set the collection to use for this item.
   *
   * A value can be passed to this function to set the active collection, or
   * the method can be overridden and simply return a default.
   *
   * @param string $collection_name
   *   Set the collection to use
   *
   * @return DocumentInterface
   *   This Document, for chaining.
   */
  public function setCollection($collection_name) {

    $this->collection = $collection_name;

    return $this;
  }

  /**
   * Returns the current collection name.
   *
   * @return string|bool
   *   Either the name of the collection, or FALSE.
   */
  public function getCollection() {

    if (!empty($this->collection)) {

      return $this->collection;
    }

    return FALSE;
  }

  /**
   * Return an objects properties as an array for inserting into Mongo.
   *
   * @return array
   *   An array of properties.
   */
  public function getValues() {

    return $this->properties;
  }
}
