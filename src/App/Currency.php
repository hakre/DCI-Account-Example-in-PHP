<?php
/**
 * DCI Account Example in PHP
 *
 * Currency Type
 *
 * @author hakre
 */

namespace App;

/**
 * Currency as PHP has none, this is mostly nice formatting for the example.
 */
class Currency
{
    /**
     * @var string
     */
    private $amount;

    public function __construct($amount) {
        $this->setAmountFromString($amount);
    }

    private function setAmountFromString($amount) {
        $r = preg_match('~^((?:\d+ )*\d+)(?:\.(\d*))?$~', $amount, $matches);
        if (!$r) {
            throw new \InvalidArgumentException(sprintf('Amount invalid format: "%s"', $amount));
        }
        list(, $before, $after) = $matches + [null, 0, 0];

        // format amount 1 234.678
        $buffer = strrev((str_replace(' ', '', $before)));
        $buffer = implode(' ', str_split($buffer, 3));
        $before = strrev($buffer);
        if (!strlen($before)) {
            $before = '0';
        }

        if (strlen($after) < 2) {
            $after .= '0';
        }

        assert('strlen($before) > 0');
        assert('strlen($after ) > 1');

        $this->amount = $before . '.' . $after;
    }

    public function __toString() {
        return $this->amount;
    }
}