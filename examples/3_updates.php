<?php
/**
 * @file
 * Provides an example for updating data.
 */

// Simply loads the Composer Autoloader.
require '../vendor/autoload.php';

// Fire up the DB connection and return the DB worker object.
$config = \Vultan\Config::init();
$config->setDb('my_database');

$vultan = \Vultan\VultanBuilder::initAndConnect($config);

$database = $vultan->getDatabase();

// Choose a collection to work with.
$database->useCollection('cars');

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
// Print a useful message.
print '<p>' . $result->successMessage() . '</p>';

/*
 * Update all items.
 */

// There are still two records, however they are now different. We'll do a
// partial update on both. This will update only the field specified.
$data = array(
  'origin' => 'United Kingdom',
);
$result = $database->updateAll($filter, $data, TRUE);

// Print a useful message.
print '<p>' . $result->successMessage() . '</p>';
