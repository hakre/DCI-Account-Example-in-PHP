<?php
/**
 * DCI Account Example in PHP
 *
 * Currency Type Addition Operation
 *
 * @author hakre
 */
namespace App;

class CurrencyOperation extends Currency
{

    public function __construct() {
    }

    public function add(Currency $a, Currency $b) {
        list($orig, $sa, $sb) = $this->cloneAndPadDecimalAndString($a, $b);

        $sc = $sa + $sb;

        return new Currency(substr_replace($sc, '.', -$orig, 0));
    }

    public function sub(Currency $a, Currency $b) {
        list($orig, $sa, $sb) = $this->cloneAndPadDecimalAndString($a, $b);

        $sc = $sa - $sb;

        return new Currency(substr_replace($sc, '.', -$orig, 0));
    }

    private function cloneAndPadDecimalAndString(Currency $a, Currency $b) {
        $a = clone $a;
        $b = clone $b;

        $orig = $this->padDecimal($a, $b);

        $sa = strtr($a, $map = [' ' => '', '.' => '']);
        $sb = strtr($b, $map);

        return [$orig, $sa, $sb];
    }

    /**
     * @param Currency $a
     * @param Currency $b
     * @return int
     */
    private function padDecimal(Currency $a, Currency $b) {
        $max        = max(array_map('strlen', [$a->decimal, $b->decimal]));
        $max        = max($max, 2);
        $a->decimal = str_pad($a->decimal, $max, '0');
        $b->decimal = str_pad($b->decimal, $max, '0');
        return $max;
    }
}