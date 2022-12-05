<?php

namespace Kernel\Forms;

use Jsl\Ensure\Components\Result;
use Jsl\Ensure\Ensure;

class Form
{
    /**
     * @var array
     */
    public readonly array $data;

    /**
     * @var Ensure
     */
    public readonly Ensure $ensure;

    /**
     * @var Result|null
     */
    protected Result|null $result = null;


    /**
     * @param array $data
     * @param Ensure $ensure
     */
    public function __construct(array $data, Ensure $ensure)
    {
        $this->data = $data;
        $this->ensure = $ensure;
    }


    /**
     * Get form data
     *
     * @param string|int|null $key
     * @param mixed $fallback
     * 
     * @return mixed
     */
    public function data(string|int|null $key = null, mixed $fallback = null): mixed
    {
        return $key
            ? ($this->data[$key] ?? $fallback)
            : $this->data;
    }


    /**
     * Validate the form
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $result = $this->validate();

        return $result->isValid();
    }


    /**
     * Get any validation errors
     *
     * @param bool $onlyFirstErrorPerField
     * 
     * @return array
     */
    public function errors(bool $onlyFirstErrorPerField = true): array
    {
        $result = $this->validate();

        return $result->getErrors($onlyFirstErrorPerField);
    }


    /**
     * Validate the data (if not already validated) and return the result
     *
     * @return Result
     */
    protected function validate(): Result
    {
        return $this->result
            ? $this->result
            : $this->result = $this->ensure->validate();
    }
}
