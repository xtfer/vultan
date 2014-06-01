<?php

/**
 * @file
 * Contains an Find BaseQuery
 */

namespace Vultan\Query\Types;

use Vultan\Query\BaseQuery;
use Vultan\Query\QueryImplementationInterface;

/**
 * Class Find
 *
 * @package Vultan\BaseQuery
 */
class Find extends BaseQuery implements QueryImplementationInterface {

  /**
   * Define the BaseQuery Type.
   */
  const QUERY_TYPE = 'find';

  /**
   * {@inheritdoc}
   */
  public function setup() {

    // We default to WRITE_UNSAFE.
    $this->setWriteConcern(static::WRITE_UNSAFE);

    // Find single - this is the default behaviour, however the Mongo docs
    // suggest that this should be set explicitly for future-proofing
    $this->setOption('multiple', FALSE);

  }

  /**
   * {@inheritdoc}
   */
  public function runQuery() {

    if (isset($this->distinct) && !empty($this->distinct)) {
      $this->result = $this->collection->getCollection()
        ->distinct($this->distinct, $this->filter);
    }
    else {
      $this->result = $this->collection
        ->find($this->filter, $this->getFields());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function postProcess($results) {

    return $this->getLastResult()->getResult();
  }

}
