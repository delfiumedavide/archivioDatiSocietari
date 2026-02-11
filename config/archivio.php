<?php

return [
    'upload' => [
        'max_size_mb' => (int) env('UPLOAD_MAX_SIZE_MB', 50),
        'allowed_types' => explode(',', env('ALLOWED_FILE_TYPES', 'pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,p7m')),
        'allowed_mimes' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'image/jpeg',
            'image/png',
            'application/zip',
            'application/pkcs7-mime',
        ],
    ],
    'expiration' => [
        'warning_days' => 30,
        'check_time' => '08:00',
    ],
    'rate_limit' => [
        'login_attempts' => (int) env('RATE_LIMIT_LOGIN_ATTEMPTS', 5),
        'login_decay_minutes' => (int) env('RATE_LIMIT_LOGIN_DECAY_MINUTES', 1),
    ],
];
