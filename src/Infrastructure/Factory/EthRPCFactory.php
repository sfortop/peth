<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

/**
 * Created by Serhii Borodai <clarifying@gmail.com>
 */

namespace Infrastructure\Factory;


use EthereumRPC\EthereumRPC;
use \Infrastructure\Exception\InvalidConfigException;
use Psr\Container\ContainerInterface;

class EthRPCFactory
{

    /**
     * @param ContainerInterface $container
     * @return EthereumRPC
     * @throws InvalidConfigException
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        if (!isset($config['ethereum'])) {
            throw new InvalidConfigException("No 'ethereum' api configuration");
        }
        if (!isset($config['ethereum']['host'])) {
            throw new InvalidConfigException("No GETH node host specified");
        }

        $port = $config['ethereum']['port'] ?? 8545;

        return new EthereumRPC($config['ethereum']['host'], $port);
    }

}