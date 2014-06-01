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

// Using the connect() method without arguments returns a connection to the
// default MongoDB on localhost.
$vultan = \Vultan\Vultan::connect();

// Choose a database.
$vultan->useDatabase('cars');

// Choose a collection to work with.
$vultan->useCollection('marques');

// Prepare some data.
$data = array(
  'name' => 'Rolls Royce',
  'founded' => '1906',
  'type' => 'manufacturer',
);

/*
 * Insert a single record.
 */

// Insert some data.
$result = $vultan->insert($data)->execute();

// Print a useful message.
print '<h2>First insert result:</h2>';
print '<p>' . $result->getMessage() . '</p>';

/*
 * Insert some data in a 'safe' way.
 */

// This returns an array containing the status of the insert.
// See http://php.net/manual/en/mongocollection.insert.php for possible
// return values, but note that at some point Vultan will do its own error
// handling.
//
// Prepare some data.
$data = array(
  'name' => 'Mercedes Benz',
  'founded' => '1926',
  'type' => 'manufacturer',
);

// This will add a second, new item with the same details.
$result = $vultan->insert($data, \Vultan\Query\BaseQuery::WRITE_SAFE)->execute();

// Print a useful message.
print '<h2>Second insert result:</h2>';
print '<p>' . $result->getMessage() . '</p>';
