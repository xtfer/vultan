<?php

/**
 * @file
 * Contains an Update BaseQuery
 */

namespace Vultan\Query\Types;

use Vultan\Query\BaseQuery;
use Vultan\Query\QueryImplementationInterface;

/**
 * Class Update
 *
 * @package Vultan\BaseQuery
 */
class Update extends WriteQuery implements QueryImplementationInterface {

  /**
   * Define the BaseQuery Type.
   */
  const QUERY_TYPE = 'update';

  /**
   * {@inheritdoc}
   */
  public function setup() {

    // We default to WRITE_UNSAFE.
    $this->setWriteConcern(static::WRITE_UNSAFE);

    // Update single - this is the default behaviour, however the Mongo docs
    // suggest that this should be set explicitly for future-proofing
    $this->setOption('multiple', FALSE);

  }

  /**
   * {@inheritdoc}
   */
  public function runQuery() {

    $data = $this->document->getProperties();

    // Fix partial keys.
    // @todo: Should occur in a pre-execute step.
    if (isset($this->options['partial']) && $this->options['partial'] == TRUE) {
      // Add a 'set' modifier.
      if (array_key_exists('_id', $data)) {
        unset($data['_id']);
      }
      $data = $this->mod('set', $data);
    }

    $this->result = $this->collection
      ->update($data, $this->filter, $this->options);
  }

  /**
   * {@inheritdoc}
   */
  public function postExecute() {

    // Can we get the updated record?
    $this->document = $this->collection->getCollection()->findOne();
  }
}
