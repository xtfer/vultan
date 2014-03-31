<?php
/**
 * @file
 * Provides a simple insert example.
 */

// Simply loads the Composer Autoloader.
require '../vendor/autoload.php';

/*
 * Setup
 */

// Fire up the DB connection and return the DB worker object.
$config = \Vultan\Config::init();
$config->setDb('my_database');

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

// Print a useful message.
print '<p>' . $result->successMessage() . '</p>';

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

// Print a useful message.
print '<p>' . $result->successMessage() . '</p>';
