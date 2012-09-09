<?php
/**
 * DCI Account Example in PHP
 *
 * Currency Type Addition Operation Test
 *
 * @author hakre
 */

namespace App;

class CurrencyOperationTest extends \PHPUnit_Framework_TestCase
{
    public function testAddition() {
        $operation = new CurrencyOperation(new Currency(0), new Currency(0));
        $this->assertInstanceOf('App\CurrencyOperation', $operation);

        $data = [
            [0, 0, 0],
            [10, 10, 20],
            [10, -10, 0],
            [0.1, -0.2, -0.1],
            [1, -0.001, 0.999]
        ];

        foreach ($data as $datum) {
            $a = new Currency($datum[0]);
            $b = new Currency($datum[1]);

            $expected = new Currency($datum[2]);
            $actual   = $operation->add($a, $b);

            $this->assertInstanceOf('App\Currency', $actual);
            $this->assertEquals($actual, $expected);
        }
    }
}