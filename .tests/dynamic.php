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

class Actor
{
    /**
     * @var PhpClassname
     */
    private $class;

    public function __construct($class) {

        $this->setClass($class);
    }

    public function setClass($class) {

        if ($class instanceof PhpClassname) {
            $this->class = $class;
        } else {
            $this->class = new PhpClassname($class);
        }
    }

    public function castRole($trait, $interface) {

        $traitFQCN     = (new PhpClassname($trait))->getFullyQualifiedClassName();
        $interfaceFQCN = (new PhpClassname($interface))->getFullyQualifiedClassName();
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