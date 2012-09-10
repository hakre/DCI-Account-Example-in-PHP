<?php
/**
 * DCI Account Example in PHP
 *
 * Compie as Testcase
 *
 * @author hakre
 */

class CompileTest extends \PHPUnit_Framework_TestCase
{
    function testCompile() {
        $file = __DIR__ . '/../../compile.php';
        $this->assertLint($file);
        require $file;
        $this->addToAssertionCount(1);
    }

    function assertLint($file) {

        $this->assertTrue(is_file($file), sprintf("%s is a file.", basename($file)));

        $command = sprintf('php -nl %s', escapeshellarg($file));
        $output  = shell_exec($command);

        $lines    = array_filter(explode("\n", $output));
        $lastLine = array_pop($lines);

        $validates = (bool)preg_match('/^No syntax errors detected in /', $lastLine);
        $this->assertTrue($validates, sprintf("File %s lints.", basename($file)));
    }
}
