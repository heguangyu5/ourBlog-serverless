<?php

class OurBlog_Controller_Action_PostLogin extends OurBlog_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->uid) {
            return $this->response(null, 'LOGIN_TIMEOUT');
        }
    }
}
