<?php
/**
 * @file
 * Contains a DocumentModelInterface
 */

namespace Ming\Model;


/**
 * Class DocumentModelInterface
 *
 * @package Ming\Document
 */
interface ModelInterface {

  /**
   * Get the value for Class.
   *
   * @return string
   *   The value of Class.
   */
  public function getClass();

  /**
   * Get the value for Collection.
   *
   * @return string
   *   The value of Collection.
   */
  public function getCollection();

  /**
   * Get the value for Fields.
   *
   * @param string $field_name
   *   The field name.
   *
   * @return array
   *   The value of Fields.
   */
  public function getField($field_name);

  /**
   * Get the value for Fields.
   *
   * @return array
   *   The value of Fields.
   */
  public function getFields();

  /**
   * Get the value for Name.
   *
   * @return string
   *   The value of Name.
   */
  public function getName();

  /**
   * Set the value for Class.
   *
   * @param string $class
   *   The value to set.
   */
  public function setClass($class);

  /**
   * Set the value for Collection.
   *
   * @param string $collection
   *   The value to set.
   */
  public function setCollection($collection);

  /**
   * Set the value for Fields.
   *
   * @param string $field_name
   *   The field name.
   * @param array $settings
   *   Settings for the field.
   */
  public function setField($field_name, $settings);

  /**
   * Set the value for Fields.
   *
   * @param array $fields
   *   The value to set.
   */
  public function setFields($fields);

  /**
   * Set the value for Name.
   *
   * @param string $name
   *   The value to set.
   */
  public function setName($name);
}
