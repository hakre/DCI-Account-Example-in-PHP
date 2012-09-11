<?php
/**
 * DCI Account Example in PHP
 *
 * Autoloader
 *
 * @author hakre
 */


/**
 * SPL autoloader
 */
spl_autoload_register(function($class) {

    $file = __DIR__ . '/' . remove_dot_segments($class) . '.php';
    is_file($file) && (require $file);
});

/**
 * Require files
 */
foreach ([
             'DCI/Transaction',
             'DCI/Gui',

             'DCI/MethodlessRoleTypes',
             'DCI/MethodfulRoles',
         ] as $part) {
    $file = __DIR__ . '/' . $part . '.php';
    require $file;
}

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