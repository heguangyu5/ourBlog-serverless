<?php

class LogoutController extends OurBlog_Controller_Action_PostLogin
{
    public function indexAction()
    {
        try {
            OurBlog_Util::killCSRF();
        } catch (InvalidArgumentException $e) {
            $this->redirect('/admin/');
        }

        Zend_Session::destroy();

        $this->redirect('/login/');
    }
}
