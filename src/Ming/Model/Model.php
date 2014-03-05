<?php
/**
 * @file
 * Contains a DocumentModel class.
 */

namespace Ming\Model;

/**
 * Class DocumentModel
 *
 * @package Ming\Document
 */
class Model implements ModelInterface {

  /**
   * name
   *
   * @var string
   */
  protected $name;

  /**
   * class
   *
   * @var string
   */
  protected $class;

  /**
   * fields
   *
   * @var array
   */
  protected $fields;

  /**
   * collection
   *
   * @var string
   */
  protected $collection;

  /**
   * indexes
   *
   * @var array
   */
  protected $indexes = array();

  /**
   * options
   *
   * @var array
   */
  protected $options;

  /**
   * Set the value for Class.
   *
   * @param string $class
   *   The value to set.
   *
   * @return Model
   *   This, for chaining.
   */
  public function setClass($class) {

    $this->class = $class;

    return $this;
  }

  /**
   * Get the value for Class.
   *
   * @return string
   *   The value of Class.
   */
  public function getClass() {

    if (!isset($this->class)) {

      return '\\Ming\\Document\\ModelledDocument';
    }

    return $this->class;
  }

  /**
   * Set the value for Fields.
   *
   * @param string $field_name
   *   The field name.
   * @param array $settings
   *   (Optional) Settings for the field. The field may have no explicit
   *   settings, which is fine.
   *
   * @return Model
   *   This, for chaining.
   */
  public function setField($field_name, $settings = array()) {

    $this->fields[$field_name] = $settings;

    return $this;
  }

  /**
   * Get the value for Fields.
   *
   * @param string $field_name
   *   The field name.
   *
   * @return array
   *   The value of Fields.
   */
  public function getField($field_name) {

    if (isset($this->fields[$field_name])) {
      return $this->fields[$field_name];
    }

    return array();
  }

  /**
   * Set the value for Name.
   *
   * @param string $name
   *   The value to set.
   *
   * @return Model
   *   This, for chaining.
   */
  public function setName($name) {

    $this->name = $name;

    return $this;
  }

  /**
   * Get the value for Name.
   *
   * @return string
   *   The value of Name.
   */
  public function getName() {

    return $this->name;
  }

  /**
   * Set the value for Fields.
   *
   * @param array $fields
   *   The value to set.
   *
   * @return Model
   *   This, for chaining.
   */
  public function setFields($fields) {

    $this->fields = $fields;

    return $this;
  }

  /**
   * Get the value for Fields.
   *
   * @return array
   *   The value of Fields.
   */
  public function getFields() {

    return $this->fields;
  }

  /**
   * Set the value for Collection.
   *
   * @param string $collection
   *   The value to set.
   *
   * @return Model
   *   This, for chaining.
   */
  public function setCollection($collection) {

    $this->collection = $collection;

    return $this;
  }

  /**
   * Get the value for Collection.
   *
   * @return string
   *   The value of Collection.
   */
  public function getCollection() {

    return $this->collection;
  }

  /**
   * Adds an index.
   *
   * @todo Not currently implemented.
   *
   * Indexes are only added when a Collection is created for the first time.
   *
   * @param string|array $index_fields
   *   The values to set.
   *
   * @return Model
   *   This, for chaining.
   */
  public function addIndex($index_fields) {

    if (is_string($index_fields)) {
      $index_fields = array($index_fields);
    }

    foreach ($index_fields as $field_name) {
      $this->indexes[$field_name] = TRUE;
    }

    return $this;
  }

  /**
   * Index all fields.
   *
   * @return Model
   *   This, for chaining.
   */
  public function useCoveredIndex() {

    $this->setOption('use_covered_index', TRUE);

    return $this;
  }

  /**
   * Remove an index.
   *
   * @param string $index_field
   *   The value to remove.
   *
   * @return Model
   *   This, for chaining.
   */
  public function removeIndex($index_field) {

    unset($this->indexes[$index_field]);

    return $this;
  }

  /**
   * Get the value for Indexes.
   *
   * @return array
   *   The value of Indexes.
   */
  public function getIndexes() {

    return $this->indexes;
  }

  /**
   * Set an option value.
   *
   * @param string $key
   *   The key.
   * @param mixed $value
   *   The value to set
   *
   * @return Model
   *   This, for chaining.
   */
  public function setOption($key, $value) {

    $this->options[$key] = $value;

    return $this;
  }

  /**
   * Get an option value.
   *
   * @param string $key
   *   The key.
   * @param mixed|null $default
   *   A default value, if the existing value is not found.
   *
   * @return mixed|null
   *   The result.
   */
  public function getOption($key, $default = NULL) {

    if (isset($this->options[$key])) {
      return $this->options[$key];
    }

    return $default;
  }
}
