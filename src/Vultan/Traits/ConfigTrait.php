<?php
/**
 * @file
 * Contains a Trait for storing a Config object.
 */

namespace Vultan\Traits;

use Vultan\Config;

/**
 * Trait Config
 *
 * @package Vultan\Traits
 */
trait ConfigTrait {

  /**
   * config
   *
   * @var \vultan\Config
   */
  protected $config;

  /**
   * Get the value for Config.
   *
   * @return \Vultan\Config
   *   The value of Config.
   */
  public function getConfig() {

    return $this->config;
  }

  /**
   * Set the value for Config.
   *
   * @param \Vultan\Config $config
   *   The value to set.
   */
  public function setConfig(Config $config) {

    $this->config = $config;
  }

}
