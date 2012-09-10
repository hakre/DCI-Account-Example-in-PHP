<?php
/**
 * DCI Account Example in PHP
 *
 * Methodful Object Roles
 *
 * @author hakre
 */

trait TransferMoneySink # extends \App\Account implements MoneySink
{
    function transferFrom(\App\Currency $amount, MoneySource $source) {
        $this->increaseBalance($amount);
        $this->updateLog('Transfer in', new DateTime, $amount);
    }
}

trait TransferMoneySource # extends App\Account implements MoneySource
{
    use Transaction;
    use Gui;

    function transferTo(\App\Currency $amount, MoneySink $recipient) {

        $this->beginTransaction();

        if ($this->balance->isLowerThan($amount)) {
            $this->endTransaction();
            throw new InvalidArgumentException(sprintf('Insufficient Funds (%s) to transfer %s.', $this->balance, $amount));
        }

        $this->decreaseBalance($amount);

        $recipient->increaseBalance($amount);
        $this->updateLog('Transfer Out', new DateTime, $amount);
        $recipient->updateLog('Transfer In', new DateTime, $amount);
        $this->guiDisplayScreen("SUCCESS_DEPOSIT_SCREEN");
        $this->endTransaction();
    }
}
