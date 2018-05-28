<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

/**
 * Created by Serhii Borodai <serhii.borodai@globalgames.net>
 */

namespace Daemon;


use Config\RedisConfig;
use Redis;

interface RedisInteractionInterface
{
    public function connectRedis(Redis $redis, RedisConfig $redisConfig);
}