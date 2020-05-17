<?php

define('APPLICATION_PATH', '/opt/serverless_ourblog_app');
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

set_include_path('/opt' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Application.php';

function main_handler($event, $context)
{
    if (APPLICATION_ENV != 'development') {
        $baseUrl = substr($event->path, strlen($event->requestContext->path));
        $_SERVER += array(
            'HTTPS'           => 'on',
            'REQUEST_METHOD'  => $event->httpMethod,
            'REQUEST_URI'     => $baseUrl . '?' . http_build_query($event->queryString),
            'HTTP_HOST'       => $event->headers->host,
            'HTTP_USER_AGENT' => isset($event->headers->{'user-agent'}) ? $event->headers->{'user-agent'} : '',
            'REMOTE_ADDR'     => $event->requestContext->sourceIp
        );
        $_GET = (array)$event->queryString;
        if ($event->httpMethod == 'POST') {
            if (   $event->headers->{'content-type'} == 'application/x-www-form-urlencoded'
                && $event->body
            ) {
                $_POST = parse_str($event->body, $_POST);
            }
        }
    }

    $app = new Zend_Application(
        APPLICATION_ENV,
        array(
            'autoloadernamespaces' => array('OurBlog_'),
            'resources' => array(
                'frontController' => array('controllerDirectory' => APPLICATION_PATH . '/controllers'),
                'layout' => array('layoutpath' => APPLICATION_PATH . '/layouts/scripts')
            )
        )
    );

    $bootstrap = $app->getBootstrap();
    $bootstrap->bootstrap();

    $front = $bootstrap->getResource('frontController');
    $front->returnResponse(true);
    if (APPLICATION_ENV != 'development') {
        $front->setBaseUrl('');
    }

    $response = $bootstrap->run();

    if (APPLICATION_ENV != 'development') {
        $headers = array();
        foreach ($response->getHeaders() as $header) {
            if ($header['replace'] || !isset($headers[$header['name']])) {
                $headers[$header['name']] = $header['value'];
            }
        }
        return array(
            'isBase64Encoded' => false,
            'statusCode'      => $response->getHttpResponseCode(),
            'headers'         => $headers + array('Content-Type' => 'text/html'),
            'body'            => $response->getBody()
        );
    } else {
        $response->sendResponse();
    }
}

if (APPLICATION_ENV == 'development') {
    main_handler(null, null);
}
