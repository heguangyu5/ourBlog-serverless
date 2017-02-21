<?php

class ErrorController extends OurBlog_Controller_Action
{
    public function indexAction()
    {
        die('you should not be here');
    }

    public function errorAction()
    {
        $this->setLayout('layout-general');

        // @see Zend_Controller_Plugin_ErrorHandler::_handleError()
        $error = $this->getParam('error_handler');
        if (!$error || !$error instanceof ArrayObject) {
            $responseCode = 404;
        } else {
            switch ($error->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $responseCode = 404;
                break;
            default:
                $responseCode = 500;
                break;
            }
            if (APPLICATION_ENV == 'development') {
                $this->view->exception = $error->exception;
                $this->view->request   = $error->request;
            }
        }

        $this->getResponse()->setHttpResponseCode($responseCode);
        echo $this->render($responseCode);
    }
}
