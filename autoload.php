<?php

namespace {
    spl_autoload_register(function ($class) {
//        CONVERT NAMESPACE SEPARATOR TO A FILE PATH
        $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';

//        IF FILE EXISTS require IT (FASTER AND MOST OPTIMIZED RELATED TO require_once AND include)
        if(file_exists($file)) {
            require $file;
        }
    });
}