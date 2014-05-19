<?php

/**
 * @file
 * Contains a DocumentFactoryTest
 */


namespace Vultan\Tests\Document;

use Vultan\Document\DocumentFactory;
use Vultan\Tests\TestHelpers\TestSetup;

/**
 * Class DocumentFactoryTest
 *
 * @package Vultan\Tests\Document
 */
class DocumentFactoryTest extends \PHPUnit_Framework_TestCase {

  use \Vultan\Tests\TestHelpers\TestSetup;

  public function setUp() {

    $this->preFlight();
  }

  public function testCreateDocument() {

    $factory = DocumentFactory::init($this->config);
    $document = $factory->createDocument();

    $db = $document->getDatabase();

    $this->assertEquals('Vultan\\Vultan\\Database', get_class($db));
  }
}
