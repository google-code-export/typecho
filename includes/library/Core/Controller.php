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
 * @package Core
 */
abstract class TypechoController
{    
    /**
     * 构造函数,定义执行步骤
     *
     */
    public function __construct()
    {
        //关闭魔术引号功能
        if(get_magic_quotes_gpc())
        {
            $_GET = typechoStripslashesDeep($_GET);
            $_POST = typechoStripslashesDeep($_POST);
            $_COOKIE = typechoStripslashesDeep($_COOKIE);
        
            reset($_GET);
            reset($_POST);
            reset($_COOKIE);
        }
        
        //设置默认时区
        if(!ini_get("date.timezone") && function_exists("date_default_timezone_set"))
        {
            @date_default_timezone_set('UTC');
        }
        
        header('content-Type: text/html;charset= UTF-8');
        
        $this->renderResponse();
    }
    
    /**
     * 回执函数
     *
     * @return void
     */
    protected abstract function renderResponse();
}
