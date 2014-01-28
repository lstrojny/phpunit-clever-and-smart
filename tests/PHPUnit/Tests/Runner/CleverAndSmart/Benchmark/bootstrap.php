<?php

ini_set('error_reporting', E_ALL);

// we assume the cwd is the project root
$files = array('vendor/autoload.php');

foreach ($files as $file) {
    if (file_exists($file)) {
        $loader = require $file;

        break;
    }
}

if (! isset($loader)) {
    throw new RuntimeException('vendor/autoload.php could not be found. Did you run `php composer.phar install`?');
}

require 'tests/PHPUnit/Tests/Runner/CleverAndSmart/Benchmark/RunSuiteEvent.php';

unset($files, $file, $loader);