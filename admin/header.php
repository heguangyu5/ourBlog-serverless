<?php
    if (!isset($headTitle)) {
        echo 'you should set $headTitle to include this file';
        exit;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($headTitle); ?> - OurBlog</title>
    <link href="../style.css" rel="stylesheet">
</head>
<body>
    <div class="margin-center max-width-1200">
        <h1 class="border-btm text-lft">
            <div class="pull-right text-sm" style="padding-top:18px">
                <a href="javascript:void(0);"><?php echo htmlspecialchars($_SESSION['email']); ?></a>
                <a href="logout.php">退出</a>
            </div>
            <a href="index.php" class="no-underline mar-rgt-lg">OurBlog</a>
            <a href="index.php" class="text-sm">博文管理</a>
            <a href="add.php" class="text-sm">写博文</a>
        </h1>
