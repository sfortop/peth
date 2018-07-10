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
        'aliases' => [
             \Humus\Amqp\JsonProducer::class => 'incoming-transactions',
        ],
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
                    'host' => getenv('PGTW_RMQ_HOST')?: 'rabbitmq',
                    'port' => getenv('PGTW_RMQ_PORT')?: 5672,
                    'login' => getenv('PGTW_RMQ_USER') ?:'guest',
                    'password' => getenv('PGTW_RMQ_PASS') ?:'guest',
                    'vhost' => '/',
                    'persistent' => false,
                    'read_timeout' => 3, //sec, float allowed
                    'write_timeout' => 1, //sec, float allowed
                ],
            ],
            'exchange' => [
                //current behavior of monitoring worker
                '' => [
                    'name' => '',
                    'connection' => 'default-amqp-connection',
                    'type' => 'direct',
                ],
                'incoming-transactions' => [
                    'name' => 'incoming-transactions',
                    'type' => 'direct',
                    'connection' => 'default-amqp-connection',
                    'auto_setup_fabric' => true,
                ],
            ],
            'producer' => [
                'incoming-transactions' => [
                    'type' => 'json',
                    'exchange' => "",
                ],
            ],
        ],
    ],
];

