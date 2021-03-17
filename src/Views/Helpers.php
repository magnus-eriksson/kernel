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
    }
}
