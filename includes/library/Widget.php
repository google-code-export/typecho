<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 定义组建路径 **/
define('__TYPECHO_WIDGET_DIR__', './widget');

/**
 * Typecho组件基类
 * 
 * @param string $widget 组件名称
 * @param mixed $param 参数
 * @return TypechoWidget
 */
function widget()
{
    $widget = func_get_arg(0);
    $widgetRows = explode('.', $widget);
    $className = array_pop($widgetRows);

    require_once(__TYPECHO_WIDGET_DIR__ . '/' . str_replace('.', '/', $widget) . '.php');
    $object = new $className();
    
    $args = func_get_args();
    array_shift($args);

    call_user_func_array(array(&$object, 'render'), $args);
        
    return $object;
}

/**
 * Typecho组件基类
 * 
 * @package Widget
 */
abstract class TypechoWidget
{
    private static $_registry = array();
    private $_stack = array();
    protected $rows = array();
    
    public function __construct()
    {
        self::$_registry[get_class($this)] = $this;
    }

    /**
     * 将类本身赋值
     *
     * @param string $variable 变量名
     * @return void
     */
    public function to(&$variable)
    {
        $variable = $this;
    }
    
    /**
     * 格式化解析堆栈内的所有数据
     *
     * @param string $format 数据格式
     * @return void
     */
    public function parse($format)
    {
        $rowsKey = array();
        
        //将数据格式化
        foreach($this->rows as $key => $val)
        {
            $rowsKey[] = '{' . $key . '}';
        }
        
        foreach($this->_stack as $val)
        {
            echo str_replace($rowsKey, $val, $format);
        }
    }
    
    /**
     * 将每一行的值压入堆栈
     *
     * @param array $value 每一行的值
     * @return array
     */
    public function push(array $value)
    {
        //将行数据按顺序置位
        if(empty($this->rows))
        {
            $this->rows = $value;
        }
    
        $this->_stack[] = $value;
        return $value;
    }
    
    public function have()
    {
        return !empty($this->_stack);
    }
    
    public function get()
    {
        $this->rows = current($this->_stack);
        next($this->_stack);
        return $this->rows;
    }
    
    public function __call($name, $args)
    {
        if(function_exists($name))
        {
            array_unshift($args, $this->rows);
            call_user_func_array($name, $args);
        }
        else
        {
            echo isset($this->rows[$name]) ? $this->rows[$name] : NULL;
        }
    }
    
    public function __get($name)
    {
        return $this->rows[$name];
    }
    
    abstract public function render();
}
