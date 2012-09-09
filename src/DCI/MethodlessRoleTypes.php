<?php
/**
 * DCI Account Example in PHP
 *
 * Methodless Role Types
 *
 * @author hakre
 */

use \DateTime;
use \App\Currency;

interface MoneySource
{
    function decreaseBalance(Currency $amount);

    function transferTo(Currency $amount, MoneySink $recipient);
}

interface MoneySink
{
    function increaseBalance(Currency $amount);

    function updateLog($string, DateTime $time, Currency $currency);

    function transferFrom(Currency $amount, MoneySource $source);
}