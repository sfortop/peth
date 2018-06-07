<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

/**
 * Created by Serhii Borodai <clarifying@gmail.com>
 */

namespace Config;


class RedisConfig
{
    protected $host;
    protected $port;
    protected $timeout;
    protected $persistentId;

    /**
     * Redis constructor.
     * @param $host
     * @param $port
     * @param $timeout
     * @param $persistentId
     */
    public function __construct($host, $port = 6379, $timeout = 0, $persistentId = self::class)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->persistentId = $persistentId;
    }


    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return mixed
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return mixed
     */
    public function getPersistentId()
    {
        return $this->persistentId;
    }

}