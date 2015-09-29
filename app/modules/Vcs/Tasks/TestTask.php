<?php

namespace Cetraria\Modules\Vcs\Tasks;

class TestTask extends \Phalcon\CLI\Task
{
    public function mainAction()
    {
        echo "Vcs action ...", PHP_EOL;
    }
}
