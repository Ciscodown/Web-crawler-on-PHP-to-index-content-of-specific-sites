<?php

class ErrorController extends Zend_Controller_Action
{
 
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
 
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
 
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
		        $this->view->stack_trace = $this->_getFullErrorMessage($errors);
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
		        $this->view->stack_trace = $this->_getFullErrorMessage($errors);
                break;
        }

	    $this->view->headTitle()->prepend( 'Error' );

        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
    }


    protected function _getFullErrorMessage($error)
    {
    	if (APPLICATION_ENV != 'development')
    	{
    	    return '';
    	}

        $message = '';

        if (!empty($_SERVER['SERVER_ADDR'])) {
            $message .= "Server IP: " . $_SERVER['SERVER_ADDR'] . "\n";
        }

        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $message .= "User agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $message .= "Request type: " . $_SERVER['HTTP_X_REQUESTED_WITH'] . "\n";
        }

        $message .= "Server time: " . date("Y-m-d H:i:s") . "\n";
        $message .= "RequestURI: " . $error->request->getRequestUri() . "\n";

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $message .= "Referer: " . $_SERVER['HTTP_REFERER'] . "\n";
        }

        $message .= "Message: " . $error->exception->getMessage() . "\n\n";
        $message .= "Trace:\n" . $error->exception->getTraceAsString() . "\n\n";
        $message .= "Request data: " . var_export($error->request->getParams(), true) . "\n\n";
        
        if (!empty($_SESSION)) {
            $it = $_SESSION;
    
            $message .= "Session data:\n\n";
            foreach ($it as $key => $value) {
                $message .= $key . ": " . var_export($value, true) . "\n";
            }
            $message .= "\n";
        }

        if (!empty($_COOKIES)) {
            $message .= "Cookie data:\n\n";
            foreach ($_COOKIES as $key => $value) {
                $message .= $key . ": " . var_export($value, true) . "\n";
            }
            $message .= "\n";
        }

        return $message;
    }

}