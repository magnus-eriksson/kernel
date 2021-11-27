<?php

namespace Kernel\Utils;

class Paths
{
    /**
     * @var array
     */
    protected array $paths = [];


    /**
     * Set a path
     *
     * @param string $key
     * @param string $path
     * 
     * @return self
     */
    public function set(string $key, string $path): self
    {
        $this->paths[$key] = $path;

        return $this;
    }


    /**
     * Get a path
     *
     * @param string $key
     * @param string|null $default
     * 
     * @return string|null
     */
    public function get(string $key, ?string $default = null): ?string
    {
        return $this->has($key)
            ? $this->paths[$key]
            : $default;
    }


    /**
     * Check if a path exists
     *
     * @param string $key
     * 
     * @return boolean
     */
    public function has(string $key): bool
    {
        return key_exists($key, $this->paths);
    }
}
