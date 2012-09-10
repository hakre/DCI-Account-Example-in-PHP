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
    protected $sign, $integer, $decimal;

    public function __construct($amount) {
        $this->setFromString($amount);
    }

    public function isLowerThan(Currency $amount) {
        return 0 < $this->operation('compare', $amount);
    }

    public function isNegative() {
        return $this->sign === '-';
    }

    public function add(Currency $amount) {
        $this->operate('add', $amount);
    }

    public function sub(Currency $amount) {
        $this->operate('sub', $amount);
    }

    private function operate($operation, Currency $operand) {
        $result = $this->operate($operation, $operand);
        $this->setFromString($result);
    }

    /**
     * @param string $operation
     * @param Currency $operand
     * @return Currency
     */
    private function operation($operation, Currency $operand) {
        return (new CurrencyOperation)->{$operation}($this, $operand);
    }

    protected function setFromString($string) {
        $r = preg_match('~^(?:([+-])(?: *))?((?:\d+ )*\d+)?(?:\.(\d*))?$~', $string, $matches);
        if (!$r) {
            throw new \InvalidArgumentException(sprintf('Amount invalid format: "%s"', $string));
        }
        list(, $sign, $before, $after) = $matches + [null, '', 0, 0];

        $sign === '' && $sign = '+';

        $before = strtr($before, [' ' => '']);
        $before = ltrim($before, '0');
        $before || $before = '0';

        $after = rtrim($after, '0');
        $after || $after = '0';

        assert('strpos(" +-", $sign)');
        assert('strlen($before) > 0');
        assert('strlen($after ) > 0');

        $this->sign    = $sign;
        $this->integer = $before;
        $this->decimal = $after;
    }

    public function __toString() {

        // format amount +/- 1 234.678
        $buffer = strrev($this->integer);
        $buffer = implode(' ', str_split($buffer, 3));
        $before = strrev($buffer);

        $after = $this->decimal;
        strlen($after) < 2 && $after .= '0';

        return $this->sign . ' ' . $before . '.' . $after;
    }
}