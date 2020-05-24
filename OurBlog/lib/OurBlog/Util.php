<?php

class OurBlog_Util
{
    public static function DBAIPK($var)
    {
        return filter_var($var, FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1)
        ));
    }
}
