<?php

/**
 * @file
 * Contains a TestSetup Trait.
 */


namespace Vultan\Tests\TestHelpers;

use Vultan\Config;
use Vultan\Vultan;

/**
 * Trait TestSetup
 *
 * @package Vultan\Tests
 */
trait TestSetup {

  /**
   * The config variable.
   *
   * @var Config
   */
  public $config;

  /**
   * The vultan variable.
   *
   * @var Vultan
   */
  public $vultan;

  /**
   * {@inheritdoc}
   */
  public function preFlight() {

    $this->vultan = Vultan::connect()
      ->useDatabase('test')
      ->useCollection('test');
  }
}
