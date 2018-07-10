<?php
/**
 * Copyright Serhii Borodai (c) 2018.
 */

use Humus\Amqp\Driver\Driver;

/**
 * Created by Serhii Borodai <clarifying@gmail.com>
 */

return [
    'dependencies' => [
        'factories' => [
            Driver::class => \Humus\Amqp\Container\DriverFactory::class,
            'default-amqp-connection' => [\Humus\Amqp\Container\ConnectionFactory::class, 'default'],
            'incoming-transactions' => [\Humus\Amqp\Container\ProducerFactory::class, 'incoming-transactions'],
        ],
    ],
    'humus' => [
        'amqp' => [
            'driver' => 'php-amqplib',
            'connection' => [
                //@fixme set correct config
                'default' => [
                    'type' => 'socket',
                    'host' => 'rabbitmq',
                    'port' => 5672,
                    'login' => 'guest',
                    'password' => 'guest',
                    'vhost' => '/',
                    'persistent' => false,
                    'read_timeout' => 3, //sec, float allowed
                    'write_timeout' => 1, //sec, float allowed
                ],
            ],
            'exchange' => [
                //@fixme place correct exchange
                'my-exchange' => [
                    'name' => 'my-exchange',
                    'type' => 'direct',
                    'connection' => 'default-amqp-connection',
                    'auto_setup_fabric' => true,
                ],
            ],
            'producer' => [
                'incoming-transactions' => [
                    'type' => 'json',
                    //@fixme place exchange name from above
                    'exchange' => 'my-exchange',
                ],
            ],
        ],
    ],
];

