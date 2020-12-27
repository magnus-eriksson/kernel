<?php
declare(strict_types = 1);

namespace Kernel\Entities;

use JsonSerializable;

class JsonResponseEntity implements JsonSerializable
{
    /**
     * @var bool
     */
    protected bool $success = true;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var array
     */
    protected array $errors = [];

    /**
     * @var string
     */
    protected string $message = '';


    /**
     * Get the value of success
     *
     * @return bool
     */
    public function getSuccess(): bool
    {
        return $this->success;
    }


    /**
     * Set the value of success
     *
     * @param bool $success
     *
     * @return self
     */
    public function setSuccess(bool $success): JsonResponseEntity
    {
        $this->success = $success;

        return $this;
    }


    /**
     * Get the value of data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * Set the value of data
     *
     * @param mixed $data
     *
     * @return self
     */
    public function setData($data): JsonResponseEntity
    {
        $this->data = $data;

        return $this;
    }


    /**
     * Get the value of errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }


    /**
     * Add errors
     *
     * @param array $errors
     *
     * @return self
     */
    public function addErrors(array $errors): JsonResponseEntity
    {
        $this->errors = array_merge($this->errors, $errors);

        return $this;
    }


    /**
     * Add an error
     *
     * @param string $error
     * @param string|int $index
     *
     * @return JsonResponseEntity
     */
    public function addError(string $error, $index = null): JsonResponseEntity
    {
        if ($index === null) {
            $this->errors[] = $error;
        } else {
            $this->errors[$index] = $error;
        }

        return $this;
    }


    /**
     * Get the value of message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }


    /**
     * Set the value of message
     *
     * @param string $message
     *
     * @return self
     */
    public function setMessage(string $message): JsonResponseEntity
    {
        $this->message = $message;

        return $this;
    }


    /**
     * Get this response as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'data'    => $this->data,
            'errors'  => $this->errors,
            'message' => $this->message,
        ];
    }


    /**
     * Data to be serialized
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }


    /**
     * If cast as a string, return it as stringified json
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }


    /**
     * Reset all values
     *
     * @return JsonResponseEntity
     */
    public function reset(): JsonResponseEntity
    {
        $this->success = true;
        $this->data    = null;
        $this->errors  = [];
        $this->message = '';

        return $this;
    }
}
