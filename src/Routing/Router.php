<?php
declare(strict_types=1);

namespace Kernel\Routing;

use Maer\Router\Router as RouterRouter;
use Symfony\Component\HttpFoundation\Request;

class Router extends RouterRouter
{
    /**
     * @var Request
     */
    protected Request $request;


    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    /**
     * Get matching route
     * -------------------------------
     * This overrides the original method to make sure the request instance
     * is always injected as first argument
     *
     * @param  string $method
     * @param  string $path
     *
     * @return object
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     */
    public function getMatch($method = null, $path = null)
    {
        $match = parent::getMatch($method, $path);
        array_unshift($match->args, $this->request);

        return $match;
    }
}
