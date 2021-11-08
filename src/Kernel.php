<?php
declare(strict_types=1);

namespace Kernel;

use Database\Connection;
use Database\Connectors\ConnectionFactory;
use Illuminate\Container\Container;
use Kernel\Abstracts\AbstractController;
use Kernel\Entities\JsonResponseEntity;
use Kernel\Routing\Router;
use Kernel\Security\Csrf;
use Kernel\Utils\Slugify;
use Kernel\Validators\DatabaseSet;
use Kernel\Views\Helpers;
use League\Plates\Engine;
use Maer\Config\Config;
use Maer\Validator\Factory as ValidationFactory;
use Maer\Validator\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @property Container $ioc
 * @property Config $config
 * @property Router $router
 * @property Request $request
 * @property Session $session
 * @property Engine $views
 * @property Factory $validator
 * @property Connection $db
 * @property Slugify $slugify
 * @property Csrf $csrf
 */
class Kernel
{
    /**
     * @var Container
     */
    protected Container $ioc;


    /**
     * @param array $configs
     */
    public function __construct(array $configs = [])
    {
        /**
         * Container
         */
        $this->ioc = new Container;
        $this->ioc->singleton(Container::class, fn (): Container => $this->ioc);
        $this->ioc->alias(Container::class, 'ioc');

        /**
         * Kernel
         */
        $this->ioc->singleton(Kernel::class, fn (): Kernel => $this);

        /**
         * Config
         */
        $configs = [__DIR__ . '/../defaults.php', ...$configs];
        $this->ioc->singleton(Config::class, fn (): Config => new Config($configs));
        $this->ioc->alias(Config::class, 'config');

        /**
         * Set up the environment
         * -----------------------------------------------------
         * This can't be done before the configs are loaded
         */
        // Timezone
        date_default_timezone_set($this->config->get('dateTime.timezone', 'UTC'));

        // Debug settings
        (function (array $debug) {
            if ($debug['enabled']) {
                ini_set('display_errors', '1');
                error_reporting(E_ALL);
            } else {
                ini_set('display_errors', '0');
                error_reporting($debug['errorReporting']);
            }
        })($this->config->get('debugging'));

        /**
         * Router
         */
        $this->ioc->singleton(Router::class, function ($ioc): Router {
            return (new Router($ioc->request))->resolver(function ($cb) use ($ioc) {
                return [$ioc->make($cb[0]), $cb[1]];
            });
        });
        $this->ioc->alias(Router::class, 'router');

        /**
         * Request
         */
        $this->ioc->singleton(Request::class, fn (): Request => Request::createFromGlobals());
        $this->ioc->alias(Request::class, 'request');

        /**
         * Session
         */
        $this->ioc->singleton(Session::class, function (): Session {
            $session = new Session;
            $session->start();

            return $session;
        });
        $this->ioc->alias(Session::class, 'session');

        /**
         * Views
         */
        $this->ioc->singleton(Engine::class, function (Container $ioc): Engine {
            $engine = new Engine($ioc->config->get('views.path'));
            $engine->loadExtension($ioc->make(Helpers::class));

            foreach ($ioc->config->get('views.folders', []) as $name => $folder) {
                $engine->addFolder($name, $folder);
            }

            foreach ($ioc->config->get('views.extensions', []) as $ext) {
                $engine->loadExtension($ioc->make($ext));
            }

            return $engine;
        });
        $this->ioc->alias(Engine::class, 'views');

        /**
         * Validation
         */
        $this->ioc->singleton(ValidationFactory::class, function ($ioc): ValidationFactory {
            $factory = new ValidationFactory;
            return $factory;
        });
        $this->ioc->alias(ValidationFactory::class, 'validator');

        /**
         * Database
         */
        $dbSettings = $this->config->get('database.connection', []);
        $this->ioc->singleton(Connection::class, fn (): Connection => (new ConnectionFactory)->make($dbSettings));
        $this->ioc->alias(Connection::class, 'db');

        /**
         * Controller
         */
        // Pass the kernel to the abstract controller to have easy access to the base dependencies
        // in the controllers. This is the only place it should be injected.
        AbstractController::setKernel($this);

        /**
         * Slugify
         */
        $this->ioc->singleton(Slugify::class);
        $this->ioc->alias(Slugify::class, 'slugify');

        /**
         * CSRF
         */
        $this->ioc->singleton(Csrf::class, fn () => new Csrf($this->session));
        $this->ioc->alias(Csrf::class, 'csrf');
    }


    /**
     * Get a configuration value
     *
     * @param string|null $key
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function config($key = null, $fallback = null)
    {
        return $this->config->get($key, $fallback);
    }


    /**
     * Add routes
     *
     * @param callable $callback
     * @param array $groupSettings
     *
     * @return self
     */
    public function addRoutes(callable $callback, array $groupSettings = []): Kernel
    {
        $this->router->group($groupSettings, $callback);

        return $this;
    }


    /**
     * Add a route
     *
     * @param string $method
     * @param string $pattern
     * @param mixed $callback
     * @param array $options
     *
     * @return Router
     */
    public function route(string $method, string $pattern, $callback, array $options = []): Router
    {
        return $this->router->add($method, $pattern, $callback, $options);
    }


    /**
     * Get a parameter from the query string
     *
     * @param mixed $key
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function queryParam($key, $fallback = null)
    {
        return $this->request->query->get($key, $fallback);
    }


    /**
     * Get a post parameter
     *
     * @param mixed $key
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function postParam($key, $fallback = null)
    {
        return $this->request->request->get($key, $fallback);
    }


    /**
     * Redirect the request
     *
     * @param string $url
     * @param int $status
     * @param array $headers
     *
     * @return RedirectResponse
     */
    public function redirect(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        return new RedirectResponse($url, $status, $headers);
    }


    /**
     * Redirect to a named route
     *
     * @param string $url
     * @param int $status
     * @param array $headers
     *
     * @return RedirectResponse
     */
    public function redirectToRoute(string $name, array $args = [], int $status = 302, array $headers = []): RedirectResponse
    {
        return new RedirectResponse($this->router->getRoute($name, $args), $status, $headers);
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
        return $this->views->render($template, $data);
    }


    /**
     * Validate data
     *
     * @param array $data
     * @param array $rules
     * @param array $fieldMessages Custom field messages
     *
     * @return Validator
     */
    public function validate(array $data, array $rules, array $fieldMessages = []): Validator
    {
        return $this->validator->create($data, $rules, $fieldMessages);
    }


    /**
     * Get the response object
     *
     * @return Response
     */
    public function getResponse(string $method = null, string $path = null): Response
    {
        $response = $this->router->dispatch($method, $path);

        if ($response instanceof JsonResponseEntity) {
            $response = new JsonResponse($response, http_response_code());
        }

        if ($response instanceof Response === false) {
            $response = new Response($response, http_response_code());
        }

        return $response;
    }


    /**
     * Execute the router and get started
     *
     * @return void
     */
    public function start(string $method = null, string $path = null): void
    {
        $response = $this->getResponse($method, $path);

        $response->send();
    }


    /**
     * Get an instance from the container
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get(string $property)
    {
        if ($this->ioc->isAlias($property) === false) {
            $prop = __CLASS__ . '::' . $property;
            trigger_error("Undefined property: $prop", E_USER_NOTICE);
        }

        return $this->ioc->make($property);
    }
}
