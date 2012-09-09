<?php
/**
 * DCI Account Example in PHP
 *
 * Methodless Role Types
 *
 * @author hakre
 */

interface MoneySource
{
    function decreaseBalance(\App\Currency $amount);

    function transferTo(\App\Currency $amount, MoneySink $recipient);
}

interface MoneySink
{
    function increaseBalance(\App\Currency $amount);

    function updateLog($string, \DateTime $time, \App\Currency $currency);

    function transferFrom(\App\Currency $amount, MoneySource $source);
}