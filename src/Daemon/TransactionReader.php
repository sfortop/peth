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
use EthereumRPC\Response\TransactionInputTransfer;
use Psr\Log\LoggerInterface;

class TransactionReader implements DaemonInterface, RedisInteractionInterface
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
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var string
     */
    private $redisListKey;
    /**
     * @var int
     */
    private $timeoutOnEmptyList;
    /**
     * @var \Redis
     */
    private $redis;
    /**
     * @var string
     */
    private $contractAddress;


    /**
     * TransactionReader constructor.
     * @param LoggerInterface $logger
     * @param Eth $eth
     * @param \Redis $redis
     * @param RedisConfig $redisConfig
     * @param string $redisListKey
     * @param string $contractAddress
     * @param int $timeoutOnEmptyList
     */
    public function __construct(
        LoggerInterface $logger,
        Eth $eth,
        \Redis $redis,
        RedisConfig $redisConfig,
        $redisListKey = BlockReader::class,
        $contractAddress = '',
        $timeoutOnEmptyList = 5)
    {
        $this->eth = $eth;
        $this->redisConfig = $redisConfig;
        $this->logger = $logger;
        $this->redisListKey = $redisListKey;
        $this->timeoutOnEmptyList = $timeoutOnEmptyList;
        $this->redis = $redis;
        $this->contractAddress = $contractAddress;
        $this->connectRedis($this->redis, $this->redisConfig);
    }

    public function process()
    {
        while (true) {
            try {
                $this->redis->ping();
                $txId = $this->redis->rPop($this->redisListKey);
                if (substr($txId, 0, 2) !== '0x') {
                    $this->logger->info(sprintf('skip incorrect id %s', $txId));
                } elseif (!$txId) {
                    sleep($this->timeoutOnEmptyList);
                } else {
                    try {
                        $transaction = $this->eth->getTransaction($txId);
                        $this->logger->info(sprintf('Process transaction %s', $transaction->hash));

                        $inputData = $transaction->input();
                        if ($inputData instanceof TransactionInputTransfer && $transaction->to == $this->contractAddress) {
                            $amount = bcdiv($inputData->wei(), bcpow('10', '8'));
                            $this->logger->info(sprintf('Found transfer amount %s to %s',
                                $amount,
                                $inputData->payee));
                            $pushed = $this->redisLPush(self::class, [
                                json_encode(['hash' => $txId, 'amount' => $amount, 'payee' => $inputData->payee])
                            ]);
                            if ($pushed === false) {
                                throw new \Exception(sprintf("Can't push transactions %s",
                                    $transaction->hash));
                            }
                        } elseif ($inputData instanceof TransactionInputTransfer) {
                            $this->logger->info(sprintf('Contract %s', $transaction->to));
                            $this->logger->info(sprintf('Found transfer amount %s to %s', $inputData->amount, $inputData->payee));
                        } else {
                            $this->logger->info(sprintf('skipped %s', $transaction->hash));
                        }
                    } catch (\RedisException $e) {
                        $this->logger->error($e->getMessage());
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                        $this->logger->alert(sprintf('return transaction %s to reparse', $txId));
                        $this->redisLPush($this->redisListKey, [$txId]);
                    }
                }
            } catch (\RedisException $exception) {
                $this->logger->error($exception->getMessage());
                $this->connectRedis($this->redis, $this->redisConfig);
            }
        }
    }
}