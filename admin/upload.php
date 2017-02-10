<?php

include __DIR__ . '/prevent-csrf.php';

header('Content-Type:application/json;charset=UTF-8');

session_start();
if (!isset($_SESSION['uid'])) {
    echo json_encode(array(
        'success' => 0,
        'message' => 'session timeout'
    ));
    exit;
}

$uid = $_SESSION['uid'];
$dir = __DIR__ . '/../uploads/' . $uid;
umask(0);
if (!is_dir($dir) && !mkdir($dir)) {
    echo json_encode(array(
        'success' => 0,
        'message' => 'mkdir error'
    ));
    exit;
}

try {
    if (!isset($_FILES['editormd-image-file'])) {
        throw new InvalidArgumentException('没有收到上传的文件');
    }
    $file = $_FILES['editormd-image-file'];
    if ($file['error'] != UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('上传过程中出错');
    }
    // 如果文件名中有/\,则不接受, .和..也不行
    $filename = trim($file['name']);
    $len = strlen($filename);
    if ($len == 0 || $len > 200) {
        throw new InvalidArgumentException('文件名太长了');
    }
    if (strpos($filename, '/') !== false
        || strpos($filename, '\\') !== false
        || $filename == '.'
        || $filename == '..'
    ) {
        throw new InvalidArgumentException('无效的文件名');
    }
    // 如果文件已存在,则不接受
    $dstPath = $dir . '/' . $filename;
    if (file_exists($dstPath)) {
        throw new InvalidArgumentException('此文件已存在');
    }
    // 由于图片需要显示出来,所以必须严格验证
    $ext = explode('.', $filename);
    $ext = strtolower(array_pop($ext));
    $mimeTypes = array(
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'zip' => 'application/zip'
    );
    if (isset($mimeTypes[$ext])) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $type  = $finfo->file($file['tmp_name']);
        if ($type != $mimeTypes[$ext]) {
            throw new InvalidArgumentException('文件扩展名和实际的类型不匹配');
        }
    } else {
        throw new InvalidArgumentException('不允许上传此类型的文件');
    }
    // 完成上传
    if (!move_uploaded_file($file['tmp_name'], $dstPath)) {
        throw new InvalidArgumentException('保存文件时出错');
    }
} catch (InvalidArgumentException $e) {
    echo json_encode(array(
        'success' => 0,
        'message' => $e->getMessage()
    ));
    exit;
}

echo json_encode(array(
    'success' => 1,
    'message' => '上传成功',
    'url'     => "/uploads/$uid/" . urlencode($filename)
));
