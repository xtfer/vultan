<?php
/**
 * @file
 * Configuration example.
 */

// Simply loads the Composer Autoloader.
require '../vendor/autoload.php';

// Fire up the DB connection and return the DB worker object.
$config = \Ming\Config::init();
$config->setDb('my_database');

?>
<ul>
  <li>Database: <?php print $config->getDatabaseName(); ?></li>
  <li>Host: <?php print $config->getHost(); ?></li>
  <li>User: <?php print $config->getUser(); ?></li>
  <li>Pass: <?php print $config->getPass(); ?></li>
  <li>Port: <?php print $config->getPort(); ?></li>
</ul>

<?php

$ming = \Ming\MingBuilder::init($config);

?>

<p>Full connection string: <?php print $ming->getMing()->getConnection()
    ->buildConnectionString(); ?></p>
