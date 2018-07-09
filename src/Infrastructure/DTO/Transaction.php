<?php
/**
 * peth
 *
 * @author Serhii Borodai <serhii.borodai@globalgames.net>
 */

namespace Infrastructure\DTO;


class Transaction
{
    /**
     * @var string
     */
    protected $hash;
    /**
     * @var string
     */
    protected $payee;
    /**
     * @var string
     */
    protected $payer;
    /**
     * @var string
     */
    protected $amount;

    /**
     * @return string
     */
    public function getHash(): ?string
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
    public function getPayee(): ?string
    {
        return $this->payee;
    }

    /**
     * @param string $payee
     */
    public function setPayee(string $payee): void
    {
        $this->payee = $payee;
    }

    /**
     * @return string
     */
    public function getPayer(): ?string
    {
        return $this->payer;
    }

    /**
     * @param string $payer
     */
    public function setPayer(string $payer): void
    {
        $this->payer = $payer;
    }

    /**
     * @return string
     */
    public function getAmount(): ?string
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

}