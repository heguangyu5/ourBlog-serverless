<?php

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
        if (self::$connection == null) {
            $pdo = new PDO(
                'mysql:host=localhost;port=3306;dbname=ourblog_test;charset=utf8',
                'root',
                '123456'
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection = $this->createDefaultDBConnection($pdo, 'ourblog_test');
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
