<?php
/**
 * @file
 * Defines a basic Configuration class for Vultan.
 *
 * @copyright Copyright(c) 2013 - 2014 Chris Skene
 * @license GPL v2 http://www.fsf.org/licensing/licenses/gpl.html
 * @author Chris Skene chris at xtfer dot com
 */

namespace Vultan;

use Vultan\Exception;

/**
 * Class Config
 *
 * @package vultan
 */
class Config {

  /**
   * host
   *
   * @var string
   */
  protected $host;

  /**
   * port
   *
   * @var string
   */
  protected $port;

  /**
   * user
   *
   * @var string
   */
  protected $user;

  /**
   * pass
   *
   * @var string
   */
  protected $pass;

  /**
   * db
   *
   * @var string
   */
  protected $database;

  /**
   * options
   *
   * @var array
   */
  protected $options;

  /**
   * model
   *
   * @var array
   */
  protected $model;

  /**
   * Public constructor.
   */
  public function __construct() {

    // This is the standard MongoDB connection option.
    $this->options['connect'] = TRUE;
  }

  /**
   * Static factory.
   *
   * @return Config
   *   A Config object
   */
  public static function create() {

    return new static();
  }

  /**
   * Old initialiser.
   *
   * @deprecated
   * @see create()
   *
   * @return Config
   *   A Config Object.
   */
  public static function init() {

    return static::create();
  }

  /**
   * Shortcut to prepare a config object for a given database.
   *
   * If only the Database Name is provided, this will connect to the localhost
   * default connection.
   *
   * @param string $database_name
   *   The database name.
   * @param string|null $host
   *   The database host.
   * @param string|null $port
   *   The database port.
   * @param string|null $user
   *   The database username.
   * @param string|null $pass
   *   The database password.
   *
   * @return Config
   *   This config object.
   */
  static public function prepare($database_name, $host = NULL, $port = NULL, $user = NULL, $pass = NULL) {

    $config = static::create();

    if (!empty($host)) {
      $config->setHost($host);
    }
    if (!empty($port)) {
      $config->setPort($port);
    }
    if (!empty($user)) {
      $config->setUser($user);
    }
    if (!empty($pass)) {
      $config->setPass($pass);
    }

    $config->setDatabase($database_name);

    return $config;
  }

  /**
   * Set the value for Db.
   *
   * @param string $database_name
   *   The value to set.
   */
  public function setDatabase($database_name) {

    $this->database = $database_name;
  }

  /**
   * Get the value for Db.
   *
   * @return string
   *   The value of Db.
   */
  public function getDatabaseName() {

    if (isset($this->database)) {
      return $this->database;
    }

    return NULL;
  }

  /**
   * Set the value for Host.
   *
   * @param string $host
   *   The value to set.
   */
  public function setHost($host) {

    $this->host = $host;
  }

  /**
   * Get the value for Host.
   *
   * @return string
   *   The value of Host.
   */
  public function getHost() {

    if (isset($this->host)) {
      return $this->host;
    }

    return 'localhost';
  }

  /**
   * Set the value for Pass.
   *
   * @param string $pass
   *   The value to set.
   */
  public function setPass($pass) {

    $this->pass = $pass;
  }

  /**
   * Get the value for Pass.
   *
   * @return string
   *   The value of Pass.
   */
  public function getPass() {

    if (isset($this->pass)) {
      return $this->pass;
    }
  }

  /**
   * Set the value for Port.
   *
   * @param string $port
   *   The value to set.
   */
  public function setPort($port) {

    $this->port = $port;
  }

  /**
   * Get the value for Port.
   *
   * @return string
   *   The value of Port.
   */
  public function getPort() {

    if (isset($this->port)) {
      return $this->port;
    }

    return '27017';
  }

  /**
   * Set the value for User.
   *
   * @param string $user
   *   The value to set.
   */
  public function setUser($user) {

    $this->user = $user;
  }

  /**
   * Get the value for User.
   *
   * @return string
   *   The value of User.
   */
  public function getUser() {

    if (isset($this->user)) {
      return $this->user;
    }

    return NULL;
  }

  /**
   * Set the value for Options.
   *
   * @param array $options
   *   The value to set.
   */
  public function setOptions($options) {

    $this->options = $options;
  }

  /**
   * Get the value for Options.
   *
   * @return array
   *   The value of Options.
   */
  public function getOptions() {

    return $this->options;
  }

  /**
   * Set the value for Options.
   *
   * These options are used by the MongoClient on connection.
   *
   * @see http://www.php.net/manual/en/mongoclient.construct.php
   *
   * @param string $key
   *   The Mongo connection option name.
   * @param mixed $option
   *   The option value.
   */
  public function setOption($key, $option) {

    $this->options[$key] = $option;
  }

  /**
   * Get the value for Options.
   *
   * @param string $key
   *   The Mongo connection option name.
   * @param mixed|null $default
   *   (Optional) A default value to return.
   *
   * @return mixed
   *   The value of Options
   */
  public function getOption($key, $default = NULL) {

    if (isset($this->options[$key])) {
      return $this->options[$key];
    }

    return $default;
  }
}
