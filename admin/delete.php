<?php
    include __DIR__ . '/prevent-csrf.php';
    include __DIR__ . '/check-login.php';

    include __DIR__ . '/../db.php';
    include __DIR__ . '/../lib/OurBlog/Util.php';
    include __DIR__ . '/../lib/OurBlog/Post.php';
    try {
        $post = new OurBlog_Post($db, $uid);
        $post->delete(OurBlog_Util::getQuery('id'));
        header('Location: ./index.php');
    } catch (InvalidArgumentException $e) {
        die('参数不对');
    } catch (Exception $e) {
        die('Server Error');
    }
