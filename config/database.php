<?php

return [
    'default' => env('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL', env('MYSQL_URL')),
            'host' => env('DB_HOST', env('MYSQLHOST', '127.0.0.1')),
            'port' => env('DB_PORT', env('MYSQLPORT', '3306')),
            'database' => env('DB_DATABASE', env('MYSQLDATABASE', 'archivio_societario')),
            'username' => env('DB_USERNAME', env('MYSQLUSER', 'archivio_user')),
            'password' => env('DB_PASSWORD', env('MYSQLPASSWORD', '')),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => 'InnoDB',
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
    ],
    'migrations' => [
        'table' => 'migrations',
        'update_date_on_migration' => true,
    ],
    'redis' => [
        'client' => env('REDIS_CLIENT', 'predis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', 'archivio_'),
        ],
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', env('REDISHOST', '127.0.0.1')),
            'username' => env('REDIS_USERNAME', env('REDISUSER')),
            'password' => env('REDIS_PASSWORD', env('REDISPASSWORD')),
            'port' => env('REDIS_PORT', env('REDISPORT', '6379')),
            'database' => env('REDIS_DB', '0'),
        ],
        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', env('REDISHOST', '127.0.0.1')),
            'username' => env('REDIS_USERNAME', env('REDISUSER')),
            'password' => env('REDIS_PASSWORD', env('REDISPASSWORD')),
            'port' => env('REDIS_PORT', env('REDISPORT', '6379')),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],
];
