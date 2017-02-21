<?php

class OurBlog_Util
{
    public static function DBAIPK($var)
    {
        return filter_var($var, FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1)
        ));
    }

    public static function killCSRF()
    {
        if (!isset($_SERVER['HTTP_REFERER'])) {
            throw new InvalidArgumentException('missing HTTP_REFERER');
        }
        if (!preg_match('#^http://([^/]+)#', $_SERVER['HTTP_REFERER'], $matches)) {
            throw new InvalidArgumentException('invalid HTTP_REFERER');
        }
        if ($_SERVER['SERVER_NAME'] != $matches[1]) {
            throw new InvalidArgumentException('SERVER_NAME and HTTP_REFERER mismatch');
        }
    }
}
