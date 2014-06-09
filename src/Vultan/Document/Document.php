<?php
/**
 * @file
 * A base class for Items to be managed in and out of the Vultan system.
 */

namespace Vultan\Document;

use Vultan\Config;
use Vultan\Exception\VultanException;
use Vultan\Query\BaseQuery;
use Vultan\Vultan\Database;
use Vultan\Traits\ConfigTrait;
use Vultan\Vultan;

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
  protected $identifier;

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
   * The vultan variable.
   *
   * @var Vultan
   */
  protected $vultan;

  /**
   * Constructor.
   *
   * @see setProperties()
   *
   * @param Config $config
   *   A Vultan Config object.
   * @param array $data
   *   (Optional) Any data to set. If this is existing Mongo data, it should
   *   have an '_id' key containing the MongoID object or value.
   *
   * @return \Vultan\Document\Document
   *   This document.
   */
  public function __construct(Config $config, array $data = array()) {

    $this->setConfig($config);

    $this->identifier = NULL;

    if (!empty($data)) {
      $this->setProperties($data);
    }

    return $this;
  }

  /**
   * Static factory method.
   *
   * @param \Vultan\Config $config
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
   * Set the identifier.
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
      $this->identifier = (string) $identifier;
    }
    else {
      $this->identifier = $identifier;
    }

    return $this;
  }

  /**
   * Get the value for the identifier.
   *
   * @return string
   *   The value of Id.
   */
  public function getId() {

    if (isset($this->identifier)) {

      return $this->identifier;
    }

    return NULL;
  }

  /**
   * Set a property.
   *
   * If the following properties are provided, they will be extracted and set
   * on the document:
   *  '_id': will be converted to a Document ID
   *  'collection': will be used as the document collection.
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

    if ($key == '_id') {
      $this->setIdentifier($value);
    }
    elseif ($key == 'collection') {
      $this->setCollection($value);
    }
    else {
      $this->properties[$key] = $value;
    }

    return $this;
  }

  /**
   * Link a document to another document or Mongo data item.
   *
   * @param string $key
   *   The property key for this item.
   * @param string|\MongoID|DocumentInterface $document
   *   The item to link. Cam be either the ID of the Mongo object, a MongoID, or
   *   a Document.
   *
   * @throws \Vultan\Exception\VultanException
   *
   * @return \Vultan\Document\DocumentInterface
   *   The Document object.
   */
  public function link($key, $document) {

    $link = new Link($key, $document);
    $this->set($key, $link);

    return $this;
  }

  /**
   * Retrieve a property.
   *
   * @param string $key
   *   The property to return
   *
   * @return string
   *   Value of the key.
   */
  public function get($key) {

    if ($key == '_id') {
      return $this->getId();
    }

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

    if ($key == '_id') {
      unset($this->identifier);
    }

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
   * @param string|int $safe
   *   Whether to conduct a safe upsert or not.
   *   - Database::WRITE_SAFE: Safe. Returns status (default)
   *   - Database::WRITE_UNSAFE: Not safe. Does not return status.
   *
   * @return array
   *   Result of the Upsert
   */
  public function save($safe = BaseQuery::WRITE_UNSAFE) {

    // Load a database.
    $this->invokeDatabaseConnection();
    $this->vultan->useCollection($this->collection);

    // Set defaults.
    $this->setDefaultProperties();

    $identifier = $this->getId();
    if (!empty($identifier)) {

      return $this->vultan->upsert($this, array(), $safe);
    }
    else {

      return $this->vultan->insert($this, $safe);
    }

  }

  /**
   * Access a Vultan DB connection.
   *
   * @return \Vultan\Document\DocumentInterface
   *   A Vultan Document.
   */
  public function invokeDatabaseConnection() {

    $this->vultan = Vultan::init($this->config);

    return $this;
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
   * Get the value for Properties.
   *
   * @return array
   *   The value of Properties.
   */
  public function getProperties() {

    $values = $this->properties;

    $this->cleanIdentitifer();
    if (isset($this->identifier)) {
      $values['_id'] = $this->getId();
    }

    return $values;
  }

  /**
   * Set the value for Properties.
   *
   * @param array $data
   *   The values to set.
   *
   * @return Document
   *   This class, for chaining.
   */
  public function setProperties(array $data) {

    if (!empty($data)) {
      foreach ($data as $key => $value) {

        $this->set($key, $value);
      }
    }

    return $this;
  }

  /**
   * Set default properties.
   */
  public function setDefaultProperties() {

    // Set default created/updated properties.
    if (!isset($this->properties['time_created'])) {
      $this->set('time_created', time());
    }
    $this->set('time_updated', time());
  }

  /**
   * Clean the identifier key.
   *
   * We never want to return an empty identifier.
   */
  public function cleanIdentitifer() {

    if (isset($this->identifier) && empty($this->identifier)) {
      unset($this->identifier);
    }
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
  static public function extractID($data) {

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
}
