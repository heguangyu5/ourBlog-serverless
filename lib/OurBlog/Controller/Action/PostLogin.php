<?php

class OurBlog_Controller_Action_PostLogin extends OurBlog_Controller_Action
{
    protected $uid;

    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->redirect('/login/');
        }

        $this->uid = $auth->getIdentity();
        $this->view->uid = $this->uid;

        $this->setLayout('layout-post-login');
    }
}
