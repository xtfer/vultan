<?php
/**
 * @file
 * Provides a Broker for Vultan.
 *
 * @copyright Copyright(c) 2013 - 2014 Chris Skene
 * @license GPL v2 http://www.fsf.org/licensing/licenses/gpl.html
 * @author Chris Skene chris at xtfer dot com
 */

namespace Vultan;

use Vultan\Document\DocumentFactory;
use Vultan\Vultan\Connection;
use Vultan\Vultan\Database;
use Vultan\Traits\ConfigTrait;

/**
 * Class Vultan
 *
 * @package Vultan\Vultan
 */
class Vultan {

  use ConfigTrait;

  /**
   * The Connection.
   *
   * @var Connection
   */
  protected $connection;

  /**
   * The Database.
   *
   * @var Database
   */
  protected $database;

  /**
   * Constructor.
   *
   * @param Config $config
   *   A Vultan Config object.
   * @param Connection $connection
   *   A Vultan Connection object.
   * @param Database $database
   *   A Vultan Database object.
   */
  public function __construct(Config $config, Connection $connection, Database $database) {

    $this->connection = $connection;
    $this->database = $database;
    $this->config = $config;
  }

  /**
   * Static factory method.
   *
   * @param Config $config
   *   A Vultan Config object.
   *
   * @return \Vultan\Vultan
   *   This controller, for chaining.
   */
  static public function init(Config $config) {

    $connection = Connection::init($config);
    $mongo_db = $connection->useDatabase($config->getDatabaseName());
    $database = Database::init($config, $mongo_db);

    return new static($config, $connection, $database);
  }

  /**
   * Get the Vultan Database.
   *
   * @return \Vultan\Vultan\Database
   *   The value of Connection.
   */
  public function getDatabase() {

    return $this->database;
  }

  /**
   * Get the value for Connection.
   *
   * @return \Vultan\Vultan\Connection
   *   The value of Connection.
   */
  public function getConnection() {

    return $this->connection;
  }

  /**
   * Return the Document Factory service.
   *
   * @return DocumentFactory
   *   A DocumentFactory object.
   */
  public function getDocumentFactory() {

    return DocumentFactory::init($this->getConfig());
  }
}
