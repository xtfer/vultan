<?php
/**
 * @file
 * Contains a Trait for storing a Config object.
 */

namespace Ming\Traits;

use Ming\Config;

/**
 * Trait Config
 *
 * @package Ming\Traits
 */
trait ConfigTrait {

  /**
   * config
   *
   * @var \ming\Config
   */
  protected $config;

  /**
   * Get the value for Config.
   *
   * @return \Ming\Config
   *   The value of Config.
   */
  public function getConfig() {

    return $this->config;
  }

  /**
   * Set the value for Config.
   *
   * @param \Ming\Config $config
   *   The value to set.
   */
  public function setConfig(Config $config) {

    $this->config = $config;
  }

}
