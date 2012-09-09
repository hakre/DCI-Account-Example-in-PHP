<?php
/**
 * DCI Account Example in PHP
 *
 * Autoloader
 *
 * @author hakre
 */

spl_autoload_register(function($class) {
    is_file($file = __DIR__ . '/' . remove_dot_segments($class) . '.php')
        && (require $file);
});

/**
 * remove dot segments from path
 *
 * @param string $path
 * @return string
 */
function remove_dot_segments($path) {
    $parts = explode('/', strtr($path, ['\\' => '/']));
    $stack = [];
    foreach ($parts as $part) {
        switch ($part) {
            case '':
                $stack && $stack = [];
                break;
            case '.':
                break;
            case '..';
                $stack && array_pop($stack);
                break;
            default:
                $stack[] = $part;
        }
    }
    return implode('/', $stack);
}