<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 18:27
 */

namespace Holder\Route;


use Holder\Util\Route\Handler;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class IndexGET extends Handler
{
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface
    {
        $response = $this->view->render($response, 'index.twig', [
            'name' => 'Henrique'
        ]);

        return $response;
    }
}