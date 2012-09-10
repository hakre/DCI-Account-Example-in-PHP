<?php
/**
 * DCI Account Example in PHP
 *
 * Context Stack
 *
 * @author hakre
 */

class ContextStack
{
    private $contexts;

    public function enterContext($context) {
        $this->contexts[] = $context;
    }

    public function getContext() {
        return end($this->contexts);
    }

    public function leaveContext() {
        array_pop($this->contexts);
    }
}