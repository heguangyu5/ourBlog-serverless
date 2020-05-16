<?php

define('APPLICATION_PATH', realpath(__DIR__ . '/../app'));

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('OurBlog_');

include 'PHPUnitNoNamespace.php';

abstract class OurBlog_DatabaseTestCase extends PHPUnit_DbUnit_Mysql_Zend_TestCase
{
    protected static $mysqlPort     = 3306;
    protected static $mysqlDbname   = 'ourblog_test';
    protected static $mysqlUsername = 'rootpw';
}
