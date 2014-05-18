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
 * Class Broker
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
   */
  public function __construct(Config $config) {

    $this->setConfig($config);

    $connection = Connection::init($this->getConfig());
    $this->setConnection($connection);

    $this->initDatabase();
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

    return new static($config);
  }

  /**
   * Instantiate a vultan database connection.
   *
   * @return \Vultan\Vultan\Database
   *   A Vultan database object.
   */
  public function connect() {

    $db_name = $this->getConfig()->getDatabaseName();
    $mongo_db = $this->getConnection()->useDatabase($db_name);

    // This is not necessary UNLESS this is a new database, in which case
    // selectDB() won't actually create the database.
    // @see http://stackoverflow.com/questions/4508529/
    // create-a-mongodb-database-with-php
    $mongo_db->listCollections();

    $this->getDatabase()->setDataSource($mongo_db);

    return $this->getDatabase();
  }

  /**
   * Initialise the Database object.
   *
   * @return \Vultan\Vultan\Database
   *   A Database object.
   */
  protected function initDatabase() {

    $db = new Database($this->getConfig());

    $this->setDatabase($db);
  }

  /**
   * Get the Vultan Database.
   *
   * @return \Vultan\Vultan\Database
   *   The value of Connection.
   */
  public function getDatabase() {

    if (!isset($this->database) || empty($this->database)) {

      $this->initDatabase();
    };

    return $this->database;
  }

  /**
   * Set the value for Connection.
   *
   * @param \Vultan\Vultan\Database $database
   *   The Database object.
   */
  public function setDatabase(Database $database) {

    $this->database = $database;
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
   * Set the value for Connection.
   *
   * @param \Vultan\Vultan\Connection $connection
   *   The value to set.
   */
  public function setConnection($connection) {

    $this->connection = $connection;
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
