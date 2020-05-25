<?php

class OurBlog_Db
{
    protected static $instance;

    protected $pdo;

    protected function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE') . ';charset=utf8',
            getenv('DB_USER'),
            getenv('DB_PASSWORD')
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function __clone()
    {}

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function fetchOne($sql, $bind = array())
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bind);
        return $stmt->fetchColumn();
    }
}
