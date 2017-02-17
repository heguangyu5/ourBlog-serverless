<?php
    include __DIR__ . '/check-login.php';
    include __DIR__ . '/../db.php';

    $error = false;
    if ($_POST) {
        include __DIR__ . '/prevent-csrf.php';
        include __DIR__ . '/../lib/OurBlog/Util.php';
        include __DIR__ . '/../lib/OurBlog/Post.php';
        try {
            $post = new OurBlog_Post($db, $uid);
            $post->add($_POST);
            header('Location: ./index.php');
            exit;
        } catch (InvalidArgumentException $e) {
            $error = '参数不对';
        } catch (Exception $e) {
            $error = 'Server Error';
        }
    }

    $headTitle = '写博文';
    include __DIR__ . '/header.php';

    if ($error) {
        echo '<p class="text-red">', $error, '</p>';
    }
?>

<link rel="stylesheet" href="../editormd/editormd.min.css">

<form method="POST" class="text-lft">
    <select name="category" class="block mar-btm">
        <option value="">所属栏目</option>
        <?php
            $stmt = $db->query('SELECT id, name FROM category', PDO::FETCH_OBJ);
            foreach ($stmt as $row) {
                echo '<option value="', $row->id, '">', htmlspecialchars($row->name), '</option>';
            }
        ?>
    </select>
    <input type="text" name="title" placeholder="标题" class="block mar-btm">
    <div id="editormd">
        <textarea name="content" class="hide"></textarea>
    </div>
    <button type="submit">提交</button>
</form>

<script src="../jquery-3.0.0.min.js"></script>
<script src="../editormd/editormd.min.js"></script>
<script>
    editormd('editormd', {
        path: '../editormd/lib/',
        height: 600,
        toolbarIcons: [
            'undo', 'redo', '|',
            'bold', 'del', 'italic', 'quote', 'h2', 'h3', '|',
            'list-ul', 'list-ol', 'hr', '|',
            'link',  'image', 'code', 'preformatted-text', 'code-block', 'table', 'html-entities', '|',
            'preview', 'fullscreen', 'clear', 'search', 'help', 'info'
        ],
        placeholder: '正文',
        imageUpload: true,
        imageFormats: ['jpg', 'png', 'gif', 'zip'],
        imageUploadURL: 'upload.php'
    });
</script>

<?php include __DIR__ . '/footer.php'; ?>
