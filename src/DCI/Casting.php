<?php
/**
 * DCI Account Example in PHP
 *
 * The famous PHP DCI Casting Agency
 *
 * @author hakre
 */

namespace DCI;
use DCI\Casting\PhpClassname;

class Casting
{
    private $citizen, $role, $script;

    function __construct($role = null, $script = null, $citizen = null) {

        $this->setRoleAndScript($role, $script);
        $this->citizen = $citizen;
    }

    function setCitizen($citizen) {

        $this->citizen = $citizen;

        return $this;
    }

    function setRoleAndScript($role, $script) {

        $this->role   = PhpClassname::FQCN($role);
        $this->script = PhpClassname::FQCN($script);

        return $this;
    }

    function cast() {

        $class = PhpClassname::FQCN(get_class($this->citizen));

        $actorClassname = $this->generateActorClassname($class, $this->script, $this->role);

        if (!class_exists($actorClassname)) {
            eval($this->generateActorClass($actorClassname, $class, $this->script, $this->role));
        }

        return $this->castViaSerialize($this->citizen, $actorClassname);
    }

    function castOf(&$actor) {

        $class = PhpClassname::FQCN(get_class($actor));

        $actorClassname = $this->generateActorClassname($class, $this->script, $this->role);

        if (!class_exists($actorClassname)) {
            eval($this->generateActorClass($actorClassname, $class, $this->script, $this->role));
        }

        $actor = $this->castViaSerialize($actor, $actorClassname);

        return $this;
    }

    protected function generateActorClass($actorClassname, $classFQCN, $traitFQCN, $interfaceFQCN) {

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

    protected function generateActorClassname($classFQCN, $traitFQCN, $interfaceFQCN) {

        $traitHash     = PhpClassname::hash($traitFQCN);
        $interfaceHash = PhpClassname::hash($interfaceFQCN);

        return sprintf("%s◨%s▶%s◧", $classFQCN, $traitHash, $interfaceHash);
    }

    protected function castViaSerialize($object, $as) {

        $serialized = serialize($object);

        $r = sscanf($serialized, 'O:%*d:"%[^"]"', $class);

        if (!$r) {
            throw new \UnexpectedValueException('Unable to extract classname of serialized object "%s".', get_class($object));
        }

        $len       = strlen($class);
        $prefixLen = $len + strlen($len) + 5; // 6: O::""

        $prefixNew = sprintf('O:%d:"%s"', strlen($as), $as);

        $new = unserialize($prefixNew . substr($serialized, $prefixLen));

        return $new;
    }
}