<?php
    include __DIR__ . '/check-login.php';
    include __DIR__ . '/../db.php';

    $error = false;
    if ($_POST) {
        try {
            $requiredKeys = array('category', 'title', 'content');
            foreach ($requiredKeys as $key) {
                if (!isset($_POST[$key])) {
                    throw new InvalidArgumentException("missing required key $key");
                }
                $_POST[$key] = trim($_POST[$key]);
                if (empty($_POST[$key])) {
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
        } catch (InvalidArgumentException $e) {
            $error = '参数不对';
        }

        if (!$error) {
            $createDate = date('Y-m-d H:i:s');
            try {
                $stmt = $db->prepare('INSERT INTO posts(uid, category, title, content, create_date, update_date) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute(array(
                    $uid,
                    $_POST['category'],
                    $_POST['title'],
                    $_POST['content'],
                    $createDate,
                    $createDate
                ));
            } catch (Exception $e) {
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
    <textarea name="content" placeholder="正文" class="block mar-btm"></textarea>
    <button type="submit">提交</button>
</form>

<?php include __DIR__ . '/footer.php'; ?>
