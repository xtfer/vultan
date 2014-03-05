# ming

A MongoDB wrapper for PHP.

## Usage

````php

// Fire up the DB connection and return the DB worker object.
$config = \Ming\Config::init();
$config->setDb('my_database');

$ming = \Ming\MingBuilder::initAndConnect($config);

$database = $ming->getDatabase();

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

````
