<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

/**
 * Created by Serhii Borodai <clarifying@gmail.com>
 */

return [
    'dependencies' => [
        'factories' => [
            \Peth\Config\RedisConfig::class => \Peth\Config\RedisConfigFactory::class,
        ]
    ],
    'redis' => [
        'host' => getenv('PGTW_REDIS') ?: 'redis',
        'port' => getenv('PGTW_REDIS_PORT') ?: '6379',
        'db'   => getenv('PGTW_REDIS_DB') ?: '2',
        'timeout' => '0',
    ],
];