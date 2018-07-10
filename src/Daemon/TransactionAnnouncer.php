<?php
/**
 * peth
 *
 * @author Serhii Borodai <serhii.borodai@globalgames.net>
 */

namespace Daemon;


use Config\RedisConfig;
use EthereumRPC\API\Eth;
use Humus\Amqp\JsonProducer;
use Infrastructure\DTO\MonitoringMessage;
use Infrastructure\DTO\Transaction;
use Psr\Log\LoggerInterface;
use Zend\Hydrator\ClassMethods;

class TransactionAnnouncer implements DaemonInterface, RedisInteractionInterface
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
     * @var \Redis
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
     * @var string
     */
    private $currency;
    private $producer;
    /**
     * @var ClassMethods
     */
    private $hydrator;
    /**
     * @var string
     */
    private $routingKey;

    /**
     * TransactionAnnouncer constructor.
     * @param LoggerInterface $logger
     * @param Eth $eth
     * @param ClassMethods $hydrator
     * @param JsonProducer $producer
     * @param \Redis $redis
     * @param RedisConfig $redisConfig
     * @param string $redisListKey
     * @param int $timeoutOnEmptyList
     * @param string $currency
     * @param string $routingKey
     */
    public function __construct(
        LoggerInterface $logger,
        Eth $eth,
        ClassMethods $hydrator,
        JsonProducer $producer,
        \Redis $redis,
        RedisConfig $redisConfig,
        $redisListKey = TransactionChecker::class,
        $timeoutOnEmptyList = 5,
        $currency = 'DMT',
        $routingKey = 'incoming-transactions'
    )
    {

        $this->logger = $logger;
        $this->eth = $eth;
        $this->redis = $redis;
        $this->redisConfig = $redisConfig;
        $this->redisListKey = $redisListKey;
        $this->timeoutOnEmptyList = $timeoutOnEmptyList;
        $this->currency = $currency;
        $this->producer = $producer;
        $this->hydrator = $hydrator;
        $this->routingKey = $routingKey;

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

                        //@todo use generated hydrator for conversion
                        $message = new MonitoringMessage();

                        $message->setHash($transaction->getHash());
                        $message->setAmount($transaction->getAmount());
                        $message->setFrom($transaction->getPayer());
                        $message->setTo($transaction->getPayee());
                        $message->setCurrency($this->currency);

                        $this->producer->publish((string) $message, $this->routingKey);
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage());
                    }
                }
            } catch (\RedisException $exception) {
                $this->logger->error($exception->getMessage());
                $this->connectRedis($this->redis, $this->redisConfig);
            }
        }

    }
}