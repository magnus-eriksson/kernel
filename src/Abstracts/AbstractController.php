<?php
declare(strict_types=1);

namespace Kernel\Abstracts;

use Illuminate\Container\Container;
use Kernel\Entities\JsonResponseEntity;
use Kernel\Kernel;
use Kernel\Routing\Router;
use League\Plates\Engine;
use Maer\Config\Config;
use Maer\Validator\Validator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Container $ioc
 * @property Config $config
 * @property Router $router
 * @property Request $request
 * @property Engine $views
 * @property Validator $validator
 */
abstract class AbstractController
{
    /**
     * @var Kernel
     */
    protected static Kernel $kernel;


    /**
     * Set Kernel instance
     *
     * @param Kernel $kernel
     *
     * @return void
     */
    public static function setKernel(Kernel $kernel)
    {
        static::$kernel = $kernel;
    }


    /**
     * Redirect
     *
     * @param string $url
     * @param int $status
     * @param array $headers
     *
     * @return RedirectResponse
     */
    protected function redirect(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        return static::$kernel->redirect($url, $status, $headers);
    }


    /**
     * Get the URL of a named route
     *
     * @param string $name
     * @param array $args
     * @param bool $prependBaseUrl
     *
     * @return string|null
     */
    protected function getRoute(string $name, array $args = [], bool $prependBaseUrl = false): ?string
    {
        return static::$kernel->router->getRoute($name, $args, $prependBaseUrl);
    }


    /**
     * Redirect to a named route
     *
     * @param string $name
     * @param array $args
     * @param int $status
     * @param array $headers
     *
     * @return RedirectResponse
     */
    protected function redirectToRoute(string $name, array $args = [], int $status = 302, array $headers = []): RedirectResponse
    {
        return static::$kernel->redirectToRoute($name, $args, $status, $headers);
    }


    /**
     * Render a view
     *
     * @param string $template
     * @param array $data
     *
     * @return string
     */
    protected function render(string $template, array $data = []): string
    {
        return static::$kernel->render($template, $data);
    }


    /**
     * Validate data
     *
     * @param array $data
     * @param array $rules
     *
     * @return Validator
     */
    protected function validate(array $data, array $rules): Validator
    {
        return static::$kernel->validator->create($data, $rules);
    }


    /**
     * Get a new json response entity instance
     *
     * @return JsonResponseEntity
     */
    protected function jsonResponseEntity(): JsonResponseEntity
    {
        return new JsonResponseEntity;
    }


    /**
     * Get instance from ioc by alias
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return static::$kernel->ioc->make($key);
    }
}
