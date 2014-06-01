<?php

/**
 * @file
 * Contains an Remove BaseQuery
 */

namespace Vultan\Query\Types;

use Vultan\Query\BaseQuery;
use Vultan\Query\QueryImplementationInterface;

/**
 * Class Remove
 *
 * @package Vultan\BaseQuery
 */
class Remove extends WriteQuery implements QueryImplementationInterface {

  /**
   * Define the BaseQuery Type.
   */
  const QUERY_TYPE = 'remove';

  /**
   * {@inheritdoc}
   */
  public function setup() {

    // We default to WRITE_UNSAFE.
    $this->setWriteConcern(static::WRITE_UNSAFE);

    // Remove single - this is the default behaviour, however the Mongo docs
    // suggest that this should be set explicitly for future-proofing
    $this->setOption('multiple', FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function runQuery() {

    $this->result = $this->collection
      ->remove($this->filter, $this->options);
  }

}
