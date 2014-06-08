<?php

/**
 * @file
 * Contains a Document Link
 */

namespace Vultan\Document;

use Vultan\Exception\VultanException;

/**
 * Class Link
 *
 * @package Vultan\Document
 */
class Link {

  protected $name;
  protected $target;

  /**
   * Constructor.
   *
   * @param string $name
   *   Name of the link.
   * @param mixed $document
   *   The target for this link.
   *
   * @throws \Vultan\Exception\VultanException
   */
  public function __construct($name, $document) {
    $this->name = $name;
    if (is_string($document)) {
      // This is a MongoID string.
      $this->target = $document;
    }
    elseif (get_class($document) == '\MongoID') {
      // This is a MondoID object.
      $this->target = (string) $document;
    }
    elseif ($document instanceof DocumentInterface || $document instanceof DocumentCompatibilityInterface) {
      // This is a document.
      /* @var DocumentInterface $document */
      $this->target = $document->getId();
    }
    else {
      throw new VultanException('No valid Mongo ID found on linked document');
    }
  }

  /**
   * Get the value for Name.
   *
   * @return string
   *   The value of Name.
   */
  public function getName() {

    return $this->name;
  }

  /**
   * Get the value for Target.
   *
   * @return mixed
   *   The value of Target.
   */
  public function getTarget() {

    return $this->target;
  }

}
