<?php
/**
 * peth
 *
 * @author Serhii Borodai <serhii.borodai@globalgames.net>
 */

namespace Infrastructure\DTO;


class MonitoringMessage
{
    /**
     * @var string
     */
    protected $hash;
    /**
     * @var string
     */
    protected $from;
    /**
     * @var string
     */
    protected $to;
    /**
     * @var string
     */
    protected $amount;
    /**
     * @var string
     */
    protected $currency;
    /**
     * @var string
     */
    protected $seed;

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom(string $from): void
    {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @param string $to
     */
    public function setTo(string $to): void
    {
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     */
    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getSeed(): string
    {
        return $this->seed;
    }

    /**
     * @param string $seed
     */
    public function setSeed(string $seed): void
    {
        $this->seed = $seed;
    }


}