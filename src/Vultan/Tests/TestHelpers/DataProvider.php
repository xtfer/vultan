<?php

/**
 * @file
 * Contains a DataProvider.
 */

namespace Vultan\Tests\TestHelpers;

/**
 * Class DataProvider
 *
 * @package Vultan\Tests\TestHelpers
 */
class DataProvider {

  /**
   * Return some common data for creating documents for testing.
   *
   * @return array
   *   An array of properties.
   */
  static public function getStandardData() {

    $data = array(
      'marque' => 'Mercedes-Benz',
      'model' => '560 SEL',
      'year' => '1988',
      'options' => array(
        'Hydro-pneumatic suspension',
        'Airbag',
        'ASR',
        'Roller blind',
      ),
      'engine' => '117.968 22 046874',
      'collection' => 'cars',
    );

    return $data;
  }
}
