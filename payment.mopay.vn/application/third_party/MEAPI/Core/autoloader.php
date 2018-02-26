<?php

spl_autoload_register(function ($class) {
    // project-specific namespace prefix
    $prefix = 'Misc\\';
    //echo $prefix;
    // For backwards compatibility
    $customBaseDir = __DIR__ . "v1";
    // @todo v6: Remove support for 'MISC_SDK_V1_SRC_DIR'
    if (defined('MISC_SDK_V1_SRC_DIR')) {
        $customBaseDir = MISC_SDK_V1_SRC_DIR;
    } elseif (defined('MISC_SDK_SRC_DIR')) {
        $customBaseDir = MISC_SDK_SRC_DIR;
    }
    // base directory for the namespace prefix
    $baseDir = $customBaseDir ? : __DIR__;
//var_dump($customBaseDir);
    //die;
    //echo $baseDir . "<br>";
    //echo $baseDir;
    // does the class use the namespace prefix?
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relativeClass = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    //$file = rtrim($baseDir, '/') . $relativeClass  . '.php';
    //echo rtrim($baseDir, '/') . '/';
    //var_dump($relativeClass);
    $file = str_replace('\\', '/', rtrim($baseDir, '/')) . '/' . str_replace('\\', '/', $relativeClass) . '.php';
    //echo $file;
    //var_dump(file_exists($file));
    //die;
    //echo $file;
    // if the file exists, require it
    
    if (file_exists($file)) {
        require_once $file;
    }
	
	$file = str_replace('\\', '/', rtrim($baseDir, '/')) . '/' . str_replace('\\', '/', "OverloadFunction") . '.php';
    //require overload function
    if (file_exists($file)) {
        //var_dump($file);
        //echo $file . "<br>";
        require_once $file;
    }
});
