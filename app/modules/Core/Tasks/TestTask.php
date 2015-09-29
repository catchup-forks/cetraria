<?php

namespace Cetraria\Modules\Core\Tasks;

class TestTask extends \Phalcon\CLI\Task
{
    public function mainAction()
    {
        echo "Core action ...", PHP_EOL;
    }
}
