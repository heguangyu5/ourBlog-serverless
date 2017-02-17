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
            $post->edit($_POST);
            header('Location: ./index.php');
            exit;
        } catch (InvalidArgumentException $e) {
            $error = '参数不对';
        } catch (Exception $e) {
            $error = 'Server Error';
        }
    }

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
        $post = $db->query("SELECT category, title, content FROM posts WHERE id = $id AND uid = $uid")->fetch(PDO::FETCH_OBJ);
        if (!$post) {
            throw new InvalidArgumentException('post not exist');
        }
    } catch (InvalidArgumentException $e) {
        header('Location: ./index.php');
        exit;
    }

    $headTitle = '编辑';
    include __DIR__ . '/header.php';

    if ($error) {
        echo '<p class="text-red">', $error, '</p>';
    }
?>

<link rel="stylesheet" href="../editormd/editormd.min.css">

<form method="POST" class="text-lft">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <select name="category" class="block mar-btm">
        <option value="">所属栏目</option>
        <?php
            $stmt = $db->query('SELECT id, name FROM category', PDO::FETCH_OBJ);
            foreach ($stmt as $row) {
                echo '<option value="', $row->id, '"', ($post->category == $row->id ? ' selected' : ''), '>', htmlspecialchars($row->name), '</option>';
            }
        ?>
    </select>
    <input type="text" name="title" placeholder="标题" class="block mar-btm" value="<?php echo htmlspecialchars($post->title); ?>">
    <div id="editormd">
        <textarea name="content" class="hide"><?php echo htmlspecialchars($post->content); ?></textarea>
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
