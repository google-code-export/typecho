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

/** 载入异常支持 **/
require_once 'Widget/WidgetException.php';

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
    $widget_rows = explode('.', $widget);
    $className = array_pop($widget_rows);

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
    /**
     * 保存所有实例化的widget对象
     * @var array
     */
    private static $_registry = array();
    
    /**
     * 内部数据堆栈
     * @var array
     */
    protected $_stack = array();
    
    /**
     * 数据堆栈每一行
     * @var array
     */
    protected $_rows = array();
    
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
        if(empty($variable) || 
        ($variable instanceof TypechoWidget && !$variable->have()))
        {
            $variable = $this;
        }
    }
    
    /**
     * 获取静态缓存中的实例化对象
     *
     * @param string $name 对象名
     * @return TypechoWidget
     */
    public function registry($name)
    {
        return self::$_registry[$name];
    }
    
    /**
     * 格式化解析堆栈内的所有数据
     *
     * @param string $format 数据格式
     * @return void
     */
    public function parse($format)
    {
        $_rowsKey = array();
        
        //将数据格式化
        foreach($this->_rows as $key => $val)
        {
            $_rowsKey[] = '{' . $key . '}';
        }
        
        foreach($this->_stack as $val)
        {
            echo str_replace($_rowsKey, $val, $format);
        }
    }
    
    /**
     * 格式化为序列化数据
     *
     * @return void
     */
    public function serialize()
    {        
        echo serialize($this->_stack);
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
        if(empty($this->_rows))
        {
            $this->_rows = $value;
        }
    
        $this->_stack[] = $value;
        return $value;
    }
    
    /**
     * 返回堆栈是否为空
     *
     * @return boolean
     */
    public function have()
    {
        return !empty($this->_stack);
    }
    
    /**
     * 返回堆栈每一行的值
     *
     * @return array
     */
    public function get()
    {
        $this->_rows = current($this->_stack);
        next($this->_stack);
        return $this->_rows;
    }
    
    /**
     * 设定堆栈每一行的值
     *
     * @param string $name 值对应的键值
     * @param mixed $name 相应的值
     * @return array
     */
    public function set($name, $value)
    {
        $this->_rows[$name] = $value;
    }
    
    public function __call($name, $args)
    {
        if(function_exists($name))
        {
            array_unshift($args, $this->_rows);
            call_user_func_array($name, $args);
        }
        else
        {
            echo isset($this->_rows[$name]) ? $this->_rows[$name] : NULL;
        }
    }
    
    public function __get($name)
    {
        return isset($this->_rows[$name]) ? $this->_rows[$name] : NULL;
    }
    
    /**
     * 必须实现的执行虚函数
     *
     * @return void
     */
    abstract public function render();
}
