<?php

$baseDir = dirname(__DIR__);

// Preparing autoloader (thanks composer!)
require_once $baseDir . '/vendor/autoload.php';
// Importing the entrypoint 
use {{ placeholder.namespace }}\App;
use {{ placeholder.namespace }}\Config\Config;

// Load configuration and create the Config entity
$configData = require $baseDir . '/config/config.php';
$config = new Config($configData);

// Entrypoint
$app = new App($config);
$app->init();