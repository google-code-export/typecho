<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * Typecho控制器基类
 * 
 * @package TypechoCore
 */
abstract class TypechoController
{    
    public function __construct()
    {
        if(!__TYPECHO_DEBUG__)
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
        else
        {
                $this->processValidation();
                $this->renderResponse();
        }
    }
    
    protected function processValidation()
    {
        if(get_magic_quotes_gpc())
        {
            $_GET = typechoStripslashesDeep($_GET);
            $_POST = typechoStripslashesDeep($_POST);
            $_COOKIE = typechoStripslashesDeep($_COOKIE);
        
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
