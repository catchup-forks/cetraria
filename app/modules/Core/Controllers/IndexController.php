<?php

/*
 +------------------------------------------------------------------------+
 | Cetraria                                                               |
 +------------------------------------------------------------------------+
 | Copyright (c) 2015 Serghei Iakovlev                                    |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to sadhooklay+cetraria@gmail.com so I can send you a copy immediately. |
 +------------------------------------------------------------------------+
*/

namespace Cetraria\Modules\Core\Controllers;

use Cetraria\Library\Mvc\BaseController;

/**
 * Class IndexController
 *
 * @package Cetraria\Modules\Core\Controllers
 *
 * @RoutePrefix("/", name="home")
 */
class IndexController extends BaseController
{
    /**
     * @Route("/", methods={"GET"}, name="home")
     */
    public function indexAction()
    {
        $this->tag->appendTitle('Welcome');
    }
}
