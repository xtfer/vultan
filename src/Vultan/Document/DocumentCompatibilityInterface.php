<?php

/**
 * @file
 * Contains a DocumentCompatibilityInterface
 */

namespace Vultan\Document;

/**
 * Interface DocumentCompatibilityInterface
 *
 * @package Vultan\Document
 */
interface DocumentCompatibilityInterface {

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
}
