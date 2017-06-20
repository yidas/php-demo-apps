<?php

/**
 * AutoLoader
 *
 * @author  Nick Tsai
 * @date    2015-12-20
 */
spl_autoload_register(function ($class)
{	
    $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, ltrim($class, '\\')) . '.php';

    if (file_exists($fileName)) {
    	
    	require $fileName;
    }
});

