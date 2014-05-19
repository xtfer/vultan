<?php

/**
 * @file
 * Contains a VultanTest
 */


namespace Vultan\Tests;

use Vultan\Config;
use Vultan\Vultan;
use Vultan\VultanBuilder;

/**
 * Class VultanTest
 *
 * @package Vultan\Tests
 */
class VultanTest extends \PHPUnit_Framework_TestCase {

  /**
   * Basic initialistion tests.
   */
  public function test() {

    $config = Config::create()->prepare('test');

    $vultan = Vultan::init($config);

    $database = $vultan->getDatabase();
    $this->assertEquals('Vultan\\Vultan\\Database', get_class($database));

    $connection = $vultan->getConnection();
    $this->assertEquals('Vultan\\Vultan\\Connection', get_class($connection));

    $document_factory = $vultan->getDocumentFactory();
    $this->assertEquals('Vultan\\Document\\DocumentFactory', get_class($document_factory));

  }
}
