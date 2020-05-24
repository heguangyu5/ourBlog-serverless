<?php

class FileController extends OurBlog_Controller_Action
{
    public function indexAction()
    {
        $this->disableLayoutAndView();

        try {
            $uid = OurBlog_Util::DBAIPK($this->getQuery('uid'));
            if (!$uid) {
                throw new InvalidArgumentException('invalid uid');
            }
            $filename = trim($this->getQuery('filename'));
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
        } catch (InvalidArgumentException $e) {
            die('参数不对');
        }

        $this->downloadFile(APPLICATION_PATH . '/../data/uploads/' . $uid, $filename);
    }
}
