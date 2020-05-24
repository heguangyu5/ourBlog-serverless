<?php

class LoginController extends OurBlog_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->redirect('/admin/', array('exit' => false));
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
                    $identity = $result->getIdentity();
                    $this->redirect(
                        '/admin?uid=' . $identity['uid'] . '&ost=' . $identity['ost'],
                        array('exit' => false)
                    );
                    return;
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
