<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

/**
 * Created by Serhii Borodai <clarifying@gmail.com>
 */
declare(strict_types=1);

namespace Peth\Daemon;


use Peth\Config\RedisConfig as RedisConfig;
use EthereumRPC\API\Eth;
use Psr\Log\LoggerInterface;
use Redis;


class BlockAnnouncer implements DaemonInterface, RedisInteractionInterface
{

    use RedisInteractionTrait;

    /**
     * @var Eth
     */
    private $eth;

    /**
     * @var int
     */
    private $connectionTimeoutTreshhold;
    /**
     * @var int
     */
    private $announcePeriod;
    /**
     * @var \Redis
     */
    private $redis;
    /**
     * @var RedisConfig
     */
    private $redisConfig;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BlockAnoncer constructor.
     * @param Eth $eth
     * @param Redis $redis
     * @param RedisConfig $redisConfig
     * @param LoggerInterface $logger
     * @param int $announcePeriod
     * @param int $connectionTimeoutTreshhold
     */
    public function __construct(LoggerInterface $logger,
                                Eth $eth,
                                Redis $redis,
                                RedisConfig $redisConfig,
                                $announcePeriod = 15,
                                $connectionTimeoutTreshhold = 600)
    {

        $this->eth = $eth;
        $this->connectionTimeoutTreshhold = $connectionTimeoutTreshhold;
        $this->announcePeriod = $announcePeriod;
        $this->redis = $redis;
        $this->redisConfig = $redisConfig;
        $this->logger = $logger;

        $this->connectRedis($this->redis, $this->redisConfig);
    }

    public function process()
    {
//        $connectionTimeOut = 0;
        while (true) {
            sleep($this->announcePeriod);
//            while ($connectionTimeOut < $this->connectionTimeoutTreshhold) {
                try {
                    $counter = 0;
                    $this->redis->ping();
                    $ethLastBlock = (string) $this->eth->blockNumber();
                    //$announced = $this->redis->lIndex(self::class, 0);
                    //@todo move get/set last announced block to separate method
                    $announced = $this->redis->get(self::class . 'announced');
                    $this->logger->info(sprintf('announced is %s, ETH last block is %s', $announced, $ethLastBlock));

                    while ($ethLastBlock > $announced) {
                        $announceBucket = ($ethLastBlock - $announced < 1000 ? $ethLastBlock : bcadd(1000 , $announced,0));

                        $this->logger->info(sprintf('bucket from %s to %s', $announced, $announceBucket));

                        $pushed = $this->redisLPush(self::class, range(bcadd($announced, 1, 0), $announceBucket));
                        if ($pushed === false) {
                            throw new \Exception(sprintf("Can't push announce bucket %s", $announceBucket));
                        }
                        $this->logger->info(sprintf("announced %s blocks", bcsub($announceBucket, $announced,0)));

                        $announced = $announceBucket;
                        //@todo move get/set last announced block to separate method
                        $this->redis->set(self::class . 'announced', $announced);
                        $counter ++;
                    }

//                    $connectionTimeOut = 0;
                    $this->logger->info(sprintf('Done %s iterations', $counter));
                } catch (\RedisException $e) {
                    $this->connectRedis($this->redis, $this->redisConfig);
                } catch (\Exception $exception) {
                    $this->logger->error($exception->getMessage());
                }
//            }
        }
    }

    /**
     * @return Eth
     */
    public function getEth(): Eth
    {
        return $this->eth;
    }

    /**
     * @return int
     */
    public function getConnectionTimeoutTreshhold(): int
    {
        return $this->connectionTimeoutTreshhold;
    }

    /**
     * @return int
     */
    public function getAnnouncePeriod(): int
    {
        return $this->announcePeriod;
    }

    /**
     * @return RedisConfig
     */
    public function getRedisConfig(): RedisConfig
    {
        return $this->redisConfig;
    }

    public function getRedis(): Redis
    {
        return $this->redis;
    }

}