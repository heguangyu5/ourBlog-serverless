<?php

class LoginController extends OurBlog_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if ($this->uid) {
            return $this->response(array(
                'uid' => $this->uid,
                'ost' => $this->ost
            ));
        }
    }

    public function indexAction()
    {
        try {
            $auth = new OurBlog_Auth($this->getPost('email'), $this->getPost('password'));
            $res  = $auth->authenticate();
            if ($res) {
                return $this->response($res);
            } else {
                return $this->failed('用户名或密码不对');
            }
        } catch (InvalidArgumentException $e) {
            return $this->invalidParams();;
        } catch (Exception $e) {
            return $this->errorOccurred();
        }
    }
}
