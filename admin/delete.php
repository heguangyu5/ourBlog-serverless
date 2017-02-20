<?php
    include __DIR__ . '/prevent-csrf.php';
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

        include __DIR__ . '/../db.php';
        if (!$db->query("SELECT id FROM posts WHERE id = $id AND uid = $uid")->fetch(PDO::FETCH_COLUMN)) {
            throw new InvalidArgumentException('you cannot only delete your own post');
        }
    } catch (InvalidArgumentException $e) {
        die('参数不对');
    }

    $db->beginTransaction();
    try {
        $db->exec("DELETE FROM posts WHERE id = $id");
        $db->exec("DELETE FROM post_tag WHERE post_id = $id");
        $db->commit();
        header('Location: ./index.php');
    } catch (Exception $e) {
        $db->rollBack();
        die('Server Error');
    }
