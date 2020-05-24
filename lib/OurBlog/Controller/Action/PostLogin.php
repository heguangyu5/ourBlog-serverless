<?php

class OurBlog_Controller_Action_PostLogin extends OurBlog_Controller_Action
{
    protected $uid;
    protected $ost;

    public function preDispatch()
    {
        parent::preDispatch();

        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->redirect('/login/', array('exit' => false));
            return;
        }

        $identity = $auth->getIdentity();
        $this->uid = $identity['uid'];
        $this->ost = $identity['ost'];
        $this->view->uid = $this->uid;
        $this->view->ost = $this->ost;

        $this->setLayout('layout-post-login');
    }
}
