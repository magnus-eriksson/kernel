<?php
namespace Kernel\Views;

use Kernel\Kernel;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class Helpers implements ExtensionInterface
{
    /**
     * @var Kernel
     */
    protected Kernel $kernel;


    /**
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }


    /**
     * @param Engine $engine
     *
     * @return void
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('route', [$this->kernel->router, 'getRoute']);
        $engine->registerFunction('csrfToken', [$this->kernel->csrf, 'get']);
        $engine->registerFunction('queryString', [$this, 'queryString']);
    }


    /**
     * Get the current query string
     *
     * @param  array  $add
     * @param  array  $remove
     * @return string
     */
    public function queryString(array $add = [], array $remove = [])
    {
        $qs     = $this->kernel->request->server->get('QUERY_STRING');
        $values = [];

        if ($qs) {
            parse_str($qs, $values);
        }

        foreach ($remove as $rmKey) {
            if (array_key_exists($rmKey, $values)) {
                unset($values[$rmKey]);
            }
        }

        $values = array_replace($values, $add);

        return $values
            ? '?' . http_build_query($values)
            : '';
    }
}
