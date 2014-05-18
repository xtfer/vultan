<?php
/**
 * @file
 * The Connection class
 *
 * @copyright Copyright(c) 2013 - 2014 Chris Skene
 * @license GPL v2 http://www.fsf.org/licensing/licenses/gpl.html
 * @author Chris Skene chris at xtfer dot com
 */

namespace Vultan\Vultan;

use Vultan\Config;
use Vultan\Exception\VultanException;
use Vultan\Traits\ConfigTrait;

/**
 * Class Connection
 *
 * @package Drupal\vultan\Vultan
 */
class Connection {

  use ConfigTrait;

  /**
   * The Mongo connection
   *
   * Because this is a public object, we can call all the usual Mongo methods on
   * it, for example Connection::connection->listDBs
   *
   * @var \MongoClient
   */
  public $MongoClient;

  /**
   * Constructor.
   *
   * This will not initialise a connection. For that use the static::init()
   * constructor, or call $this->connect().
   *
   * @param \Vultan\Config $config
   *   A Vultan Configuration object.
   *
   * @return \Vultan\Vultan\Connection
   *   This connection object, with an initialised database connection.
   */
  public function __construct(Config $config) {
    $this->config = $config;

    return $this;
  }

  /**
   * Connect to a mongo database.
   *
   * @param \Vultan\Config $config
   *   A Vultan Configuration object.
   *
   * @return \Vultan\Vultan\Connection
   *   This connection object, with an initialised database connection.
   */
  static public function init(Config $config) {
    $connection = new static($config);

    /* @var $connection Connection */
    $connection->connect();

    return $connection;
  }

  /**
   * Prepares and loads a connection from the objects settings.
   *
   * @todo Add Replica Set and Socket support
   *
   * @return \Vultan\Vultan\Connection
   *   A PHP Mongo class
   */
  public function connect() {

    $connection_string = $this->buildConnectionString();

    $this->initialiseClient($connection_string);

    return $this;
  }

  /**
   * Build a connection string for mongodb out of config parameters.
   *
   * @return string
   *   The connection string.
   */
  public function buildConnectionString() {

    // Build an authentication string.
    $auth_string = '';

    $user = $this->getConfig()
      ->getUser();
    $pass = $this->getConfig()
      ->getPass();

    // Set the User and Pass if present.
    if (!empty($user) && !empty($pass)) {
      $auth_string = "${$user}:${$pass}@";
    }

    $connection_string = 'mongodb://' . $auth_string . $this->getConfig()->getHost() . ':' . $this->getConfig()->getPort();

    return $connection_string;
  }

  /**
   * Shortcut for MongoClient::selectDB.
   *
   * @param string $db_name
   *   Name of the Database.
   *
   * @return \MongoDB
   *   A MongoDB object.
   */
  public function useDatabase($db_name) {

    return $this->getMongoClient()->selectDB($db_name);
  }

  /**
   * Return information about the Connection status.
   *
   * @return array
   *   Information about the connection.
   */
  public function getConnectionStatus() {

    if ($this->hasConnection() == TRUE) {
      return $this->getMongoClient()->getConnections();
    }

    return array();
  }

  /**
   * Determine if a valid connection has been started.
   *
   * @return bool
   *   TRUE, if the connection is present.
   */
  public function hasConnection() {

    if (isset($this->MongoClient) && $this->MongoClient instanceof \MongoClient) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Get the value for MongoClient.
   *
   * @throws \Vultan\Exception\VultanException
   * @return \MongoClient
   *   The value of MongoClient.
   */
  public function getMongoClient() {

    if (!isset($this->MongoClient) || empty($this->MongoClient)) {

      throw new VultanException('No MongoClient object loaded in Vultan.');
    }

    return $this->MongoClient;
  }

  /**
   * Load a connection.
   *
   * @param string $connection_string
   *   The server connection string.
   *
   * @throws \vultan\Exception\VultanException
   */
  public function initialiseClient($connection_string) {

    if (!class_exists('MongoClient')) {
      throw new VultanException('MongoDB PHP drivers not found.');
    }

    $this->MongoClient = new \MongoClient($connection_string, $this->getConfig()->getOptions());
  }
}
