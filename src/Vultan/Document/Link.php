<?php

/**
 * @file
 * Contains a Document Link
 */

namespace Vultan\Document;

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
   * @param mixed $target
   *   The target for this link.
   */
  public function __construct($name, $target) {
    $this->name = $name;
    $this->target = $target;
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
