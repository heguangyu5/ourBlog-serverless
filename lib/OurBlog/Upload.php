<?php

class OurBlog_Upload
{
    protected $imageDir;
    protected $fileDir;

    public function __construct($uid, $imageDir, $fileDir)
    {
        $this->imageDir = $imageDir . '/' . $uid;
        $this->fileDir  = $fileDir . '/' . $uid;

        umask(0);
        if (!is_dir($this->imageDir) && !mkdir($this->imageDir)) {
            throw new Exception('mkdir error: image dir');
        }
        if (!is_dir($this->fileDir) && !mkdir($this->fileDir)) {
            throw new Exception('mkdir error: file dir');
        }
    }

    public function upload()
    {
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
        // 由于图片需要显示出来,所以必须严格验证
        $ext = explode('.', $filename);
        $ext = strtolower(array_pop($ext));
        $mimeTypes = array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        );
        if (isset($mimeTypes[$ext])) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $type  = $finfo->file($file['tmp_name']);
            if ($type != $mimeTypes[$ext]) {
                throw new InvalidArgumentException('文件扩展名和实际的类型不匹配');
            }
            $dstPath = $this->imageDir . '/' . $filename;
            $resultAccess = 'direct';
        } else {
            $dstPath = $this->fileDir . '/' . $filename;
            $resultAccess = 'php';
        }
        // 如果文件已存在,则不接受
        if (file_exists($dstPath)) {
            throw new InvalidArgumentException('此文件已存在');
        }
        // 完成上传
        if (!move_uploaded_file($file['tmp_name'], $dstPath)) {
            throw new InvalidArgumentException('保存文件时出错');
        }

        return array(
            'access'   => $resultAccess,
            'filename' => $filename
        );
    }
}
