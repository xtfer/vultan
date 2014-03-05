<?php
/**
 * @file
 * Contains a Ming Select query class.
 */

namespace Ming\Query;

use Ming\Ming\Database;

/**
 * Class Select.
 *
 * The Select query class is essentially a wrapper around the
 * \ming\Mongo\Database::find() method, which breaks out the Mongo filter
 * array syntax into an interface more closely approximating the Drupal
 * db_select() syntax.
 *
 * @package Drupal\ming\Ming
 */
class Select {

  /**
   * The Ming Database connection.
   *
   * @var Database
   */
  protected $database;

  /**
   * The collection name.
   *
   * @var string
   */
  protected $collection;

  /**
   * The Mongo filter.
   *
   * @var array
   */
  protected $filter = array();

  /**
   * The fields to return.
   *
   * @var array
   */
  protected $fields;

  /**
   * Whether to return results as a MongoCursor or an array.
   *
   * @var bool
   */
  protected $asArray;

  /**
   * The last MongoCursor.
   *
   * @var \MongoCursor
   */
  protected $lastCursor;

  /**
   * Public constructor.
   *
   * @param \Ming\Ming\Database $ming_db
   *   A Ming Database handler.
   *
   * @return \Ming\Query\Select
   *   This Query object.
   */
  public function __construct(Database $ming_db) {
    $this->database = $ming_db;

    return $this;
  }

  /**
   * Static constructor.
   *
   * @param \Ming\Ming\Database $ming_db
   *   A Ming Database handler.
   *
   * @return \Ming\Query\Select
   *   This Query object.
   */
  static public function init(Database $ming_db) {
    return new static($ming_db);
  }

  /**
   * Set the collection name.
   *
   * @param string $name
   *   The collection name.
   *
   * @return \Ming\Query\Select
   *   This Query object.
   */
  public function collection($name) {

    $this->collection = $name;

    return $this;
  }

  /**
   * A filter condition.
   *
   * @param string $key
   *   Name of the field to return. Nested fields can be seperated with a .
   *   (full stop), as per the MongoDB query syntax.
   * @param string $value
   *   Value to match on.
   *
   * @return \Ming\Query\Select
   *   This Query object.
   */
  public function condition($key, $value) {
    $this->filter[$key] = $value;

    return $this;
  }

  /**
   * Specify Fields to retrieve.
   *
   * @param array $fields
   *   (optional) An array of field keys. Omitting this value will return ALL
   *   fields.
   *
   * @return \Ming\Query\Select
   *   This Query object.
   */
  public function fields($fields = array()) {

    if (!empty($fields)) {
      $this->fields = array_merge($this->fields, $fields);
    }
    else {
      $this->fields = NULL;
    }

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
   * @return \Ming\Query\Select
   *   This Query object.
   */
  public function resultsAsArray() {
    $this->asArray = TRUE;

    return $this;
  }



  /**
   * Execute the query.
   *
   * @return bool|\MongoCursor|array
   *   Either FALSE, or a MongoCursor or array.
   */
  public function execute() {

    if (empty($this->fields)) {
      $fields = NULL;
    }
    else {
      $fields = $this->fields;
    }

    try {
      $this->getDatabase()
        ->useCollection($this->collection);

      $result = $this->getDatabase()
        ->find($this->filter, $fields);

      $this->setCursor($result);
    }
    catch(\Exception $e) {

      return FALSE;
    }

    // This returns a nice array, but also prevents manipulating the cursor.
    if ($this->getAsArray() == TRUE) {
      return iterator_to_array($this->getCursor());
    }

    return $this->getCursor();
  }

  /**
   * Get the value for Database.
   *
   * @return \Ming\Ming\Database
   *   The value of Database.
   */
  public function getDatabase() {

    return $this->database;
  }

  /**
   * Get the value for AsArray.
   *
   * @return bool
   *   The value of AsArray.
   */
  protected function getAsArray() {

    if (isset($this->asArray) && $this->asArray == TRUE) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Set the value for LastCursor.
   *
   * @param \MongoCursor $cursor
   *   A MongoCursor.
   */
  public function setCursor(\MongoCursor $cursor) {

    $this->lastCursor = $cursor;
  }

  /**
   * Get the value for LastCursor.
   *
   * @return \MongoCursor
   *   The value of the last MongoCursor.
   */
  public function getCursor() {

    return $this->lastCursor;
  }
}
