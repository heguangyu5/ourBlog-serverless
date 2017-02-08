<?php
    if (!isset($headTitle)) {
        echo 'you should set $headTitle to include this file';
        exit;
    }

    if (!isset($db)) {
        include __DIR__ . '/db.php';
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($headTitle); ?> - OurBlog</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="margin-center max-width-1200">
        <h1 class="border-btm text-lft">
            <a href="index.php" class="no-underline mar-rgt-lg">OurBlog</a>
            <a href="index.php" class="text-sm mar-rgt">首页</a>
            <?php
                $stmt = $db->query('SELECT id, name FROM category', PDO::FETCH_OBJ);
                foreach ($stmt as $row) {
                    echo '<a href="index.php?category=', $row->id, '" class="text-sm mar-rgt">', htmlspecialchars($row->name), '</a>';
                }
            ?>
        </h1>
