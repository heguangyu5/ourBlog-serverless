<?php

class OurBlog_Util
{
    public static function getQuery($key)
    {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    public static function getPost($key)
    {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    public static function DBAIPK($var)
    {
        return filter_var($var, FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1)
        ));
    }
}
