<?php

// for autoload
include __DIR__ . '/../index.php';
// for db
putenv('DB_HOST=127.0.0.1');
putenv('DB_PORT=3306');
putenv('DB_DATABASE=ourblog_test');
putenv('DB_USER=rootpw');
putenv('DB_PASSWORD=123456');

include 'PHPUnitNoNamespace.php';

abstract class OurBlog_DatabaseTestCase extends PHPUnit_DbUnit_Mysql_TestCase
{
    protected static $mysqlPort     = 3306;
    protected static $mysqlDbname   = 'ourblog_test';
    protected static $mysqlUsername = 'rootpw';
}
