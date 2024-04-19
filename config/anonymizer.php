<?php

return [
    'locale' => 'en_US',
    'chunk_size' => 1000,
    'models_path' => app_path('Models'),
    'models_namespace' => '\\App\\Models',

    /*
    |--------------------------------------------------------------------------
    | Blocked Environments
    |--------------------------------------------------------------------------
    |
    | Support multiple environments that require a prompt before anonymizing.
    | By default the listed blocked environments are forcibly blocked for extra safety.
    |
    */
    'blocked_env' => ['production'],
    'force_blocked_env' => true,

    /*
    | Model Ordering
    |--------------------------------------------------------------------------
    |
    | Optionally specify the order of anonymization, these Models will be anonymized first.
    |
    */
    'ordered_models' => []
];