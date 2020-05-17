<?php

class LoginController extends OurBlog_Controller_Action
{
    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->redirect('/admin/');
        }
    }

    public function indexAction()
    {
        if ($_POST) {
            try {
                OurBlog_Util::killCSRF();
                $auth    = Zend_Auth::getInstance();
                $adapter = new OurBlog_Auth($this->getPost('email'), $this->getPost('password'));
                $result  = $auth->authenticate($adapter);
                if ($result->isValid()) {
                    session_regenerate_id(true);
                    $_SESSION['email'] = $_POST['email'];
                    $this->redirect('/admin/');
                }
                $this->view->error = '用户名或密码不对';
            } catch (InvalidArgumentException $e) {
                $this->view->error = '参数不对';
            } catch (Exception $e) {
                $this->view->error = 'Server Error';
            }
        }

        $this->setLayout('layout-general');
    }
}
