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

        $this->class = new PhpClassname($class);
    }

    /**
     * @param object $citizen
     * @param string $script trait
     * @param string $role interface
     */
    function cast($citizen, $script, $role) {

        $class = get_class($citizen);

    }

    public function castRole($trait, $interface) {

        $class = $this->class;

        $traitFQCN     = PhpClassname::FQCN($trait);
        $interfaceFQCN = PhpClassname::FQCN($interface);
        $classFQCN     = PhpClassname::FQCN($class);

        $actorClassname = $this->generateActorClassname($classFQCN, $traitFQCN, $interfaceFQCN);

        if (class_exists($actorClassname)) {
            return $actorClassname;
        }

        $definition = $this->generateActorClass($actorClassname, $classFQCN, $traitFQCN, $interfaceFQCN);

        eval($definition);

        return $actorClassname;
    }

    private function generateActorClass($actorClassname, $classFQCN, $traitFQCN, $interfaceFQCN) {

        $newActorClass      = new PhpClassname($actorClassname);
        $actorNamespace     = $newActorClass->getNamespace();
        $actorBaseClassname = $newActorClass->getBasename();

        $definition = "
            namespace $actorNamespace;

            class $actorBaseClassname extends $classFQCN implements $interfaceFQCN {
                use $traitFQCN;
            }
        ";

        return $definition;
    }

    private function generateActorClassname($classFQCN, $traitFQCN, $interfaceFQCN) {

        $traitHash     = PhpClassname::hash($traitFQCN);
        $interfaceHash = PhpClassname::hash($interfaceFQCN);

        return sprintf("%s᚜%sᚖ%s᚛", $classFQCN, $traitHash, $interfaceHash);
    }
}

function make_actor_class($class, $trait, $interface) {

    $actor = new Actor($class);
    return $actor->castRole($trait, $interface);
}

function make_actor($citizen, $script, $role) {

    $class      = get_class($citizen);
    $driver     = new Actor($class);
    $actorClass = $driver->castRole($script, $role);
    $actor      = PhpCast::cast($citizen, $actorClass);
    return $actor;
}

$source = new \App\CheckingAccount();
$source->increaseBalance(new \App\Currency(1000));
$sourceActorClass = make_actor_class('App\CheckingAccount', 'TransferMoneySource', 'MoneySource');
$sourceActor      = PhpCast::cast($source, $sourceActorClass);

$sink           = new \App\SavingsAccount();
$sinkActorClass = make_actor_class('App\SavingsAccount', 'TransferMoneySink', 'MoneySink');
$sinkActor      = PhpCast::cast($sink, $sinkActorClass);

/**
 * @var $sourceActor TransferMoneySource
 */
$sourceActor->transferTo(new \App\Currency(10), $sinkActor);

$sourceActor2 = make_actor($sourceActor, 'TransferMoneySource', 'MoneySource');
$sourceActor2->transferTo(new \App\Currency(10), $sinkActor);

echo "After: ", $sourceActor2->getAvailableBalance(), "\n";

$casting = new DCI\Casting();
$casting->setRoleAndScript('MoneySource', 'TransferMoneySource')->castOf($sourceActor2);

$sourceActor2->transferTo(new \App\Currency(980), $sinkActor);
echo "After: ", $sourceActor2->getAvailableBalance(), "\n";