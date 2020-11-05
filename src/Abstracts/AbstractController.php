<?php
declare(strict_types=1);

namespace Kernel\Abstracts;

use Kernel\Kernel;
use Maer\Validator\TestSuite;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
    public function redirect(string $url, int $status = 302, array $headers = []): RedirectResponse
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
    public function getRoute(string $name, array $args = [], bool $prependBaseUrl = false): ?string
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
    public function redirectToRoute(string $name, array $args = [], int $status = 302, array $headers = []): RedirectResponse
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
    public function render(string $template, array $data = []): string
    {
        return static::$kernel->render($template, $data);
    }


    /**
     * Validate data
     *
     * @param array $data
     * @param array $rules
     *
     * @return TestSuite
     */
    public function validate(array $data, array $rules): TestSuite
    {
        return static::$kernel->validator->make($data, $rules);
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
