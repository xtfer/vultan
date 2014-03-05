<?php
/**
 * @file
 * Example showing the difference between Simple vs Builder approaches.
 */

// Simply loads the Composer Autoloader.
require '../vendor/autoload.php';

// Fire up the DB connection and return the DB worker object.
$config = \Ming\Config::init();
$config->setDb('my_database');

// 1. Simple Ming loading
//
// Initialise the Ming object. This loads the connection and database
// controllers. An exception thrown here would possibly indicate a broken
// connection or missing PHP MongoDB driver.
$ming = \Ming\Ming::init($config);

// Connect to the database.
// If the database does not exist, it will be created here.
$ming->connect();

// Load the database object for CRUD operations.
$database = $ming->getDatabase();

// Run an operation.
$result = $database->find(array('some_key' => 'some_value'));

// 2. Loading Ming with the Builder.
//
// The same set of steps can be accomplished more simply using the MingBuilder
// class, which essentially wraps these steps.
$ming = \Ming\MingBuilder::initAndConnect($config);
$database = $ming->getDatabase();
$result = $database->find(array('some_key' => 'some_value'));
