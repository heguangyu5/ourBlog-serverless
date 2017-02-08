<?php

try {
    $db = new PDO(
        'mysql:host=localhost;port=3306;dbname=ourblog;charset=utf8',
        'ourblog',
        'thisisourblog'
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}
