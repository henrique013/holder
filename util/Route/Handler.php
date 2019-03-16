<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 18:31
 */

namespace Holder\Util\Route;


use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class Handler
{
    /** @var Container */
    protected $container;
    /** @var \Slim\Views\Twig */
    protected $view;


    // constructor receives container instance
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->view = $container['view'];
    }


    public abstract function __invoke(Request $request, Response $response, array $args): ResponseInterface;
}