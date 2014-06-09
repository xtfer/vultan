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

// Using the connect() method without arguments returns a connection to the
// default MongoDB on localhost.
$vultan = \Vultan\Vultan::connect()
  ->useDatabase('cars')
  ->useCollection('marques');

// 1. Find
//
// BaseQuery for all items matching the following condition.
$result = $vultan->find(array('type' => 'manufacturer'))->execute();

// Print the results.
// $result is an iterator. We can foreach(), or use iterator_to_array().
$data = iterator_to_array($result);

print '<h2>First find result:</h2>';
print '<pre>';
print_r($data);
print '</pre>';

// 2. Advanced Find.
//
$query = $vultan->find();

$other_data = $query
  // Add some conditions.
  ->addCondition('founded', '1900')
  ->addCondition('place', 'Manchester')
  // Add all fields.
  ->fields()
  // Ensure the query returns an array, not an iterator.
  ->resultsAsArray()
  // Run the query.
  ->execute();

print '<h2>Second find result:</h2>';
print '<pre>';
print_r($other_data);
print '</pre>';

// 3. Find an item by its ID
//
// As a shortcut for this example, fetch the key of the last retrieved item
// from the data. This is a MongoID.
$item_key = key($other_data);

$results = $vultan->find()
  // MongoID filters require a special filter which auto-generates the MongoID
  // object.
  ->addFilterMongoID($item_key)
  ->fields(array('name', 'founded'))
  ->resultsAsArray()
  ->execute();

print '<h2>Find by ID:</h2>';
print '<pre>';
print_r($results);
print '</pre>';
