# Vultan

A MongoDB wrapper for PHP.

## Usage

````php

// Fire up the DB connection and return the DB worker object.
$config = \Vultan\Config::init();
$config->setDb('my_database');

$Vultan = \Vultan\VultanBuilder::initAndConnect($config);

$database = $Vultan->getDatabase();

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
