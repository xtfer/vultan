<?php
/**
 * @file
 * Contains a DocumentInterface.
 */

namespace Ming\Document;

use Ming\Config;
use Ming\Ming\Database;

/**
 * Interface DocumentInterface
 *
 * @package Ming\Document
 */
interface DocumentInterface {

  /**
   * Static factory method.
   *
   * @param \Ming\Config $config
   *   A Ming Configuration object
   * @param array $data
   *   (Optional) An array containing any object properties to set.
   *
   * @return \Ming\Document\DocumentInterface
   *   The Document object.
   */
  public static function create(Config $config, array $data = array());

  /**
   * Retrieve a property.
   *
   * @param string $key
   *   The property to return
   */
  public function get($key);

  /**
   * Returns the current collection name.
   *
   * @return string|bool
   *   Either the name of the collection, or FALSE.
   */
  public function getCollection();

  /**
   * Return the Database.
   *
   * @return Database
   *   A Database object.
   */
  public function getDatabase();

  /**
   * Get the value for Id.
   *
   * @return string
   *   The value of Id.
   */
  public function getId();

  /**
   * Return an objects properties as an array for inserting into Mongo.
   *
   * @return array
   *   An array of properties.
   */
  public function getValues();

  /**
   * Unset a property.
   *
   * @param string $key
   *   Property to unset.
   *
   * @return \Ming\Document\DocumentInterface
   *   The Document object.
   */
  public function remove($key);

  /**
   * Save the current item to the database.
   *
   * This will either create or update an existing item, using the Mongo upsert
   * functionality. It also sets time created (if new) and time updated.
   *
   * @return array
   *   Result of the Upsert
   */
  public function save();

  /**
   * Set a property.
   *
   * @param string $key
   *   The property key.
   * @param mixed $value
   *   The value.
   *
   * @return \Ming\Document\DocumentInterface
   *   The Document object.
   */
  public function set($key, $value);

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
  public function setCollection($collection_name);

  /**
   * Set id.
   *
   * This will check whether the provided ID is a MongoId object before setting
   * the value.
   *
   * @param string|\MongoId $identifier
   *   A suitable MongoDB identifier.
   *
   * @return \Ming\Document\DocumentInterface
   *   The Document object.
   */
  public function setIdentifier($identifier);
}
