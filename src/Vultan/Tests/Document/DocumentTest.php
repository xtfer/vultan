<?php

/**
 * @file
 * Contains a DocumentTest.
 */


namespace Vultan\Tests\Document;

use Vultan\Document\Document;
use Vultan\Tests\TestHelpers\DataProvider;
use Vultan\Tests\TestHelpers\TestSetup;

/**
 * Class DocumentTest
 *
 * @package Vultan\Tests\Document
 */
class DocumentTest extends \PHPUnit_Framework_TestCase {

  use TestSetup;

  public function setUp() {
    $this->preFlight();
  }

  public function testConstruct() {

    $data = DataProvider::getStandardData();

    $document = Document::create($this->config, $data);

    $values = $document->getProperties();

    $this->assertArrayHasKey('marque', $values);
    $this->assertEquals('Mercedes-Benz', $values['marque']);
  }

  public function testSetIdentifier() {

    // Test automatic creation.
    $document = Document::create($this->config);
    $test_id = new \MongoId();
    $document->setIdentifier($test_id);
    $this->assertEquals((string) $test_id, $document->getId());

    // Test assigning a value.
    $dummy_id = '123456781234567812345678';

    $test_id = new \MongoId($dummy_id);
    $document = Document::create($this->config);
    $document->setIdentifier($test_id);
    $this->assertEquals($dummy_id, $document->getId());

    // Check fetching with explicit call get().
    $this->assertEquals($dummy_id, $document->get('_id'));
  }

  public function testProperties() {

    $document = Document::create($this->config);

    // Test default property creation.
    $document->setDefaultProperties();

    $values = $document->getProperties();

    // Default properties are currently just updated and created timestamps.
    $this->assertArrayHasKey('time_created', $values);
    $this->assertArrayHasKey('time_updated', $values);

    // Test adding more properties.
    $some_values = DataProvider::getStandardData();
    $document->setProperties($some_values);

    $values = $document->getProperties();

    // Check our existing defaults are still there.
    $this->assertArrayHasKey('time_created', $values);
    $this->assertArrayHasKey('time_updated', $values);

    // The document should now have default test data set.
    $this->assertArrayHasKey('marque', $values);

    // As well as a collection.
    $this->assertEquals('cars', $document->getCollection());

    // And ID should only exist if we've added one (or a save operation has).
    $test_id = new \MongoId();
    $document->setIdentifier($test_id);

    $values = $document->getProperties();

    // Check the ID is set, and that is internally consistent.
    $this->assertArrayHasKey('_id', $values);
    $this->assertEquals($values['_id'], $document->getId());

  }

}
