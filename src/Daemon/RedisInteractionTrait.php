<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

/**
 * Created by Serhii Borodai <clarifying@gmail.com>
 */

namespace Daemon;


use Config\RedisConfig;

trait RedisInteractionTrait
{
    public function connectRedis(\Redis $redis, RedisConfig $redisConfig)
    {
        //@todo add database selection
        return $redis->pconnect($redisConfig->getHost(), $redisConfig->getPort(), $redisConfig->getTimeout(), $redisConfig->getPersistentId());
    }

    protected function redisLPush($key, $values)
    {
        return call_user_func_array([$this->redis, 'lPush'],
            array_merge([$key], $values));
    }

}