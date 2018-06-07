<?php
/**
 * Copyright Serhii Borodai (c) 2017-2018.
 */

/**
 * Created by Serhii Borodai <clarifying@gmail.com>
 */

namespace Daemon;


use EthereumRPC\API\Eth;
use Config\RedisConfig as RedisConfig;
use Psr\Log\LoggerInterface;

class BlockReader implements DaemonInterface, RedisInteractionInterface
{
    use RedisInteractionTrait;

    /**
     * @var Eth
     */
    private $eth;
    /**
     * @var RedisConfig
     */
    private $redisConfig;
    /**
     * @var \Redis
     */
    private $redis;
    /**
     * @var string
     */
    private $redisListKey;
    /**
     * @var int
     */
    private $timeoutOnEmptyList;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BlockReader constructor.
     * @param LoggerInterface $logger
     * @param Eth $eth
     * @param \Redis $redis
     * @param RedisConfig $redisConfig
     * @param string $redisListKey redis list keyName where blocks numbers are stored
     * @param int $timeoutOnEmptyList timeout if redis list of blocks is empty
     */
    public function __construct(LoggerInterface $logger, Eth $eth, \Redis $redis, RedisConfig $redisConfig, $redisListKey = BlockAnnouncer::class, $timeoutOnEmptyList = 5)
    {

        $this->eth = $eth;
        $this->redisConfig = $redisConfig;
        $this->redis = $redis;
        $this->redisListKey = $redisListKey;
        $this->timeoutOnEmptyList = $timeoutOnEmptyList;
        $this->logger = $logger;
    }

    /**
     * @throws \Exception
     */
    public function process()
    {
        while (true) {
            try {
                $this->redis->ping();
                $blockToParse = $this->redis->rPop($this->redisListKey);
                if (!$blockToParse) {
                    sleep($this->timeoutOnEmptyList);
                } else {
                    try {
                        $block = $this->eth->getBlock($blockToParse);
                        $this->logger->info(sprintf('Process block %s', $block->number()));
                        $this->logger->info(sprintf('Found %d transactions', count($block->transactions)));
                        if (count($block->transactions) > 0) {
                            $pushed = $this->redisLPush(self::class, $block->transactions);
                            if ($pushed === false) {
                                throw new \Exception(sprintf("Can't push transactions %s",
                                    json_encode($block->transactions)));
                            }
                        }
                    } catch (\RedisException $e) {
                        $this->logger->error($e->getMessage());
                        throw new \Exception(sprintf("Can't push transactions %s",
                            json_encode($block->transactions)));
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                        $this->logger->alert(sprintf('return block %s to reparse', $blockToParse));
                        $this->redisLPush($this->redisListKey, [$blockToParse]);
                    }
                }
            } catch (\RedisException $exception) {
                $this->connectRedis($this->redis, $this->redisConfig);
            }
        }
    }
}