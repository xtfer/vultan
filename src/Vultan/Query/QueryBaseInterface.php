<?php

/**
 * @file
 * Contains a QueryBaseInterface
 */

namespace Vultan\Query;

use Vultan\Config;
use Vultan\Query\Types\Find;
use Vultan\Vultan\Collection;

/**
 * Interface QueryBaseInterface
 *
 * @package Vultan\BaseQuery
 */
interface QueryBaseInterface {

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
  static public function create(Config $config, Collection $collection);

  /**
   * Add a filter condition.
   *
   * @param string $key
   *   Name of the field to return. Nested fields can be seperated with a .
   *   (full stop), as per the MongoDB query syntax.
   * @param string $value
   *   Value to match on.
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function addCondition($key, $value);

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
  public function addField($field_key);

  /**
   * Shortcut to set up a filter for filtering by ID.
   *
   * @todo: Dynamic filtering.
   *
   * @param string $identifier
   *   An identifier.
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function addFilterMongoID($identifier);

  /**
   * A special filter to reduce to distinct values.
   *
   * @param string $key
   *   Key to return distinct values for.
   */
  public function distinct($key);

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
  public function fields($fields = array());

  /**
   * Get the value for AsArray.
   *
   * @return bool
   *   The value of AsArray.
   */
  public function getAsArray();

  /**
   * Get the set fields for this query.
   *
   * @return array
   *   An array of fields.
   */
  public function getFields();

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
  public function query($data, array $filter = array(), array $options = array());

  /**
   * Get the value for LastCursor.
   *
   * @return \MongoCursor
   *   The value of LastCursor.
   */
  public function getLastCursor();

  /**
   * Get the value for LastResult.
   *
   * @return \Vultan\Vultan\Result
   *   The value of LastResult.
   */
  public function getLastResult();

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
  public function resultsAsArray();

  /**
   * Set the value for LastCursor.
   *
   * @param \MongoCursor $cursor
   *   The value to set.
   */
  public function setLastCursor(\MongoCursor $cursor);

  /**
   * Set an option to pass to Mongo.
   *
   * @param string $key
   *   Key of the option.
   * @param mixed $value
   *   The value.
   *
   * @return QueryInterface
   *   This Query object.
   */
  public function setOption($key, $value);

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
  public function setWriteConcern($write_concern);
}
