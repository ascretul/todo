<?php

declare(strict_types=1);

return [
    'responses' => [
        400 => [
            'message' => 'Bad request: The request could not be understood by the server due to malformed syntax. ' .
                'The client should not repeat the request without modifications.',
            'model' => 'BadErrorModel'
        ],
        401 => [
            'message' => 'Unauthorized: You are not authorized to use this API without an api key. ' .
                'Provide your token in X-API-KEY header',
            'model' => 'UnauthorizedErrorModel'
        ],
        404 => [
            'message' => 'Not found: The requested resource could not be found on this server',
            'model' => 'MissingErrorModel'
        ],
        403 => [
            'message' => 'Forbidden: You do not have permission to access the resource on this server',
            'model' => 'ForbiddenErrorModel'
        ],
        500 => [
            'message' => 'Error: An internal server error occurred while delivering your request. ' .
                'Please contact our support team (' . env('TECH_SUPPORT_RECIPIENT') . ') if the problem persists',
            'model' => 'ServerErrorModel'
        ],
        'default' => [
            'message' => 'An unexpected error has occurred',
            'model' => 'DefaultErrorModel'
        ]
    ]
];
