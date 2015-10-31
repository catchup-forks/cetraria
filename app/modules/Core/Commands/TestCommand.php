<?php

namespace Cetraria\Modules\Core\Commands;

use Cetraria\Console\Commands\Command;

class TestCommand extends Command
{
    public function onConstruct()
    {
        echo __METHOD__, PHP_EOL;
    }
}
