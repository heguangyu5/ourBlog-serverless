<?php
    session_start();
    if (isset($_SESSION['uid'])) {
        header('Location: ./index.php');
        exit;
    }

    $error = false;
    if ($_POST) {
        include __DIR__ . '/prevent-csrf.php';
        include __DIR__ . '/../db.php';
        include __DIR__ . '/../lib/OurBlog/Util.php';
        include __DIR__ . '/../lib/OurBlog/Auth.php';
        try {
            $auth  = new OurBlog_Auth($db);
            $uid   = $auth->authenticate(OurBlog_Util::getPost('email'), OurBlog_Util::getPost('password'));
            if ($uid) {
                session_regenerate_id(true);
                $_SESSION['uid']   = $uid;
                $_SESSION['email'] = $_POST['email'];
                header('Location: ./index.php');
                exit;
            }
            $error = '用户名或密码不对';
        } catch (InvalidArgumentException $e) {
            $error = '参数不对';
        } catch (Exception $e) {
            $error = 'Server Error';
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
    <div class="margin-center max-width-1200">
        <h1 class="border-btm text-lft">OurBlog</h1>
        <?php
            if ($error) {
                echo '<p class="text-red">', $error, '</p>';
            }
        ?>
        <form method="POST">
            <div class="mar-btm">
                <label class="inline-block w100 text-rgt">E-mail:</label>
                <input type="text" name="email" class="w200">
            </div>
            <div class="mar-btm">
                <label class="inline-block w100 text-rgt">密码:</label>
                <input type="password" name="password" class="w200">
            </div>
            <button type="submit">登录</button>
        </form>
    </div>
</body>
</html>
