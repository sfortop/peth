<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

use Psr\Container\ContainerInterface;

/**
 * Created by Serhii Borodai <serhii.borodai@globalgames.net>
 */

return [
    'dependencies' => [
        'factories' => [
            \Config\RedisConfig::class => function (ContainerInterface $container) {
                return new \Config\RedisConfig(
                    'redis'
                );
            }
        ]
    ],
];