<?php

/**
 * @file
 * Contains a MongoIDHelper
 */

namespace Vultan\Traits;

/**
 * Class MongoIDHelper
 *
 * @package Vultan\Traits
 */
trait MongoIDHelper {

  /**
   * Create a new MongoId object.
   *
   * @param string|null $identifier
   *   Identifier to use. Must be 24 hexidecimal characters. If an invalid
   *   string is passed to this constructor, the constructor will ignore it
   *   and create a new id value.
   *
   * @return \MongoId
   *   A MongoId object.
   */
  public function createMongoIdentifier($identifier = NULL) {

    $mid = new \MongoId($identifier);

    return $mid;
  }

  /**
   * Given a MongoID or data, return the ID number.
   *
   * @param array $data
   *   An array containing a MongoID.
   *
   * @return string|bool
   *   A string ID, or FALSE.
   */
  public function extractID($data) {

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
