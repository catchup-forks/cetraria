<?php

use Cetraria\Library\Application;

require realpath(dirname(dirname(__FILE__))) . '/config/env.php';

$application = new Application;
$application->init()->run();
