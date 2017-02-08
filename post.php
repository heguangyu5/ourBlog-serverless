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

<link rel="stylesheet" href="editormd/editormd.preview.min.css">

<div class="text-lft">
    <div id="editormd">
        <textarea class="hide"><?php
            echo '# ', htmlspecialchars($post->title), "\n";
            echo htmlspecialchars($post->content);
        ?></textarea>
    </div>
</div>

<script src="jquery-3.0.0.min.js"></script>
<script src="editormd/lib/marked.min.js"></script>
<script src="editormd/lib/prettify.min.js"></script>
<script src="editormd/editormd.min.js"></script>
<script>
    editormd.markdownToHTML('editormd');
</script>
