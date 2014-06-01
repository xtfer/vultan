<?php

/**
 * @file
 * Contains a Collection.
 */

namespace Vultan\Vultan;

use Vultan\Config;
use Vultan\Exception\VultanIndexException;
use Vultan\Traits\ConfigTrait;

/**
 * Class Collection
 *
 * @package Vultan\Vultan
 */
class Collection {

  use ConfigTrait;

  /**
   * The collection variable.
   *
   * @var \MongoCollection
   */
  protected $collection;

  /**
   * Constructor.
   *
   * @param \Vultan\Config $config
   *   A Config object.
   * @param \MongoCollection $collection
   *   A MongoCollection
   */
  public function __construct(Config $config, \MongoCollection $collection) {

    $this->config = $config;
    $this->collection = $collection;
  }

  /**
   * Insert.
   *
   * @param array $data
   *   The data to insert.
   * @param array $options
   *   (Optional) Any options to pass.
   *
   * @return array|bool
   *   The result.
   */
  public function insert(array $data, array $options = array()) {

    return $this->collection->insert($data, $options);
  }

  /**
   * Update.
   *
   * @param array $data
   *   The data to insert.
   * @param array $filter
   *   (optional) The filter to use.
   * @param array $options
   *   (Optional) Any options to pass.
   *
   * @return bool
   *   The result.
   */
  public function update(array $data, array $filter = array(), array $options = array()) {

    return $this->collection->update($filter, $data, $options);
  }

  /**
   * Delete.
   *
   * @param array $filter
   *   (optional) The filter to use.
   * @param array $options
   *   (Optional) Any options to pass.
   *
   * @return mixed
   *   Result of the delete.
   */
  public function remove(array $filter = array(), array $options = array()) {

    return $this->collection->remove($filter, $options);
  }

  /**
   * Delete.
   *
   * @param array $query
   *   (optional) The filter to use.
   * @param array $fields
   *   (Optional) Any field options to pass.
   *
   * @return mixed
   *   Result of the delete.
   */
  public function find(array $query = array(), array $fields = array()) {

    return $this->collection->find($query, $fields);
  }

  /**
   * Get the value for Collection.
   *
   * @return \MongoCollection
   *   The value of Collection.
   */
  public function getCollection() {

    return $this->collection;
  }

  /**
   * Create an index.
   *
   * Effectively, just a wrapper around the collections own ensureIndex method.
   *
   * @param array $keys
   *   An array specifying the index's fields as its keys. For each field, the
   *   value is either the index direction or » index type. If specifying
   *   direction, specify 1 for ascending or -1 for descending.
   * @param array $options
   *   (Optional) An array of options for the index creation.
   *
   * @throws \Vultan\Exception\VultanIndexException
   * @see http://www.php.net/manual/en/mongocollection.createindex.php
   */
  public function createIndex(array $keys, array $options = array()) {

    try {
      $this->collection->ensureIndex($keys, $options);
    }
    catch (\MongoException $e) {
      throw new VultanIndexException('Unable to create index: ' . $e->getMessage());
    }
  }
}
