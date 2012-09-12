<?php
/**
 * DCI Account Example in PHP
 *
 * Test Dynamic Extension
 *
 * @author hakre
 */

require __DIR__ . '/../src/autoloader.php';

use  DCI\Casting\PhpClassname;
use  DCI\Casting\PhpCast;

class InvalidTypeException extends RuntimeException
{
}

trait ActorDecorator
{
    private $subject;

    public function __construct($subject) {

        $this->subject = $subject;
    }

    function __call($name, $arguments) {

        $callable = array($this->subject, $name);
        call_user_func_array($callable, $arguments);
    }

    function __clone() {

        $this->subject = clone $this->subject;
    }

    function __destruct() {

        $callable = array($this->subject, '__destruct');
        if (is_callable($callable)) {
            call_user_func($callable);
        }
    }

    function __get($name) {

        return $this->subject->{$name};
    }

    function __invoke() {

        $callable = array($this->subject, '__invoke');
        if (is_callable($callable)) {
            call_user_func($callable);
        }
    }

}


class TLanguage extends PhpClassname
{
    private $autoload = true;

    const TYPE_UNDEFINED = 0; # always equals 0 / false / null
    const TYPE_CLASS     = 1;
    const TYPE_INTERFACE = 2;
    const TYPE_TRAIT     = 4;

    private $TYPE_NAMES = [
        self::TYPE_UNDEFINED => 'Undefined',
        self::TYPE_CLASS     => 'Class',
        self::TYPE_INTERFACE => 'Interface',
        self::TYPE_TRAIT     => 'Trait'
    ];

    public function __construct($string, $type) {

        $this->string = (string)$string;
        $this->ensureType($type);
    }

    protected function isClass() {

        $autoload = $this->autoload;

        return class_exists($this->string, $autoload);
    }

    protected function isInterface() {

        $autoload = $this->autoload;

        return interface_exists($this->string, $autoload);
    }

    protected function isTrait() {

        $autoload = $this->autoload;

        return (bool)trait_exists($this->string, $autoload);
    }

    /**
     * @return int on of the TYPE_... constant
     */
    protected function getType() {

        if ($this->isClass()) {
            return self::TYPE_CLASS;
        }

        if ($this->isInterface()) {
            return self::TYPE_INTERFACE;
        }

        if ($this->isTrait()) {
            return self::TYPE_TRAIT;
        }

        return self::TYPE_UNDEFINED;
    }

    protected function ensureType($type) {

        $names = $this->TYPE_NAMES;

        if (!isset($names[$type])) {
            throw new InvalidTypeException(sprintf("Undefined type %s.", $type));
        }

        $isType      = $this->getType();
        $istTypeName = $names[$isType];

        $typeName = $names[$type];

        if ($isType !== $type) {
            throw new InvalidTypeException(
                sprintf("%s [%s (%d)] is not a %s (%d).",
                    $this, $istTypeName, $isType, $typeName, $type)
            );
        }
    }

    public function __toString() {

        return $this->string;
    }
}

class TUndefined extends TLanguage
{
    public function __construct($string) {

        parent::__construct($string, self::TYPE_UNDEFINED);
    }
}

class TClass extends TLanguage
{
    public function __construct($string) {

        parent::__construct($string, self::TYPE_CLASS);
    }
}

class TInterface extends TLanguage
{
    public function __construct($string) {

        parent::__construct($string, self::TYPE_INTERFACE);
    }
}

class TTrait extends TLanguage
{
    public function __construct($string) {

        parent::__construct($string, self::TYPE_TRAIT);
    }
}

class Actor
{
    /**
     * @var TClass
     */
    private $class;

    public function __construct($class) {

        $this->setClass($class);
    }

    public function setClass($class) {

        if ($class instanceof TClass) {
            $this->class = $class;
        } else {
            $this->class = new TClass($class);
        }
    }

    public function castRole($trait, $interface) {

        $traitFQCN     = (new TTrait($trait))->getFullyQualifiedClassName();
        $interfaceFQCN = (new TInterface($interface))->getFullyQualifiedClassName();
        $classFQCN     = $this->class->getFullyQualifiedClassName();

        $newClassName = $this->generateActorClassname($classFQCN, $traitFQCN, $interfaceFQCN);

        if (class_exists($newClassName)) {
            return $newClassName;
        }

        $newClass = new PhpClassname($newClassName);

        $namespace     = $newClass->getNamespace();
        $classBasename = $newClass->getBasename();


        $definition = "
            namespace $namespace;

            class $classBasename extends $classFQCN implements $interfaceFQCN {
                use $traitFQCN;
            }
        ";

        echo $definition, "\n";

        eval($definition);
        return $newClass;
    }

    private function generateActorClass($classFQCN, $traitFQCN, $interfaceFQCN) {

    }

    private function generateActorClassname($classFQCN, $traitFQCN, $interfaceFQCN) {

        $traitFQCN     = $this->removeNamespaceChars($traitFQCN);
        $interfaceFQCN = $this->removeNamespaceChars($interfaceFQCN);

        return sprintf("%s_ACT_%s_ROLE_%s", $classFQCN, $traitFQCN, $interfaceFQCN);
    }

    private function removeNamespaceChars($string) {

        $namespaceSeparator = '\\';
        $string             = ltrim($string, $namespaceSeparator);
        return strtr($string, [$namespaceSeparator => "__"]);
    }
}

function make_actor($class, $trait, $interface) {

    $actor = new Actor($class);
    return $actor->castRole($trait, $interface);
}


$source           = new \App\CheckingAccount();
$sourceActorClass = make_actor('App\SavingsAccount', 'TransferMoneySource', 'MoneySource');
$sourceActor      = PhpCast::castAs($source, $sourceActorClass);
$sourceActor->increaseBalance(new \App\Currency(1000));


$sink           = new \App\SavingsAccount();
$sinkActorClass = make_actor('App\SavingsAccount', 'TransferMoneySink', 'MoneySink');
$sinkActor      = PhpCast::castAs($sink, $sinkActorClass);

// echo 'Class: ', $sourceActorClass, "\n";
// echo 'Actor: ', var_dump($sourceActor);


/**
 * @var $sourceActor TransferMoneySource
 */
$sourceActor->transferTo(new \App\Currency(10), $sinkActor);