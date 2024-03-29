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

use Phalcon\Mvc\Controller;

/**
 * Error Controller
 * @package Cetraria\Modules\Core\Controllers
 */
class ErrorController extends Controller
{
    public function indexAction()
    {
        $this->tag->setTitle('Unexpected error');

        /** @var \Phalcon\Error\Error $error */
        $error = $this->dispatcher->getParam('error');

        switch ($error->type()) {
            case 404:
                $code = 404;
                break;
            case 403:
                $code = 403;
                break;
            case 401:
                $code = 401;
                break;
            default:
                $code = 500;
        }

        $this->response->resetHeaders()->setStatusCode($code, null);

        $this->view->setVars([
            'code'  => $code,
            'error' => $error,
            'debug' => ENV_DEVELOPMENT === APPLICATION_ENV
        ]);
    }

    public function route404Action()
    {
        $this->tag->setTitle('Not Found');
        $this->response->resetHeaders()->setStatusCode(404, 'Not Found');
        $this->view->pick('error/404');
    }
}
