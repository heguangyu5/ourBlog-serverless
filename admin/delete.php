<?php
    include __DIR__ . '/check-login.php';

    $error = false;
    try {
        if (!isset($_GET['id'])) {
            throw new InvalidArgumentException('missing required key id');
        }
        // id
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1)
        ));
        if (!$id) {
            throw new InvalidArgumentException('invalid id');
        }
    } catch (InvalidArgumentException $e) {
        die('参数不对');
    }

    include __DIR__ . '/../db.php';
    $db->exec("DELETE FROM posts WHERE id = $id AND uid = $uid");
    header('Location: ./index.php');
