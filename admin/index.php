<?php
    include __DIR__ . '/check-login.php';

    $headTitle = '后台首页';
    include __DIR__ . '/header.php';
?>

<div class="text-lft">
<?php
    include __DIR__ . '/../db.php';
    $stmt = $db->query("SELECT id, title FROM posts WHERE uid = $uid ORDER BY id DESC", PDO::FETCH_OBJ);
    foreach ($stmt as $row):
?>
    <div class="mar-btm">
        <?php echo htmlspecialchars($row->title); ?>
        <div class="pull-right">
            <a href="edit.php">编辑</a>
            <a href="delete.php?id=<?php echo $row->id; ?>">删除</a>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
