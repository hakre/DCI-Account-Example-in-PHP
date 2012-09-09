<?php
/**
 * DCI Account Example in PHP
 *
 * Account Type
 *
 * @author hakre
 */

namespace App;

abstract class Account
{
    /**
     * @var Currency
     */
    protected $balance;

    /**
     * @var string
     */
    protected $toString = 'Account';

    function __construct() {
        $this->balance = new Currency(0);
    }

    /**
     * @return Currency
     */
    function getAvailableBalance() {
        return $this->balance;
    }

    function decreaseBalance(Currency $amount) {
        if ($amount->isNegative()) {
            throw new \InvalidArgumentException(sprintf('Amount "%s" is negative.', $amount));
        }
        $this->balance->sub($amount);
    }

    function increaseBalance(Currency $amount) {
        $this->balance->add($amount);
    }

    function updateLog($message, \DateTime $date, Currency $amount) {
        printf("Account: %s, %s, %s, %s\n", $this, $message, $date->format('Y-m-d'), $amount);
    }

    function __toString() {
        return $this->toString;
    }
}