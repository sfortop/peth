<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

/**
 * Created by Serhii Borodai <clarifying@gmail.com>
 */

namespace Daemon;


use Config\RedisConfig;
use Redis;

interface RedisInteractionInterface
{
    public function connectRedis(Redis $redis, RedisConfig $redisConfig);
}