<?php
/**
 * DCI Account Example in PHP
 *
 * "Compile" script
 *
 * @author hakre
 */

require_once __DIR__ . '/src/autoloader.php';

/// Methodful Roles as Traits ///

class CMoneySourceActor extends \App\Account implements MoneySource
{
    use TransferMoneySource;
}

/// Methodless Role Types ///

abstract class CMoneySource implements MoneySource
{
}

class CMoneySink extends CMoneySource implements MoneySink
{
    function decreaseBalance(\App\Currency $amount) {
    }

    function increaseBalance(\App\Currency $amount) {
    }

    function updateLog($string, \DateTime $time, \App\Currency $currency) {
    }

    function transferFrom(\App\Currency $amount, MoneySource $source) {
    }

    function transferTo(\App\Currency $amount, MoneySink $recipient) {
    }
}

new CMoneySink();

/// Currency ///

class CCurrency extends \App\Currency
{
}
