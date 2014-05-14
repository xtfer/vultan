<?php
/**
 * @file
 * Example of the use of the find and select tools.
 *
 * This example assumes you have already inserted suitable data. The files
 * simple_insert.php and updates.php will insert this data for you.
 */

// Simply loads the Composer Autoloader.
require '../vendor/autoload.php';

// Fire up the DB connection and return the DB worker object.
$config = Vultan\Config::create();
$config->setDatabase('my_database');

$vultan = \Vultan\VultanBuilder::initAndConnect($config);

$database = $vultan->getDatabase();

// Choose a collection to work with.
$database->useCollection('cars');

// 1. Find
//
// Query for all items matching the following condition.
$result = $database->find(array('marque' => 'Rolls Royce'));

// Print the results.
// $result is an iterator. We can foreach(), or use iterator_to_array().
$data = iterator_to_array($result);

print '<pre>';
print_r($data);
print '</pre>';

// 2. Select
//
// The select query tool offers a more expressive syntax for building queries.
$select = $vultan->select();

$other_data = $select->collection('cars')
  // Add a condition on the 'model' field.
  ->condition('model', 'Silver Shadow')
  // Add all fields.
  ->fields()
  // Ensure the query returns an array, not an iterator.
  ->resultsAsArray()
  // Run the query.
  ->execute();

print '<pre>';
print_r($other_data);
print '</pre>';
