<?php

class OurBlog_Controller_Action extends Zend_Controller_Action
{
    public function getQuery($key = null, $default = null)
    {
        return $this->getRequest()->getQuery($key, $default);
    }

    public function getPost($key = null, $default = null)
    {
        return $this->getRequest()->getPost($key, $default);
    }

    protected function disableLayoutAndView()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function setLayout($layoutName)
    {
        $this->_helper->layout->setLayout($layoutName);
    }

    protected function disableLayout()
    {
        $this->_helper->layout->disableLayout();
    }

    protected function downloadFile($dir, $filename)
    {
        $path = $dir . '/' . $filename;
        if (!is_file($path)) {
            die('file not exists');
        }

        header('Content-Type: application/octet-stream');
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
            $filename = rawurlencode($filename);
        }
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        readfile($path);
    }
}
