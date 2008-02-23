<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

abstract class TypechoController
{    
    public function __construct()
    {
        try
        {
            $this->processValidation();
            $this->renderResponse();
        }
        catch(TypechoException $exception)
        {
            $this->renderExceptionResponse($exception);
        }
    }
    
    protected function processValidation()
    {
        if(get_magic_quotes_gpc())
        {
            $_GET = ltStripslashesDeep($_GET);
            $_POST = ltStripslashesDeep($_POST);
            $_COOKIE = ltStripslashesDeep($_COOKIE);
        
            reset($_GET);
            reset($_POST);
            reset($_COOKIE);
        }
        
        if(!ini_get("date.timezone") && function_exists("date_default_timezone_set"))
        {
            @date_default_timezone_set('UTC');
        }
    }
    
    abstract protected function renderResponse();
    
    protected function renderExceptionResponse(TypechoException $exception)
    {
        
    }
}
