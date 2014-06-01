<?php
/**
 * @file
 * Contains a Result object.
 */

namespace Vultan\Vultan;

use Vultan\Document\DocumentInterface;
use MongoCursor;

/**
 * Class Result
 *
 * @package Drupal\vultan\Vultan
 */
class Result {

  /**
   * result
   *
   * @var array
   */
  public $results;

  /**
   * success
   *
   * @var bool
   */
  public $success;

  /**
   * id
   *
   * @var string|bool
   */
  public $id;

  /**
   * The Document on which the operation was performed.
   *
   * @var \Vultan\Document\DocumentInterface
   */
  public $document;

  /**
   * error
   *
   * @var array
   */
  public $error;

  /**
   * The kind of operation.
   *
   * @var string
   */
  public $operation;

  /**
   * The message variable.
   *
   * @var string
   */
  public $message;

  /**
   * Set the value for Id.
   *
   * @param bool|string $id
   *   The value to set.
   */
  public function setId($id) {

    $this->id = $id;
  }

  /**
   * Get the value for Id.
   *
   * @return bool|string
   *   The value of Id.
   */
  public function getId() {

    if (isset($this->id)) {

      return $this->id;
    }

    return FALSE;
  }

  /**
   * Set the value for Result.
   *
   * @param array $result
   *   The value to set.
   */
  public function setResult($result) {

    $this->results = $result;
  }

  /**
   * Get the value for Result.
   *
   * @return array|MongoCursor
   *   The value of Result.
   */
  public function getResult() {

    if (isset($this->results)) {

      return $this->results;
    }

    return array();
  }

  /**
   * Set the value for Success.
   *
   * @param bool $success
   *   The value to set.
   */
  public function setSuccess($success) {

    $this->success = $success;
  }

  /**
   * Get the value for Success.
   *
   * @return bool
   *   The value of Success.
   */
  public function getSuccess() {

    if (isset($this->success)) {

      return $this->success;
    }

    return NULL;
  }

  /**
   * Set the Document.
   *
   * @param \Vultan\Document\DocumentInterface $data
   *   The value to set.
   */
  public function setDocument(DocumentInterface $data) {

    $this->document = $data;
  }

  /**
   * Get the Document.
   *
   * @return array
   *   The value of Data.
   */
  public function getDocument() {

    if (isset($this->document)) {
      return $this->document;
    }

    return NULL;
  }

  /**
   * Set the value for Error.
   *
   * @param array $error
   *   The value to set.
   */
  public function setError($error) {

    $this->error = $error;
  }

  /**
   * Get the value for Error.
   *
   * @return array
   *   The value of Error.
   */
  public function getError() {

    if (isset($this->error)) {
      return $this->error;
    }

    return NULL;
  }

  /**
   * Set the value for Operation.
   *
   * @param string $operation
   *   The value to set.
   */
  public function setOperation($operation) {

    $this->operation = $operation;
  }

  /**
   * Get the value for Operation.
   *
   * @return string
   *   The value of Operation.
   */
  public function getOperation() {

    return $this->operation;
  }

  /**
   * Return a human readable result message.
   *
   * @return string
   *   The message.
   */
  public function getMessage() {

    if ($this->getSuccess() == TRUE) {

      return 'Successfully ' . $this->changeOperationTense($this->getOperation()) . ' document with ID: ' . $this->getId();
    }
    else {
      return 'Failed to ' . $this->getOperation() . ' document with ID: ' . $this->getId();
    }
  }

  /**
   * Helper to turn an operation into a past-participle verb.
   *
   * @param string $op
   *   The operation.
   *
   * @return string
   *   The verb.
   */
  protected function changeOperationTense($op) {

    if ($op == 'insert') {

      return 'inserted';
    }

    if ($op == 'update') {

      return 'updated';
    }

    if ($op == 'upsert') {

      return 'upserted';
    }
  }

  /**
   * Determine if there is more than one result for an operation.
   *
   * @return bool
   *   TRUE if there is more than one result.
   */
  public function hasManyResults() {
    if (count($this->results) > 1) {
      return TRUE;
    }

    return FALSE;
  }
}
