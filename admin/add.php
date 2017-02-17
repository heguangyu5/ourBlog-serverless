<?php
    include __DIR__ . '/check-login.php';
    include __DIR__ . '/../db.php';

    $error = false;
    if ($_POST) {
        include __DIR__ . '/prevent-csrf.php';
        try {
            $requiredKeys = array(
                // key => required
                'category' => true,
                'title'    => true,
                'content'  => true,
                'tags'     => false
            );
            foreach ($requiredKeys as $key => $required) {
                if (!isset($_POST[$key])) {
                    throw new InvalidArgumentException("missing required key $key");
                }
                $_POST[$key] = trim($_POST[$key]);
                if ($required && empty($_POST[$key])) {
                    throw new InvalidArgumentException("$key required");
                }
            }
            // category
            $_POST['category'] = filter_var($_POST['category'], FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 1)
            ));
            if (!$_POST['category']) {
                throw new InvalidArgumentException('invalid category');
            }
            // title
            $len = mb_strlen($_POST['title'], 'UTF-8');
            if ($len > 500) {
                throw new InvalidArgumentException('title maxlength is 500');
            }
            // content
            $len = strlen($_POST['content']);
            if ($len > 64000) {
                throw new InvalidArgumentException('content maxlength is 64000');
            }
            // tags
            $hasTag = false;
            if ($_POST['tags']) {
                $len = mb_strlen($_POST['tags']);
                if ($len > 600) {
                    throw new InvalidArgumentException('tags too long');
                }
                $tags     = explode(',', $_POST['tags']);
                $tagIdMap = array();
                foreach ($tags as $idx => $tag) {
                    $tag = trim($tag);
                    $len = mb_strlen($tag, 'UTF-8');
                    if ($len > 50) {
                        throw new InvalidArgumentException('tag too long');
                    }
                    if ($len == 0) {
                        continue;
                    }
                    $tagIdMap[$tag] = 0;
                }
                if ($tagIdMap) {
                    $hasTag = true;
                }
            }
        } catch (InvalidArgumentException $e) {
            $error = '参数不对';
        }

        if (!$error) {
            if ($hasTag) {
                $tagsCount = count($tagIdMap);
                $stmt      = $db->prepare('SELECT id, name FROM tag WHERE name IN (?' . str_repeat(', ?', $tagsCount - 1) . ')');
                $stmt->execute(array_keys($tagIdMap));
                $tagRows = $stmt->fetchAll(PDO::FETCH_OBJ);
                foreach ($tagRows as $row) {
                    $tagIdMap[$row->name] = $row->id;
                }
                $newTags = array();
                foreach ($tagIdMap as $tag => $tagId) {
                    if (!$tagId) {
                        $newTags[] = $tag;
                    }
                }
            }
            $createDate = date('Y-m-d H:i:s');

            $db->beginTransaction();
            try {
                // post
                $stmt = $db->prepare('INSERT INTO posts(uid, category, title, content, create_date, update_date) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute(array(
                    $uid,
                    $_POST['category'],
                    $_POST['title'],
                    $_POST['content'],
                    $createDate,
                    $createDate
                ));
                // tags
                if ($hasTag) {
                    $postId = $db->lastInsertId();
                    // tag
                    if ($newTags) {
                        $stmt = $db->prepare('INSERT INTO tag(name) VALUES (?)');
                        foreach ($newTags as $tag) {
                            $stmt->execute(array($tag));
                            $tagIdMap[$tag] = $db->lastInsertId();
                        }
                    }
                    // post_tag
                    $stmt = $db->prepare('INSERT INTO post_tag(post_id, tag_id) VALUES (?, ?)');
                    foreach ($tagIdMap as $tagId) {
                        $stmt->execute(array($postId, $tagId));
                    }
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                $error = 'Server Error';
            }
            if (!$error) {
                header('Location: ./index.php');
                exit;
            }
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
    <p>多个标签使用,号分隔,最多可打10个标签</p>
    <input type="text" name="tags" placeholder="标签" class="block mar-btm">
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
