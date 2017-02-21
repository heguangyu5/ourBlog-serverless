<?php

class AdminController extends OurBlog_Controller_Action_PostLogin
{
    public function indexAction()
    {}

    public function addAction()
    {
        if ($_POST) {
            try {
                OurBlog_Util::killCSRF();
                $post = new OurBlog_Post($this->uid);
                $post->add($_POST);
                $this->redirect('/admin/');
            } catch (InvalidArgumentException $e) {
                $this->view->error = '参数不对';
            } catch (Exception $e) {
                $this->view->error = 'Server Error';
            }
        }
    }
}
