<?php

namespace Kernel\Validation;

use Illuminate\Container\Container;
use Jsl\Ensure\Contracts\ResolverMiddlewareInterface;

class Resolver implements ResolverMiddlewareInterface
{
    /**
     * @var Container
     */
    protected Container $ioc;


    /**
     * @param Container $ioc
     */
    public function __construct(Container $ioc)
    {
        $this->ioc = $ioc;
    }


    /**
     * @inheritDoc
     */
    public function resolveClass(string $className): object
    {
        return $this->ioc->make($className);
    }
}
