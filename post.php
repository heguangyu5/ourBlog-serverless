<?php
    try {
        if (!isset($_GET['id'])) {
            throw new InvalidArgumentException('missing required key id');
        }
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1)
        ));
        if (!$id) {
            throw new InvalidArgumentException('invalid id');
        }

        include __DIR__ . '/db.php';
        $post = $db->query("SELECT title, content FROM posts WHERE id = $id")->fetch(PDO::FETCH_OBJ);
        if (!$post) {
            throw new InvalidArgumentException('post not exist');
        }
    } catch (InvalidArgumentException $e) {
        header('Location: ./index.php');
        exit;
    }

    $headTitle = $post->title;
    include __DIR__ . '/header.php';
?>

<div class="text-lft">
    <h3><?php echo htmlspecialchars($post->title); ?></h3>
    <div><?php echo nl2br(htmlspecialchars($post->content)); ?></div>
</div>
