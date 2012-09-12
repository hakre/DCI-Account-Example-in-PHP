<?php
/**
 * DCI Account Example in PHP
 *
 * Example Stub Application invoking DCI
 *
 * @author hakre
 */

require __DIR__ . '/../src/autoloader.php';

header("Content-Type: text/plain");

print "DCI Account Example in PHP\n";
print "==========================\n";

Use DCI\Casting;
Use App\SavingsAccount;
Use App\CheckingAccount;
Use App\Currency;


$casting = new Casting();

$source = new SavingsAccount;
$casting->setRoleAndScript('MoneySource', 'TransferMoneySource')->castOf($source);

$sink = new CheckingAccount;
$casting->setRoleAndScript('MoneySink', 'TransferMoneySink')->castOf($sink);

$source->increaseBalance(new Currency(100000));
$source->transferTo(new Currency(200), $sink);

printf("Source: %s: Sink: %s\n", $source->getAvailableBalance(), $sink->getAvailableBalance());


