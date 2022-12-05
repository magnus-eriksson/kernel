<?php

namespace Kernel\Forms;

use InvalidArgumentException;
use Jsl\Ensure\EnsureFactory;
use Kernel\Security\Csrf;
use Maer\Config\ConfigInterface;
use Symfony\Component\HttpFoundation\Request;

class FormFactory
{
    /**
     * @var EnsureFactory
     */
    protected EnsureFactory $ensure;

    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $config;

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var Csrf
     */
    protected Csrf $csrf;


    /**
     * @param EnsureFactory $ensure
     * @param ConfigInterface $config
     * @param Request $request
     * @param Csrf $csrf
     */
    public function __construct(EnsureFactory $ensure, ConfigInterface $config, Request $request, Csrf $csrf)
    {
        $this->ensure = $ensure;
        $this->config = $config;
        $this->request = $request;
        $this->csrf = $csrf;
    }


    /**
     * Get a form instance
     *
     * @param string $name
     * @param string $method POST or GET
     *
     * @return Form
     */
    public function form(string $name, string $method = 'POST'): Form
    {
        $method = strtolower($method);
        if ($method != 'post' && $method != 'get') {
            throw new InvalidArgumentException("Method must be either POST or GET. Got: {$method}");
        }

        $request = $method === 'post'
            ? $this->request->request
            : $this->request->query;

        $ensure = $this->ensure->create([], []);
        $config = $this->config->get('forms.' . $name, []);
        $data = [];

        // Iterate through the form settings and populate Ensure & fetch the data
        foreach ($config['fields'] ?? [] as $field => $info) {
            if ($request->has($field)) {
                $data[$field] = $request->get($field);
                $ensure->setFieldValue($field, $data[$field]);
            }

            if (empty($info)) {
                continue;
            }

            if (is_array($info['rules'] ?? null)) {
                $ensure->setFieldRules($field, $info['rules']);
            }

            if (is_string($info['error'] ?? null) && strlen($info['error']) > 0) {
                $ensure->setFieldTemplate($field, $info['error']);
            }
        }

        // If we have a CSRF field name, let's use it
        if (isset($config['csrf']) && $config['csrf']) {
            $data[$config['csrf']] = $request->get($config['csrf'], '');
            $ensure->setFieldRule($config['csrf'], 'in', $this->csrf->get($config['csrf']))
                ->setFieldTemplate($config['csrf'], 'The form has expired. Reload the page and try again.');
        }


        return new Form($data, $ensure);
    }
}
