<?php
/**
 * @file
 * Provides a Factory Controller for Ming
 *
 * @copyright Copyright(c) 2013 Chris Skene
 * @license GPL v2 http://www.fsf.org/licensing/licenses/gpl.html
 * @author Chris Skene chris at xtfer dot com
 */

namespace Ming;

use Ming\Document\DocumentFactory;
use Ming\Ming;
use Ming\Query\Select;

/**
 * Class Controller
 *
 * @package Ming
 */
class MingBuilder {

  /**
   * The main Ming controller.
   *
   * @var Ming;
   */
  protected $ming;

  /**
   * Can't be directly constructed.
   */
  protected function __construct(Ming $ming) {

    $this->setMing($ming);
  }

  /**
   * Invoke Ming, returning a Ming object.
   *
   * @param Config|null $config
   *   (Optional) A configuration object.
   *
   * @return \Ming\MingBuilder
   *   The Ming controller.
   */
  static public function init($config = NULL) {

    if (empty($config)) {
      $config = new Config();
    }

    $ming = Ming::init($config);

    return new static($ming);
  }

  /**
   * Invoke Ming with a database connection.
   *
   * @param Config|null $config
   *   (Optional) A configuration object.
   *
   * @return \Ming\MingBuilder
   *   A Ming Controller.
   */
  static public function initAndConnect($config = NULL) {
    $ming_builder = static::init($config);

    $ming_builder->getMing()->connect();

    return $ming_builder;
  }

  /**
   * Returns a Ming select query object.
   *
   * @return \Ming\Query\Select
   *   A Ming Query object.
   */
  public function select() {

    return Select::init($this->getMing()->getDatabase());
  }

  /**
   * Set the value for Ming.
   *
   * @param \Ming\Ming $ming
   *   The value to set.
   */
  public function setMing($ming) {

    $this->ming = $ming;
  }

  /**
   * Get the value for Ming.
   *
   * @return \Ming\Ming
   *   The value of Ming.
   */
  public function getMing() {

    return $this->ming;
  }

  /**
   * Shortcut to retrieve the database.
   *
   * @return \Ming\Ming\Database
   *   A Ming Database handler.
   */
  public function getDatabase() {

    return $this->getMing()->getDatabase();
  }
}
