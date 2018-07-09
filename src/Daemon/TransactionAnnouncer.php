<?php
/**
 * peth
 *
 * @author Serhii Borodai <serhii.borodai@globalgames.net>
 */

namespace Daemon;


use Config\RedisConfig;
use EthereumRPC\API\Eth;
use Infrastructure\DTO\MonitoringMessage;
use Psr\Log\LoggerInterface;

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
    private $rmq;

    /**
     * TransactionAnnouncer constructor.
     * @param LoggerInterface $logger
     * @param Eth $eth
     * @param \Redis $redis
     * @param RedisConfig $redisConfig
     * @param string $redisListKey
     * @param int $timeoutOnEmptyList
     * @param string $currency
     * @param $rmq
     */
    public function __construct(
        LoggerInterface $logger,
        Eth $eth,
        \Redis $redis,
        RedisConfig $redisConfig,
        $redisListKey = TransactionChecker::class,
        $timeoutOnEmptyList = 5,
        $currency = 'DMT',
        $rmq
    )
    {

        $this->logger = $logger;
        $this->eth = $eth;
        $this->redis = $redis;
        $this->redisConfig = $redisConfig;
        $this->redisListKey = $redisListKey;
        $this->timeoutOnEmptyList = $timeoutOnEmptyList;
        $this->currency = $currency;
        $this->rmq = $rmq;

        $this->connectRedis($this->redis, $this->redisConfig);
    }

    public function process()
    {
        //@todo use generated hydrator for conversion
        $message = new MonitoringMessage();

        $message->setAmount($transaction->getAmount());
        $message->setFrom($transaction->getPayer());
        $message->setTo($transaction->getPayee());
        $message->setCurrency($this->currency);

    }
}