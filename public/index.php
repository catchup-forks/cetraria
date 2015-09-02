<?php

use Cetraria\Library\Application;

require realpath(dirname(dirname(__FILE__))) . '/config/env.php';

$application = new Application;
$application->init();

$di = $application->getDI();

var_dump($application);
var_dump(class_exists('Abc\Cde'));
