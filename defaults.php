<?php

return [
    /**
     * Date & time settings
     */
    'dateTime' => [
        'timezone' => 'UTC',
    ],

    /**
     * View settings
     */
    'views' => [
        // Main view settings
        'path' => __DIR__ . '/views',
        'folders' => [],
        'extensions' => [],
    ],

    /**
     * Debugging
     */
    // If true, error reporting will be set as E_ALL and display_errors turned on
    'debugging' => [
        'enabled' => true,
        // Only used when debugging is disabled. If enabled, E_ALL will be used
        'logLevel' => E_ERROR,
    ],

    /**
     * Database settings
     */
    'database' => [
        'connection' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'database',
            'username'  => 'root',
            'password'  => 'password',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'lazy'      => false,
            'options'   => [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
            ]
        ],
    ],

    // Validation
    'ensure' => [
        'rulesets' => [],
        'validators' => [],
    ],
];
