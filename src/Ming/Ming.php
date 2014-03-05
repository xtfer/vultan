<?php
/**
 * @file
 * Provides a Broker for Ming.
 *
 * @copyright Copyright(c) 2013 Chris Skene
 * @license GPL v2 http://www.fsf.org/licensing/licenses/gpl.html
 * @author Chris Skene chris at xtfer dot com
 */

namespace Ming;

use Ming\Document\DocumentFactory;
use Ming\Ming\Connection;
use Ming\Ming\Database;
use Ming\Model\ModelFactory;
use Ming\Traits\ConfigTrait;

/**
 * Class Broker
 *
 * @package Ming\Ming
 */
class Ming {

  use ConfigTrait;

  /**
   * The Connection.
   *
   * @var Connection
   */
  protected $connection;

  /**
   * The Database.
   */
  protected $database;

  /**
   * Constructor.
   *
   * @param Config $config
   *   A Ming Config object.
   */
  public function __construct(Config $config) {

    $this->setConfig($config);

    $this->initConnection();
    $this->initDatabase();
  }

  /**
   * Static factory method.
   *
   * @param Config $config
   *   A Ming Config object.
   *
   * @return \Ming\Ming
   *   This controller, for chaining.
   */
  static public function init(Config $config) {

    return new static($config);
  }

  /**
   * Instantiate a ming database connection.
   *
   * @return \Ming\Ming\Database
   *   A Ming database object.
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
   * Initialise the Connection object.
   *
   * @return Connection
   *   A Connection object.
   */
  protected function initConnection() {

    $connection = Connection::init($this->getConfig());

    $this->setConnection($connection);
  }

  /**
   * Initialise the Database object.
   *
   * @return \Ming\Ming\Database
   *   A Database object.
   */
  protected function initDatabase() {

    $db = new Database($this->getConfig());

    $this->setDatabase($db);
  }

  /**
   * Get the Ming Database.
   *
   * @return \Ming\Ming\Database
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
   * @param \Ming\Ming\Database $database
   *   The Database object.
   */
  public function setDatabase(Database $database) {

    $this->database = $database;
  }

  /**
   * Get the value for Connection.
   *
   * @return \Ming\Ming\Connection
   *   The value of Connection.
   */
  public function getConnection() {

    return $this->connection;
  }

  /**
   * Set the value for Connection.
   *
   * @param \Ming\Ming\Connection $connection
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

  /**
   * Return the Model Factory service.
   *
   * @return ModelFactory
   *   A ModelFactory object.
   */
  public function getModelFactory() {

    return ModelFactory::init($this->getConfig());
  }
}
