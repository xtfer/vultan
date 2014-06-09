# Vultan: A MongoDB wrapper for PHP

Vultan wraps the native MongoPHP driver and provides a fluent, extensible query
interface.

## Insert a document

````php

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
  'place' => 'Manchester'
);

// Insert some data.
$result = $vultan->insert($data)->execute();

````

## Find a document

````php

$vultan = \Vultan\Vultan::connect()
  ->useDatabase('cars')
  ->useCollection('marques');

$query = $vultan->find();

$result_data = $query
  // Add some conditions.
  ->addCondition('founded', '1906')
  ->addCondition('place', 'Manchester')
  // Ensure the query returns an array. Omit this if you prefer an iterator.
  ->resultsAsArray()
  // Run the query.
  ->execute();

print_r($result_data);
````
