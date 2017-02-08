<?php
    $headTitle = '首页';
    include __DIR__ . '/header.php';
?>

<div class="text-lft">
<?php
    $category = null;
    if (isset($_GET['category'])) {
        $category = filter_var($_GET['category'], FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1)
        ));
    }

    $sql = 'SELECT id, title FROM posts';
    if ($category) {
        $sql .= " WHERE category = $category";
    }
    $sql .= ' ORDER BY id DESC';
    $stmt = $db->query($sql, PDO::FETCH_OBJ);
    foreach ($stmt as $row):
?>
    <a href="post.php?id=<?php echo $row->id; ?>" target="_blank" class="block mar-btm">
        <?php echo htmlspecialchars($row->title); ?>
    </a>
    <?php endforeach; ?>
</div>
