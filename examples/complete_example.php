<?php
/**
 * @file
 * Complete example.
 */

// Simply loads the Composer Autoloader.
require '../vendor/autoload.php';

/*
 * Setup
 */

// Fire up the DB connection and return the DB worker object.
$config = \Vultan\Config::init();
$config->setDatabase('my_database');

$vultan = \Vultan\VultanBuilder::initAndConnect($config);

$database = $vultan->getDatabase();

// Choose a collection to work with.
$database->useCollection('cars');

// Prepare some data.
$data = array(
  'marque' => 'Rolls Royce',
  'model' => 'Silver Shadow',
  'year' => '1975',
);

/*
 * Insert a single record.
 */

// Insert some data.
$result = $database->insert($data);

// The passed array $data will also have a new '_id' key. The same data can now
// be sent to an upsert without rekeying.
// Information about the request is also available in the Result object.
$result = $database->getLastResult();
$success = $result->getSuccess();
$insert_id = $result->getId();

// Print a useful message.
print '<p>Result: ' . $result->getSuccess() . ' on insert of ' . $result->getId() . '</p>';

/*
 * Insert some data in a 'safe' way.
 */

// This returns an array containing the status of the insert.
// See http://php.net/manual/en/mongocollection.insert.php for possible
// return values, but note that at some point Vultan will do its own error
// handling.
//
// Start by unsetting the '_id' parameter to get a new insert.
unset($data['_id']);

// This will add a second, new item with the same details.
$result = $database->insert($data, TRUE);

// Message...
print '<p>Result: ' . $result->getSuccess() . ' on insert of ' . $result->getId() . '</p>';

/*
 * Update the first matched item.
 */

// We now have two records for Silver Shadow's, this will only update one.
$data = array(
  'marque' => 'Rolls Royce',
  'model' => 'Silver Shadow II',
  'year' => '1976',
);
$filter = array('marque' => 'Rolls Royce');
$result = $database->update($filter, $data);

// Message...
print '<p>Result: ' . $result->getSuccess() . ' on update of ' . $result->getId() . '</p>';

/*
 * Update all items.
 */

// There are still two records, however they are now different. We'll do a
// partial update on both. This will update only the field specified.
$data = array(
  'origin' => 'United Kingdom',
);
$database->updateAll($filter, $data, TRUE);

/*
 * Query the database for existing items.
 */

// Query for all items matching the following condition.
$result = $database->find(array('marque' => 'Rolls Royce'));

// Print the results.
// $result is an iterator. We can foreach(), or use iterator_to_array().
$data = iterator_to_array($result);

print '<pre>';
print_r($data);
print '</pre>';

/*
 * Using the Select query tool.
 */

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
