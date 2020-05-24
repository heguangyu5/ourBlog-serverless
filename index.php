<?php

define('APP_NAME',        'OurBlog');
define('APP_PATH',        '/opt/' . APP_NAME);
define('APP_MODULE_PATH', APP_PATH . '/module');
define('APP_LIB_PATH',    APP_PATH . '/lib');

set_include_path('/opt' . PATH_SEPARATOR . get_include_path());

spl_autoload_register(function ($class) {
    include APP_LIB_PATH . '/' . str_replace('_', '/', $class) . '.php';
}, true, true);

function main_handler($event, $context)
{
    // $_SERVER
    $_SERVER = array(
        'HTTP_HOST'       => $event->headers->host,
        'SERVER_NAME'     => $event->headers->host,
        'HTTP_REFERER'    => isset($event->headers->referer) ? $event->headers->referer : '',
        'HTTP_USER_AGENT' => isset($event->headers->{'user-agent'}) ? $event->headers->{'user-agent'} : '',
        'REMOTE_ADDR'     => $event->requestContext->sourceIp
    ) + $_SERVER;
    // /module/controller/action
    $moduleControllerAction = rtrim(
        substr($event->path, strlen($event->requestContext->path)),
        '/'
    );
    if ($moduleControllerAction == '') {
        $module     = 'default';
        $controller = 'index';
        $action     = 'index';
    } elseif (preg_match('#^(/[-a-zA-Z0-9]+){1,3}$#', $moduleControllerAction)) {
        $moduleControllerAction = explode('/', $moduleControllerAction);
        $count = count($moduleControllerAction);
        if ($count == 2) {
            $module     = 'default';
            $controller = $moduleControllerAction[1];
            $action     = 'index';
        } elseif ($count == 3) {
            $module     = 'default';
            $controller = $moduleControllerAction[1];
            $action     = $moduleControllerAction[2];
        } elseif ($count == 4) {
            $module     = $moduleControllerAction[1];
            $controller = $moduleControllerAction[2];
            $action     = $moduleControllerAction[3];
        }
        $module     = str_replace('-', ' ', $module);
        $module     = str_replace(' ', '', ucwords($module));
        $controller = str_replace('-', ' ', $controller);
        $controller = str_replace(' ', '', ucwords($controller));
        $action     = str_replace('-', ' ', $action);
        $action     = str_replace(' ', '', ucwords($action));
    } else {
        return array('response' => 'BAD_REQUEST');
    }
    $controllerClassName = $controller . 'Controller';
    if ($module != 'default') {
        $controllerClassName = $module . '_' . $controllerClassName;
    }
    if (!class_exists($controllerClassName, false)) {
        $controllerPath = APP_MODULE_PATH . '/' . $module . '/' . $controller . 'Controller.php';
        if (is_file($controllerPath)) {
            include $controllerPath;
            if (!class_exists($controllerClassName, false)) {
                return array('response' => '404');
            }
        } else {
            return array('response' => '404');
        }
    }
    $actionName = $action . 'Action';
    if (!method_exists($controllerClassName, $actionName)) {
        return array('response' => '404');
    }
    // $_GET
    $_GET = (array)$event->queryString;
    // $_POST
    if ($event->httpMethod == 'POST') {
        if (   $event->headers->{'content-type'} == 'application/x-www-form-urlencoded'
            && $event->body
        ) {
            parse_str($event->body, $_POST);
        } else {
            $_POST = array();
        }
    } else {
        $_POST = array();
    }
    // call
    $controller = new $controllerClassName();
    if (method_exists($controller, 'init')) {
        $res = $controller->init();
        if ($res !== null) {
            return $res;
        }
    }
    if (method_exists($controller, 'preDispatch')) {
        $res = $controller->preDispatch();
        if ($res !== null) {
            return $res;
        }
    }
    return $controller->$actionName();
}
