<?php
/**
 * DCI Account Example in PHP
 *
 * Methodful Object Roles
 *
 * @author hakre
 */

trait TransferMoneySource
{
    function decreaseBalance(\App\Currency $amount) {
        // TODO: Implement decreaseBalance() method.
    }

    function transferTo(\App\Currency $amount, MoneySink $recipient) {
        // TODO: Implement transferTo() method.
    }
}

trait TransferMoneySink
{

    function increaseBalance(\App\Currency $amount) {
        // TODO: Implement increaseBalance() method.
    }

    function updateLog($string, \DateTime $time, \App\Currency $currency) {
        // TODO: Implement updateLog() method.
    }

    function transferFrom(\App\Currency $amount, MoneySource $source) {
        // TODO: Implement transferFrom() method.
    }
}