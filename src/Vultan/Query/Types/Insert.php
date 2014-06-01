<?php

/**
 * @file
 * Contains an InsertQuery
 */

namespace Vultan\Query\Types;

use Vultan\Query\BaseQuery;
use Vultan\Query\QueryImplementationInterface;

/**
 * Class Insert
 *
 * @package Vultan\BaseQuery
 */
class Insert extends WriteQuery implements QueryImplementationInterface {

  /**
   * Define the BaseQuery Type.
   */
  const QUERY_TYPE = 'insert';

  /**
   * {@inheritdoc}
   */
  public function setup() {

    // We default to WRITE_UNSAFE.
    $this->setWriteConcern(static::WRITE_UNSAFE);
  }

  /**
   * {@inheritdoc}
   */
  public function runQuery() {

    $this->result = $this->collection
      ->insert($this->document->getProperties(), $this->options);
  }

}
