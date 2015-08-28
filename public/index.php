<?php

require realpath(dirname(dirname(__FILE__))) . '/config/env.php';

// Include App & Initializer
require_once BASE_DIR . 'app/library/Initializer.php';
require_once BASE_DIR . 'app/library/Application.php';

$application = new Cetraria\Library\Application;
$application->init();


var_dump($application);


var_dump(class_exists('Abc\Cde'));
