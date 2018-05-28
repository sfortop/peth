<?php
/**
 * Copyright Serhii Borodai (c) 2018.
 */

/**
 * Created by Serhii Borodai <serhii.borodai@globalgames.net>
 */

namespace Daemon;


use Config\RedisConfig;
use EthereumRPC\API\Eth;
use Infrastructure\Exception\InvalidConfigException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class TransactionReaderFactory
{
    /**
     * @param ContainerInterface $container
     * @return TransactionReader
     * @throws InvalidConfigException
     */
    public function __invoke(ContainerInterface $container)
    {
        $logger = $container->get(LoggerInterface::class);
        $eth = $container->get(Eth::class);
        $redis = $container->get(\Redis::class);
        $redisConfig = $container->get(RedisConfig::class);
        if (!isset($container->get('config')['ethereum']['contracts']['DMT'])) {
            throw new InvalidConfigException('DMT contract address not found');
        }
        $contractAddress = $container->get('config')['ethereum']['contracts']['DMT'];


        return new TransactionReader(
            $logger,
            $eth,
            $redis,
            $redisConfig,
            BlockReader::class,
            $contractAddress
        );
    }


}