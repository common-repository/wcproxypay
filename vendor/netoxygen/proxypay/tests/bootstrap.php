<?php
require_once __DIR__.'/../vendor/autoload.php';

// Configure AspectMock testing stuff
$kernel = \AspectMock\Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'appDir'       => __DIR__.'/../src',
    'cacheDir'     => __DIR__. '/../build/tmp',
    'includePaths' => [ __DIR__.'/../src' ]
]);
