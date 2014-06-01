<?php

/**
 * @file
 * Contains a DatabaseTest
 */


namespace Vultan\Tests;

use Vultan\Document\DocumentFactory;
use Vultan\Tests\TestHelpers\DataProvider;
use Vultan\Tests\TestHelpers\TestSetup;
use Vultan\Document\Document;

/**
 * Class DatabaseTest
 *
 * @package Vultan\Tests
 */
class DatabaseTest extends \PHPUnit_Framework_TestCase {

  use TestSetup;

  /**
   * The lastDocument variable.
   *
   * @var Document
   */
  public $lastDocument;

  /**
   * {@inheritdoc}
   */
  public function setup() {

    $this->preFlight();
  }

  /**
   * Test identifier creation.
   */
  public function testCreateMongoIdentifier() {

    $id = $this->vultan->createMongoIdentifier();

    $this->assertEquals('MongoId', get_class($id));
  }

  /**
   * Test Document preparation and conversion.
   */
  public function testPrepareDocument() {

    $some_data = DataProvider::getStandardData();

    $document = DocumentFactory::init($this->config)->prepareDocument($some_data);

    $this->assertEquals('Vultan\\Document\\Document', get_class($document));
    $this->assertEquals('Mercedes-Benz', $document->get('marque'));

  }
}
