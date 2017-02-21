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
}
