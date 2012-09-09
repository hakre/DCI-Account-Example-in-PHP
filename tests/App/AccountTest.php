<?php
/**
 * DCI Account Example in PHP
 *
 * Account Type
 *
 * @author hakre
 */

namespace App;

require 'AccountStub.php';

class AccountTest extends \PHPUnit_Framework_TestCase
{
    function testConstructor() {
        $account = new AccountStub;
        $this->assertInstanceOf('\App\Account', $account);
    }

    function testUpdateLog() {
        $account = new AccountStub;

        ob_start();
        $account->updateLog('Messageline of text', new \DateTime('2012-12-12 12:12'), new Currency(1000));
        $actual   = ob_get_clean();
        $expected = "Account: StubAccount, Messageline of text, 2012-12-12, + 1 000.00\n";
        $this->assertEquals($expected, $actual);

    }
}