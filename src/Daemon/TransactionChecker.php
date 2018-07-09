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
use Infrastructure\DTO\Transaction;
use Psr\Log\LoggerInterface;
use Redis;
use Zend\Hydrator\ClassMethods;

class TransactionChecker implements DaemonInterface, RedisInteractionInterface
{
    use RedisInteractionTrait;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Eth
     */
    private $eth;
    /**
     * @var Redis
     */
    private $redis;
    /**
     * @var RedisConfig
     */
    private $redisConfig;
    /**
     * @var string
     */
    private $redisListKey;
    /**
     * @var int
     */
    private $timeoutOnEmptyList;
    /**
     * @var ClassMethods
     */
    private $hydrator;

    /**
     * TransactionChecker constructor.
     * @param LoggerInterface $logger
     * @param Eth $eth
     * @param ClassMethods $hydrator
     * @param Redis $redis
     * @param RedisConfig $redisConfig
     * @param string $redisListKey
     * @param int $timeoutOnEmptyList
     */
    public function __construct(
        LoggerInterface $logger,
        Eth $eth,
        ClassMethods $hydrator,
        \Redis $redis,
        RedisConfig $redisConfig,
        $redisListKey = TransactionReader::class,
        $timeoutOnEmptyList = 5
    )
    {
        $this->logger = $logger;
        $this->eth = $eth;
        $this->hydrator = $hydrator;
        $this->redis = $redis;
        $this->redisConfig = $redisConfig;
        $this->redisListKey = $redisListKey;
        $this->timeoutOnEmptyList = $timeoutOnEmptyList;

        $this->connectRedis($this->redis, $this->redisConfig);
    }

    public function process()
    {
        while (true) {
            try {
                $this->redis->ping();
                $transactionData = $this->redis->rPop($this->redisListKey);
                if (!$transactionData) {
                    sleep($this->timeoutOnEmptyList);
                } else {
                    try {
                        $tmp = json_decode($transactionData, true);
                        if (!$tmp) {
                            throw new \Exception(sprintf('Failed to decode transaction data json [%s]', $transactionData));
                        }
                        /** @var Transaction $transaction */
                        $transaction = $this->hydrator->hydrate($tmp, new Transaction());

                        $receipt = $this->eth->getTransactionReceipt($transaction->getHash());

                        if (hexdec($receipt->status)) {
                            $this->logger->info(sprintf('Push transaction %s with status [%s]', $receipt->transactionHash, $receipt->status));

                            $pushed = $this->redisLPush(self::class, [
                                json_encode($transaction)
                            ]);
                            if ($pushed === false) {
                                throw new \Exception(sprintf("Can't push receipt %s",
                                    $receipt->transactionHash));
                            }
                        } else {
                            $this->logger->info(sprintf('Skip transaction %s with status [%s]', $receipt->transactionHash, $receipt->status));


                        }
                    } catch (\RedisException $e) {
                        $this->logger->error($e->getMessage());
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                        $this->logger->alert(sprintf('return transaction %s to re-check', $transaction->getHash()));
                        $this->redisLPush($this->redisListKey, [$transactionData]);
                    }
                }
            } catch (\RedisException $exception) {
                $this->logger->error($exception->getMessage());
                $this->connectRedis($this->redis, $this->redisConfig);
            }
        }


    }
}