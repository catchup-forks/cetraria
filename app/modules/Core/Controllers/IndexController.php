<?php

namespace Cetraria\Modules\Core\Controllers;

use Phalcon\Mvc\Controller;

/**
 * Class IndexController
 * @package Cetraria\Modules\Core\Controllers
 *
 * @RoutePrefix("/", name="home")
 */
class IndexController extends Controller
{
    /**
     * @Route("/", methods={"GET"}, name="home")
     */
    public function indexAction()
    {

    }
}
