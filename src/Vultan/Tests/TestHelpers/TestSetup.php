<?php

/**
 * @file
 * Contains a TestSetup Trait.
 */


namespace Vultan\Tests\TestHelpers;

use Vultan\Config;
use Vultan\VultanBuilder;

/**
 * Trait TestSetup
 *
 * @package Vultan\Tests
 */
trait TestSetup {

  /**
   * The database variable.
   *
   * @var \Vultan\Vultan\Database
   */
  public $database;

  /**
   * The config variable.
   *
   * @var Config
   */
  public $config;

  /**
   * The vultan variable.
   *
   * @var VultanBuilder
   */
  public $vultanBuilder;

  /**
   * {@inheritdoc}
   */
  public function preFlight() {

    $this->config = Config::create()->prepare('test');

    $this->vultanBuilder = VultanBuilder::init($this->config);
  }
}
