<?php
/**
 * @file
 * Contains a Trait for storing a Config object.
 */

namespace Vultan\Traits;

use Vultan\Config\Config;

/**
 * Trait Config
 *
 * @package Vultan\Traits
 */
trait ConfigTrait {

  /**
   * config
   *
   * @var \Vultan\Config\Config
   */
  protected $config;

  /**
   * Get the value for Config.
   *
   * @return \Vultan\Config\Config
   *   The value of Config.
   */
  public function getConfig() {

    return $this->config;
  }

  /**
   * Set the value for Config.
   *
   * @param \Vultan\Config\Config $config
   *   The value to set.
   */
  public function setConfig(Config $config) {

    $this->config = $config;
  }

}
