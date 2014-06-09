<?php

/**
 * @file
 * Contains a VultanTest
 */


namespace Vultan\Tests;

use Vultan\Config;
use Vultan\Tests\TestHelpers\TestSetup;
use Vultan\Vultan;
use Vultan\VultanBuilder;

/**
 * Class VultanTest
 *
 * @package Vultan\Tests
 */
class VultanTest extends \PHPUnit_Framework_TestCase {

  use TestSetup;

  /**
   * Constructor.
   */
  public function setup() {
    $this->preFlight();
  }

  /**
   * Basic initialisation tests.
   */
  public function testInit() {

    $database = $this->vultan->getDatabase();
    $this->assertEquals('Vultan\\Vultan\\Database', get_class($database));

    $connection = $this->vultan->getConnection();
    $this->assertEquals('Vultan\\Vultan\\Connection', get_class($connection));

    $collection = $this->vultan->getCollection();
    $this->assertEquals('Vultan\\Vultan\\Collection', get_class($collection));

    $document_factory = $this->vultan->getDocumentFactory();
    $this->assertEquals('Vultan\\Document\\DocumentFactory', get_class($document_factory));

  }
}
