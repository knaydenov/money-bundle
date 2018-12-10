<?php
namespace Kna\MoneyBundle\Tests\App\Entity;


use Money\Money;

class Account
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var Money
     */
    protected $balance;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Money
     */
    public function getBalance(): Money
    {
        return $this->balance;
    }

    /**
     * @param Money $balance
     */
    public function setBalance(Money $balance): void
    {
        $this->balance = $balance;
    }
}