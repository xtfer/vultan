<?php
/**
 * @file
 * Contains a factory object for building Documents.
 */

namespace Vultan\Document;

use Vultan\Config;

/**
 * Class DocumentFactory
 *
 * @package Vultan\Document
 */
class DocumentFactory {

  /**
   * config
   *
   * @var Config
   */
  protected $config;

  /**
   * Protected Constructor. Use DocumentFactory::init() instead.
   */
  protected function __construct() {

  }

  /**
   * Constructor.
   *
   * @param \Vultan\Config $config
   *   A Config object.
   *
   * @return \Vultan\Document\DocumentFactory
   *   This document factory.
   */
  static public function init(Config $config) {

    $factory = static::instantiate();

    $factory->setConfig($config);

    return $factory;
  }

  /**
   * Create a new Document.
   *
   * @param array $data
   *   (Optional) An array of data in insert into the document.
   *
   * @return Document
   *   A Document.
   */
  public function createDocument(array $data = array()) {

    $doc = new Document($this->getConfig(), $data);

    return $doc;
  }

  /**
   * Prepare the document data.
   *
   * @param array|object $document
   *   Convert the document data into a valid Document object.
   *
   * @return \Vultan\Document\DocumentInterface
   *   A Vultan Document.
   */
  public function prepareDocument($document) {

    $values = $document;

    // All of our objects MUST be valid Vultan Documents.
    // If this isn't, convert it to an array now, before we blow our
    // stack with a private or protected Exception.
    if (is_object($document)) {

      if (!$document instanceof DocumentInterface
        && !$document instanceof DocumentCompatibilityInterface
      ) {
        $values = get_object_vars($document);
      }

      if ($document instanceof DocumentCompatibilityInterface) {
        $values = $document->getValues();
      }
    }

    if (is_array($values)) {

      $document = $this->createDocument($values);
    }

    // Nix strange global ID creation.
    // @see http://stackoverflow.com/a/10183273/225682
    $document->cleanIdentitifer();

    return $document;
  }

  /**
   * Set the value for Config.
   *
   * @param Config $config
   *   The value to set.
   */
  public function setConfig(Config $config) {

    $this->config = $config;
  }

  /**
   * Get the value for Config.
   *
   * @return Config
   *   The value of Config.
   */
  public function getConfig() {

    return $this->config;
  }

  /**
   * Protected object instantiation.
   *
   * This exists purely for IDE type hinting.
   *
   * @return DocumentFactory
   *   This Factory.
   */
  protected static function instantiate() {

    return new static();
  }

}
