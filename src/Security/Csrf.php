<?php

namespace Kernel\Security;

use Symfony\Component\HttpFoundation\Session\Session;

class Csrf
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var array
     */
    protected $tokens = [];

    /**
     * @var string
     */
    protected $name;


    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->name    = 'kt_' . md5($session->getId());
        $this->tokens  = $session->get($this->name, []);
    }


    /**
     * Get a token
     *
     * @param string $name
     * @param bool $regenerate If set to true, the token will be regenerated
     *
     * @return string
     */
    public function get(string $name, bool $regenerate = false): string
    {
        $key = $this->getKey($name);

        if ($regenerate || $this->has($name) === false) {
            $this->tokens[$key] = $this->generateRandomToken();
            $this->save();
        }

        return $this->tokens[$key];
    }


    /**
     * Validate a token
     *
     * @param string $name
     * @param string $token
     *
     * @return bool
     */
    public function match(string $name, string $token): bool
    {
        return $this->has($name)
            && $this->tokens[$this->getKey($name)] === $token;
    }


    /**
     * Check if a csrf token exists
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return key_exists($this->getKey($name), $this->tokens);
    }


    /**
     * Gerenrate a random token
     *
     * @return string
     */
    public function generateRandomToken(): string
    {
        return bin2hex(random_bytes(64));
    }


    /**
     * Remove a csrf token
     *
     * @param string $name
     *
     * @return void
     */
    public function remove(string $name): void
    {
        if ($this->has($name)) {
            unset($this->tokens[$this->getKey($name)]);
            $this->save();
        }
    }


    /**
     * Get name
     *
     * @param string $name
     *
     * @return string
     */
    protected function getKey(string $name): string
    {
        return 't_' . md5($name . $this->session->getId());
    }


    /**
     * Store the tokens in session
     *
     * @return void
     */
    protected function save(): void
    {
        $this->session->set($this->name, $this->tokens);
    }
}
