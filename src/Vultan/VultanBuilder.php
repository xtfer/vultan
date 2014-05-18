<?php
/**
 * @file
 * Provides a Factory Controller for Vultan
 *
 * @copyright Copyright(c) 2013 - 2014 Chris Skene
 * @license GPL v2 http://www.fsf.org/licensing/licenses/gpl.html
 * @author Chris Skene chris at xtfer dot com
 */

namespace Vultan;

use Vultan\Vultan;
use Vultan\Query\Select;

/**
 * Class Controller
 *
 * @package Vultan
 */
class VultanBuilder {

  /**
   * The main Vultan controller.
   *
   * @var Vultan;
   */
  protected $vultan;

  /**
   * Can't be directly constructed.
   */
  protected function __construct(Vultan $vultan) {

    $this->setVultan($vultan);
  }

  /**
   * Invoke Vultan, returning a Vultan object.
   *
   * @param Config|null $config
   *   (Optional) A configuration object.
   *
   * @return \Vultan\VultanBuilder
   *   The Vultan controller.
   */
  static public function init($config = NULL) {

    if (empty($config)) {
      $config = new Config();
    }

    $vultan = Vultan::init($config);

    return new static($vultan);
  }

  /**
   * Invoke Vultan with a database connection.
   *
   * @param Config|null $config
   *   (Optional) A configuration object.
   *
   * @return \Vultan\VultanBuilder
   *   A Vultan Controller.
   */
  static public function initAndConnect($config = NULL) {
    $vultan_builder = static::init($config);

    $vultan_builder->getVultan()->connect();

    return $vultan_builder;
  }

  /**
   * Returns a Vultan select query object.
   *
   * @return \Vultan\Query\Select
   *   A Vultan Query object.
   */
  public function select() {

    return Select::init($this->getVultan()->getDatabase());
  }

  /**
   * Set the value for Vultan.
   *
   * @param \Vultan\Vultan $vultan
   *   The value to set.
   */
  public function setVultan($vultan) {

    $this->vultan = $vultan;
  }

  /**
   * Get the value for Vultan.
   *
   * @return \Vultan\Vultan
   *   The value of Vultan.
   */
  public function getVultan() {

    return $this->vultan;
  }

  /**
   * Shortcut to retrieve the database.
   *
   * @return \Vultan\Vultan\Database
   *   A Vultan Database handler.
   */
  public function getDatabase() {

    return $this->getVultan()->getDatabase();
  }
}
