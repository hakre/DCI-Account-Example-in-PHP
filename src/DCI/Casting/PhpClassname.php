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

    static function FQCN($classname) {

        return (new self($classname))->getFullyQualifiedClassName();
    }

    static function hash($classname) {

        return (new self($classname))->getHash();
    }

    protected $string;
    private $namespaceSeparator = '\\';

    function __construct($string) {

        $this->string = (string)$string;
    }

    function __toString() {

        return $this->string;
    }

    function getNamespace() {

        $className          = $this->string;
        $namespaceSeparator = $this->namespaceSeparator;

        return ltrim($this->extractNamespace($className), $namespaceSeparator);
    }

    function getBasename() {

        $className          = $this->string;
        $namespaceSeparator = $this->namespaceSeparator;

        $pos = strlen($this->extractNamespace($className));

        return ltrim(substr($className, $pos), $namespaceSeparator);
    }

    function getFullyQualifiedClassName() {

        $className          = $this->string;
        $namespaceSeparator = $this->namespaceSeparator;

        return $namespaceSeparator . ltrim($className, $namespaceSeparator);
    }

    function getHash() {

        $className          = $this->string;
        $namespaceSeparator = $this->namespaceSeparator;

        $string = ltrim($className, $namespaceSeparator);
        return strtr($string, [$namespaceSeparator => "__"]);
    }

    private function extractNamespace($className) {

        $namespaceSeparator = $this->namespaceSeparator;

        // TODO: Check for PHP string function: locate last occurrence of a char and return everything before it
        $pos = strrpos($className, $namespaceSeparator);

        if ($pos === false) {
            return $className;
        }

        return rtrim(substr($className, 0, $pos), $namespaceSeparator);
    }

}