<?php

define('APPLICATION_PATH', realpath(__DIR__ . '/../app'));
define('APPLICATION_ENV', 'testing');

require_once 'Zend/Application.php';
$app = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/app.ini'
);
$app->bootstrap();

class OurBlog_DbUnit_ArrayDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet
{
    protected $tables = array();

    public function __construct(array $data)
    {
        foreach ($data as $tableName => $rows) {
            $columns = array();
            if (isset($rows[0])) {
                $columns = array_keys($rows[0]);
            }

            $metaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $columns);
            $table = new PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);
            foreach ($rows as $row) {
                $table->addRow($row);
            }

            $this->tables[$tableName] = $table;
        }
    }

    protected function createIterator($reverse = false)
    {
        return new PHPUnit_Extensions_Database_DataSet_DefaultTableIterator($this->tables, $reverse);
    }

    public function getTable($tableName)
    {
        if (!isset($this->tables[$tableName])) {
            throw new InvalidArgumentException("$tableName is not a table in the current database.");
        }

        return $this->tables[$tableName];
    }
}

abstract class OurBlog_DatabaseTestCase extends PHPUnit_Extensions_Database_TestCase
{
    protected static $connection;

    public function getConnection()
    {
        if (!self::$connection) {
            $db       = Zend_Db_Table_Abstract::getDefaultAdapter();
            $dbConfig = $db->getConfig();
            self::$connection = $this->createDefaultDBConnection($db->getConnection(), $dbConfig['dbname']);
        }
        return self::$connection;
    }

    public function createArrayDataSet(array $data)
    {
        return new OurBlog_DbUnit_ArrayDataSet($data);
    }

    public function assertTableEmpty()
    {
        $tables = func_get_args();
        foreach ($tables as $table) {
            $this->assertEquals(0, $this->getConnection()->getRowCount($table));
        }
    }
}
