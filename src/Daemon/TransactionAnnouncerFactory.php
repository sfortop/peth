<?php
/**
 * Copyright Serhii Borodai (c) 2018.
 */

/**
 * Created by Serhii Borodai <clarifying@gmail.com>
 */

namespace Daemon;


use Config\RedisConfig;
use EthereumRPC\API\Eth;
use Humus\Amqp\JsonProducer;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Zend\Hydrator\ClassMethods;

class TransactionAnnouncerFactory
{
    /**
     * @param ContainerInterface $container
     * @return TransactionAnnouncer
     */
    public function __invoke(ContainerInterface $container)
    {
        $logger = $container->get(LoggerInterface::class);
        $eth = $container->get(Eth::class);
        $redis = $container->get(\Redis::class);
        $redisConfig = $container->get(RedisConfig::class);

        /** @var JsonProducer $producer */
        $producer = $container->get('incoming-transactions');
        return new TransactionAnnouncer(
            $logger,
            $eth,
            new ClassMethods(),
            $producer,
            $redis,
            $redisConfig,
            TransactionChecker::class
        );
    }

}