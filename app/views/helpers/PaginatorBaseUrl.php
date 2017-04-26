<?php

class Zend_View_Helper_PaginatorBaseUrl
{
    public function paginatorBaseUrl($ignoreParams = null)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params  = $request->getQuery();

        unset($params['page']);

        if ($ignoreParams) {
            if (is_array($ignoreParams)) {
                foreach ($ignoreParams as $key) {
                    unset($params[$key]);
                }
            } else {
                unset($params[$ignoreParams]);
            }
        }

        $baseUrl = '/' . $request->getControllerName() . '/' . $request->getActionName() . '/?';
        foreach ($params as $key => $value) {
            $key = urlencode($key);
            if (is_array($value)) {
                foreach ($value as $val) {
                    $val = urlencode($val);
                    $baseUrl .= $key . "[]=$val&";
                }
            } else {
                $value = urlencode($value);
                $baseUrl .= "$key=$value&";
            }
        }

        return $baseUrl . 'page=';
    }
}
