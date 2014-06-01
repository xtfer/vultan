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

  use TestSetup;

  public function setUp() {

    $this->preFlight();
  }

  public function testCreateDocument() {

    $factory = DocumentFactory::init($this->config);
    $document = $factory->createDocument();

    $this->assertEquals('Vultan\\Document\\Document', get_class($document));
  }
}
