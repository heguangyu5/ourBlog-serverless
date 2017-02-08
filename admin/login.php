<?php
    session_start();
    if (isset($_SESSION['uid'])) {
        header('Location: ./index.php');
        exit;
    }

    $error = false;
    if ($_POST) {
        try {
            $requiredKeys = array('email', 'password');
            foreach ($requiredKeys as $key) {
                if (!isset($_POST[$key])) {
                    throw new InvalidArgumentException("missing required key $key");
                }
            }
            // email
            $len = strlen($_POST['email']);
            if ($len < 5 || $len > 200) {
                throw new InvalidArgumentException('invalid email, length limit 5~200');
            }
            $_POST['email'] = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            if (!$_POST['email']) {
                throw new InvalidArgumentException('email format wrong');
            }
            // password
            $len = strlen($_POST['password']);
            if ($len < 6 || $len > 50) {
                throw new InvalidArgumentException('invalid password, length limit 6~50');
            }
        } catch (InvalidArgumentException $e) {
            $error = '参数不对';
        }

        if (!$error) {
            $salt = 'EYFXOEII/T3Y/75D0pUXbz5bqxVIpo7qMipQ7MtnPaUHIvX1nDKgU6KfLf9JpYAvjO7dacpgt8C/';
            $_POST['password'] = md5($salt . '-' . $_POST['password']);
            // login
            include __DIR__ . '/../db.php';
            $stmt = $db->prepare('SELECT uid FROM user WHERE email = ? AND password = ?');
            $stmt->execute(array($_POST['email'], $_POST['password']));
            $uid  = $stmt->fetchColumn();
            if ($uid) {
                $_SESSION['uid'] = $uid;
                header('Location: ./index.php');
                exit;
            }
            $error = '用户名或密码不对';
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录 - OurBlog</title>
    <link href="../style.css" rel="stylesheet">
</head>
<body>
    <div class="margin-center max-width-800">
        <h1 class="border-btm text-lft">OurBlog</h1>
        <?php
            if ($error) {
                echo '<p class="text-red">', $error, '</p>';
            }
        ?>
        <form method="POST">
            <div class="mar-btm">
                <label class="inline-block w100 text-rgt">E-mail:</label>
                <input type="text" name="email">
            </div>
            <div class="mar-btm">
                <label class="inline-block w100 text-rgt">密码:</label>
                <input type="password" name="password">
            </div>
            <button type="submit">登录</button>
        </form>
    </div>
</body>
</html>
