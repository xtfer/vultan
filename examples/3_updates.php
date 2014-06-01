<?php
/**
 * @file
 * Provides an example for updating data.
 */

// Simply loads the Composer Autoloader.
require '../vendor/autoload.php';

// Using the connect() method without arguments returns a connection to the
// default MongoDB on localhost.
$vultan = \Vultan\Vultan::connect()
  ->useDatabase('cars')
  ->useCollection('marques');

/*
 * Update the first matched item.
 */

// We now have two records, this will only update one.
$data = array(
  'name' => 'Rolls Royce',
  'founded' => '1906',
  'place' => 'Manchester',
  'type' => 'manufacturer',
);
$filter = array('name' => 'Rolls Royce');
$result = $vultan->update($filter, $data)->execute();

// Message...
// Print a useful message.
print '<h2>First update result:</h2>';
print '<p>' . $result->getMessage() . '</p>';

/*
 * Update all matched items.
 */

// There are still two records, however they are now different. We'll do a
// partial update on both. This will update only the field specified.
$filter = array(
  'type' => 'manufacturer',
);
$data = array(
  'founded' => '1900',
);
$result = $vultan->updateAll($filter, $data, TRUE)->execute();

// Print a useful message.
print '<h2>Second update result:</h2>';
print '<p>' . $result->getMessage() . '</p>';
