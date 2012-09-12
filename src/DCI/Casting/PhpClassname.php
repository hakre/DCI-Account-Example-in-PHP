<?php
/**
 * DCI Account Example in PHP
 *
 * Classname parser and normalizer based on PHP rules (helper object for casting)
 *
 * @author hakre
 */

namespace DCI\Casting;

class PhpClassname
{
    const SEPARATOR = '\\';

    static function FQCN($classname) {

        return self::SEPARATOR . ltrim($classname, self::SEPARATOR);
    }

    static function hash($classname) {

        $string = ltrim($classname, self::SEPARATOR);
        return strtr($string, [self::SEPARATOR => "__"]);
    }

    protected $name;

    function __construct($string) {

        $this->name = (string)$string;
    }

    function __toString() {

        return $this->name;
    }

    function getNamespace() {

        return ltrim($this->extractNamespace($this->name), self::SEPARATOR);
    }

    function getBasename() {

        $classname = $this->name;

        $pos = strlen($this->extractNamespace($classname));

        return ltrim(substr($classname, $pos), self::SEPARATOR);
    }

    function getFullyQualifiedClassName() {

        return self::FQCN($this->name);
    }

    function getHash() {

        return self::hash($this->name);
    }

    private function extractNamespace($classname) {

        // TODO: Check for PHP string function: locate last occurrence of a char and return everything before it
        $pos = strrpos($classname, self::SEPARATOR);

        if ($pos === false) {
            return $classname;
        }

        return rtrim(substr($classname, 0, $pos), self::SEPARATOR);
    }

}