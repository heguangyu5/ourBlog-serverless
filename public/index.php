<?php

define('APPLICATION_PATH', realpath(__DIR__ . '/../app'));
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

require_once 'Zend/Application.php';
$app = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/app.ini'
);
$app->bootstrap()->run();
