<?php

/**
 * @file
 * Contains a WriteQuery
 */

namespace Vultan\Query\Types;

use Vultan\Query\BaseQuery;
use Vultan\Vultan\Result;

/**
 * Class WriteQuery
 *
 * @package Vultan\Query\Types
 */
abstract class WriteQuery extends BaseQuery {

  /**
   * Post-execution step.
   */
  public function postExecute() {

    $this->document->setIdentifier($this->extractID($this->document->getProperties()));
  }

  /**
   * Process the result of a write operation.
   *
   * @return \Vultan\Vultan\Result
   *   The Result object.
   */
  protected function processWriteResult() {

    $this->lastResult = new Result();

    $this->getLastResult()->setOperation(static::QUERY_TYPE);

    // Results as array indicate the operation was processed with a writeConcern
    // == TRUE, and the operation is in safe mode.
    if (is_array($this->result)) {

      $this->getLastResult()->setResult($this->result);

      if (is_null($this->result['err'])) {
        $this->getLastResult()->setSuccess(TRUE);
      }
      else {
        $this->getLastResult()->setError($this->result['err']);
        $this->getLastResult()->setSuccess(FALSE);
      }
    }
    elseif (is_bool($this->result)) {
      $this->getLastResult()->setSuccess($this->result);
    }

    if (!empty($this->document)) {
      $this->getLastResult()->setId($this->document->getId());
      $this->getLastResult()->setDocument($this->document);
    }

    return $this->getLastResult();
  }
}
