<?php
/**
 * @file
 * Contains a ModelFactory
 */

namespace Ming\Model;

use Ming\Config;
use Ming\Exception\MingModelException;
use Ming\Traits\ConfigTrait;

/**
 * Class ModelFactory
 *
 * @package Ming\Model
 */
class ModelFactory {

  use ConfigTrait;

  /**
   * Constructor.
   *
   * @param \Ming\Config $config
   *   The Config information.
   *
   * @return ModelFactory
   *   A ModelFactory
   */
  protected function __construct(Config $config) {

    $this->setConfig($config);
  }

  /**
   * Static constructor.
   *
   * @param \Ming\Config $config
   *   The Config information.
   *
   * @return ModelFactory
   *   A ModelFactory
   */
  static public function init(Config $config) {

    return new static($config);
  }

  /**
   * Create a new Model.
   *
   * @return Model
   *   The Model.
   */
  public function createModel() {

    $model = new Model();

    return $model;
  }

}
