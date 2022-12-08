<?php

declare(strict_types=1);

namespace Kernel\Abstracts;

use Database\Connection;
use Illuminate\Container\Container;
use Jsl\Ensure\Components\Result;
use Jsl\Ensure\Ensure;
use Jsl\Ensure\EnsureFactory;
use Kernel\Entities\JsonResponseEntity;
use Kernel\Kernel;
use Kernel\Routing\Router;
use Kernel\Security\Csrf;
use Kernel\Utils\Paths;
use Kernel\Utils\Slugify;
use League\Plates\Engine;
use Maer\Config\Config;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @property Container $ioc
 * @property Config $config
 * @property Router $router
 * @property Request $request
 * @property Session $session
 * @property Engine $views
 * @property Connection $db
 * @property Slugify $slugify
 * @property Csrf $csrf
 * @property Paths $paths
 * @property EnsureFactory $ensure
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
     * Get a new json response entity instance
     *
     * @return JsonResponseEntity
     */
    protected function jsonResponseEntity(): JsonResponseEntity
    {
        return new JsonResponseEntity;
    }


    /**
     * Add a flash message
     *
     * @param string $key
     * @param string $message
     *
     * @return self
     */
    protected function addFlash(string $key, string $message): self
    {
        $this->session->getFlashBag()->add($key, $message);

        return $this;
    }


    /**
     * Get flash messages
     *
     * @param string $key
     * 
     * @return array
     */
    protected function getFlash(string $key): array
    {
        return $this->session->getFlashBag()->get($key, []);
    }


    /**
     * Create a new Ensure validation instance
     *
     * @param array $data
     * @param string|array $rules
     *
     * @return Ensure
     */
    public function ensure(array $data, string|array $rules = []): Ensure
    {
        return $this->ensure->create($rules, $data);
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
