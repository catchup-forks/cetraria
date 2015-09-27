<?php

use Cetraria\Library\Application;

require realpath(dirname(dirname(__FILE__))) . '/config/env.php';

// Include App & Initializer
require_once DOCROOT . 'app/library/Initializer.php';
require_once DOCROOT . 'app/library/Application.php';

$application = new Application;
echo $application->init()->run();
