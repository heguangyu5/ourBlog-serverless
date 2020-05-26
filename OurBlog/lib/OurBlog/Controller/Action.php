<?php

class OurBlog_Controller_Action
{
    protected $uid;
    protected $ost;

    public function preDispatch()
    {
        $uid = $this->getParam('uid');
        $ost = $this->getParam('ost');
        if ($uid && $ost && OurBlog_Auth::isValidOST($ost, $uid)) {
            $this->uid = $uid;
            $this->ost = $ost;
        }
    }

    public function response($data, $response = 'SUCCESS')
    {
        $ret = array('response' => $response);
        if ($data !== null) {
            $ret['data'] = $data;
        }
        return $ret;
    }

    public function failed($msg)
    {
        return $this->response($msg, 'FAILED');
    }

    public function invalidParams($msg = null)
    {
        return $this->response($msg, 'INVALID_PARAMS');
    }

    public function errorOccurred($msg = null)
    {
        return $this->response($msg, 'ERROR_OCCURRED');
    }

    public function notFound404($msg = null)
    {
        return $this->response($msg, '404');
    }

    public function getQuery($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }

        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    public function getPost($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }

        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    public function getParam($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST + $_GET;
        }

        $param = $this->getPost($key);
        if ($param !== null) {
            return $param;
        }

        return $this->getQuery($key, $default);
    }
}
