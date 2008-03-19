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
 * TypechoValidationError::display的别名
 * 
 * @param string $errorName 错误名称
 * @param string $format 错误格式化字符串
 * @return void
 */
function validationError($errorName, $format = '%s')
{
    TypechoValidationError::display($errorName, $format);
}

/**
 * TypechoValidationError::displayList的别名
 * 
 * @param string $tag 错误列表html标签
 * @return void
 */
function validationErrorList($tag = 'li')
{
    TypechoValidationError::displayList($tag);
}

/**
 * TypechoValidationError::match的别名
 * 
 * @param string $errorName 错误名称
 * @param string $string 需要显示的字符
 * @return void
 */
function validationErrorMatch($errorName, $string)
{
    TypechoValidationError::match($errorName, $string);
}

/**
 * 验证类错误信息
 * 
 * @package Validation
 */
class TypechoValidationError
{
    /**
     * 错误信息数组
     * 
     * @access private
     * @var array
     */
    private static $_errorMessage = array();

    /**
     * 初始化错误信息
     * 
     * @access public
     * @param array $errorMessage 错误信息数组
     * @return void
     */
    public function __construct(array $errorMessage)
    {
        setCookie('typechoErrorMessage', serialize($errorMessage), 0, typechoGetSiteRoot());
        self::$_errorMessage = $errorMessage;
    }
    
    /**
     * 读取错误信息数据
     * 
     * @access private
     * @return void
     */
    private static function getError()
    {
        if(!empty($_COOKIE['typechoErrorMessage']))
        {
            self::$_errorMessage = unserialize($_COOKIE['typechoErrorMessage']);
            setCookie('typechoErrorMessage', '', 0, typechoGetSiteRoot());
        }
    }
    
    /**
     * 列出错误数据
     * 
     * @access public
     * @param string $tag 错误列表html标签
     * @return void
     */
    public static function displayList($tag = 'li')
    {
        self::getError();
        
        foreach(self::$_errorMessage as $error)
        {
            echo "<{$tag}>" . $error . "</{$tag}>";
        }
    }
    
    /**
     * 显示错误
     * 
     * @access public
     * @param string $errorName 错误名称
     * @param string $format 错误格式化字符串
     * @return void
     */
    public static function display($errorName, $format = '%s')
    {
        self::getError();
        
        if(isset(self::$_errorMessage[$errorName]))
        {
            printf($format, self::$_errorMessage[$errorName]);
        }
    }
    
    /**
     * 获取错误数据
     * 
     * @access public
     * @param string $tag 错误列表html标签
     * @return void
     */
    public static function get($tag = NULL)
    {
        self::getError();
        
        if($tag)
        {
            $str = '';
            
            foreach(self::$_errorMessage as $error)
            {
                $str .= "<{$tag}>" . $error . "</{$tag}>";
            }
        }
        else
        {
            return self::$_errorMessage;
        }
    }
    
    /**
     * 根据错误显示相应字符
     * 
     * @access public
     * @param string $errorName 错误名称
     * @param string $string 需要显示的字符
     * @return void
     */
    public static function match($errorName, $string)
    {
        self::getError();
    
        if(isset(self::$_errorMessage[$errorName]))
        {
            echo $string;
        }
    }
}
