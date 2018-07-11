<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

/**
 * Created by Serhii Borodai <clarifying@gmail.com>
 */
declare(strict_types=1);

namespace Peth\Config;


use Peth\Infrastructure\Exception\InvalidConfigException;
use Psr\Container\ContainerInterface;

class RedisConfigFactory
{

    /**
     * @param ContainerInterface $container
     * @return RedisConfig
     * @throws InvalidConfigException
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        if (!isset($config['redis'])) {
            throw new InvalidConfigException("No 'redis' configuration");
        }

        if (!isset($config['redis']['host'])) {
            throw new InvalidConfigException("redis 'host' mandatory");
        }

        $options[] = $config['redis']['host'];

        if (isset($config['redis']['port'])) {
            $options[] = $config['redis']['port'];
        }

        if (isset($config['redis']['timeout'])) {
            $options[] = $config['redis']['timeout'];
        }

        return new RedisConfig(...$options);
    }

}