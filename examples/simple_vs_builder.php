<?php
/**
 * @file
 * Example showing the difference between Simple vs Builder approaches.
 */

// Simply loads the Composer Autoloader.
require '../vendor/autoload.php';

// Fire up the DB connection and return the DB worker object.
$config = Vultan\Config::create();
$config->setDatabase('my_database');

// 1. Simple Vultan loading
//
// Initialise the Vultan object. This loads the connection and database
// controllers. An exception thrown here would possibly indicate a broken
// connection or missing PHP MongoDB driver.
$vultan = \Vultan\Vultan::init($config);

// Connect to the database.
// If the database does not exist, it will be created here.
$vultan->connect();

// Load the database object for CRUD operations.
$database = $vultan->getDatabase();

// Run an operation.
$result = $database->find(array('some_key' => 'some_value'));

// 2. Loading Vultan with the Builder.
//
// The same set of steps can be accomplished more simply using the VultanBuilder
// class, which essentially wraps these steps.
$vultan = \Vultan\VultanBuilder::initAndConnect($config);
$database = $vultan->getDatabase();
$result = $database->find(array('some_key' => 'some_value'));
