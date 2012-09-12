<?php
/**
 * DCI Account Example in PHP
 *
 * Test Dynamic Extension
 *
 * @author hakre
 */

require __DIR__ . '/../src/autoloader.php';

use DCI\Casting\PhpClassname;
use DCI\Casting;

class Actor extends Casting
{
    /**
     * @var PhpClassname
     */
    private $class;

    public function __construct($class) {

        $this->class = new PhpClassname($class);
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

    public function cast($object, $as) {

        return $this->castViaSerialize($object, $as);
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
    $actor      = $driver->cast($citizen, $actorClass);
    return $actor;
}

$source = new \App\CheckingAccount();
$source->increaseBalance(new \App\Currency(1000));
$sourceActor = make_actor($source, 'TransferMoneySource', 'MoneySource');

$sink      = new \App\SavingsAccount();
$sinkActor = make_actor($sink, 'TransferMoneySink', 'MoneySink');

/**
 * @var $sourceActor TransferMoneySource
 */
$sourceActor->transferTo(new \App\Currency(10), $sinkActor);

$sourceActor2 = make_actor($sourceActor, 'TransferMoneySource', 'MoneySource');
$sourceActor2->transferTo(new \App\Currency(10), $sinkActor);

echo "After: ", $sourceActor2->getAvailableBalance(), "\n";

$casting = new Casting();
$casting->setRoleAndScript('MoneySource', 'TransferMoneySource')->castOf($sourceActor2);

$casting2 = new Casting('MoneySink', 'TransferMoneySink');
$casting2->castOf($sinkActor);

$sourceActor2->transferTo(new \App\Currency(980), $sinkActor);
echo "After: ", $sourceActor2->getAvailableBalance(), "\n";