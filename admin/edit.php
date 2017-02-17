<?php
    include __DIR__ . '/check-login.php';
    include __DIR__ . '/../db.php';

    $error = false;
    if ($_POST) {
        include __DIR__ . '/prevent-csrf.php';
        try {
            $requiredKeys = array(
                // key => required
                'id'       => true,
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
            // id
            $_POST['id'] = filter_var($_POST['id'], FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 1)
            ));
            if (!$_POST['id']) {
                throw new InvalidArgumentException('invalid id');
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
            // 验证权限
            $post = $db->query('SELECT id, external_post FROM posts WHERE id = ' . $_POST['id'] . ' AND uid = ' . $uid)->fetch(PDO::FETCH_OBJ);
            if (!$post) {
                throw new InvalidArgumentException('you can only edit your own post');
            }
            // content
            $len = strlen($_POST['content']);
            if ($post->external_post) {
                if ($len > 1000) {
                    throw new InvalidArgumentException('external post url too long');
                }
                if (!preg_match('#^https?://[^"]+$#', $_POST['content'])) {
                    throw new InvalidArgumentException('invalid external post url');
                }
            } else {
                if ($len > 64000) {
                    throw new InvalidArgumentException('content maxlength is 64000');
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
                $tagIds  = array();
                foreach ($tagIdMap as $tag => $tagId) {
                    if ($tagId) {
                        $tagIds[] = $tagId;
                    } else {
                        $newTags[] = $tag;
                    }
                }
                // 取得post原有的tag
                $stmt = $db->query('SELECT tag_id FROM post_tag WHERE post_id = ' . $_POST['id']);
                $postTagIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
                // diff
                $tagIdsToBeAdded   = array_diff($tagIds, $postTagIds);
                $tagIdsToBeDeleted = array_diff($postTagIds, $tagIds);
            }
            $updateDate = date('Y-m-d H:i:s');

            $db->beginTransaction();
            try {
                // post
                $stmt = $db->prepare("UPDATE posts SET category = ?, title = ?, content = ?, update_date = ? WHERE id = ?");
                $stmt->execute(array(
                    $_POST['category'],
                    $_POST['title'],
                    $_POST['content'],
                    $updateDate,
                    $_POST['id']
                ));
                // tag
                if ($hasTag) {
                    // newTags
                    if ($newTags) {
                        $stmtTag     = $db->prepare('INSERT INTO tag(name) VALUES (?)');
                        $stmtPostTag = $db->prepare('INSERT INTO post_tag(post_id, tag_id) VALUES (?, ?)');
                        foreach ($newTags as $tag) {
                            $stmtTag->execute(array($tag));
                            $stmtPostTag->execute(array($_POST['id'], $db->lastInsertId()));
                        }
                    }
                    // toBeAdded
                    if ($tagIdsToBeAdded) {
                        if (!$newTags) {
                            $stmtPostTag = $db->prepare('INSERT INTO post_tag(post_id, tag_id) VALUES (?, ?)');
                        }
                        foreach ($tagIdsToBeAdded as $tagId) {
                            $stmtPostTag->execute(array($_POST['id'], $tagId));
                        }
                    }
                    // toBeDeleted
                    if ($tagIdsToBeDeleted) {
                        $db->exec('DELETE FROM post_tag WHERE post_id = ' . $_POST['id'] . ' AND tag_id IN (' . implode(',', $tagIdsToBeDeleted) . ')');
                    }
                } else {
                    $db->exec('DELETE FROM post_tag WHERE post_id = ' . $_POST['id']);
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
        $post = $db->query("SELECT category, title, content, external_post FROM posts WHERE id = $id AND uid = $uid")->fetch(PDO::FETCH_OBJ);
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
    <?php if ($post->external_post): ?>
    <label class="block mar-btm">外部文章</label>
    <input type="text" name="content" placeholder="http(s)://" value="<?php echo $post->content; ?>">
    <?php else: ?>
    <div id="editormd">
        <textarea name="content" class="hide"><?php echo htmlspecialchars($post->content); ?></textarea>
    </div>
    <?php endif; ?>
    <p>多个标签使用,号分隔,最多可打10个标签</p>
    <?php
        $stmt = $db->query("SELECT t.name FROM post_tag pt, tag t WHERE pt.post_id = $id AND pt.tag_id = t.id ORDER BY pt.id ASC");
        $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $tags = implode(',', $tags);
    ?>
    <input type="text" name="tags" placeholder="标签" class="block mar-btm" value="<?php echo htmlspecialchars($tags); ?>">
    <button type="submit">提交</button>
</form>

<?php if ($post->external_post): ?>
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
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
