<?php
/**
 * zumpay-billing
 *
 * @author Serhii Borodai <serhii.borodai@globalgames.net>
 */

use Infrastructure\Factory\LoggerFactory;

return [
    'dependencies' => [
        'factories' => [
            \Psr\Log\LoggerInterface::class => LoggerFactory::class,
        ],
        'invokables' => [
            LoggerFactory::class => LoggerFactory::class,
        ]
    ],
    'log' => [
        'exceptionhandler' => true,
        'errorhandler' => true,
        'fatal_error_shutdownfunction' => true,
        'writers' => [
            [
                'name' => \Zend\Log\Writer\Stream::class,
                'options' => [
                    "stream" => 'php://output',
                ]
            ],
//            [
//                'name' => \Zend\Log\Writer\Stream::class,
//                'options' => [
//                    "stream" => 'php://stderr',
//                ]
//            ],
//            [
//                'name' => \Zend\Log\Writer\Stream::class,
//                'options' => [
//                    "stream" => 'php://stdout',
//                ]
//            ],
        ],
    ]
];