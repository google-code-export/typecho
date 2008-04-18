<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Widget.php 107 2008-04-11 07:14:43Z magike.net $
 */

/** 异常基类 */
require_once 'Exception.php';

/** 载入异常支持 */
require_once 'Widget/WidgetException.php';

/** 载入导航页支持 */
require_once 'Widget/WidgetNavigator.php';

/**
 * Typecho组件调用
 *
 * @param string $widget 组件名称
 * @param mixed $param 参数
 * @return TypechoWidget
 * @throws TypechoWidgetException
 */
function widget($widget)
{
    /** 已经载入的widget */
    static $_widgets;

    /** 判断是否为plugin */
    $widgetRoot = __TYPECHO_WIDGET_DIR__;
    if(0 === strpos($widget, 'plugin:'))
    {
        $widgetRoot = __TYPECHO_PLUGIN_DIR__;
        $widget = substr($widget, 7);
    }

    if(empty($_widgets[$widget]))
    {
        $widget_rows = explode('.', $widget);
        $className = array_pop($widget_rows);

        if(!class_exists($className))
        {
            $fileName = __TYPECHO_WIDGET_DIR__ . '/' . str_replace('.', '/', $widget) . '.php';
            if(file_exists($fileName))
            {
                require_once $fileName;
            }
            else
            {
                throw new TypechoWidgetException(_t('组件%s不存在', $widget), 404);
            }
        }

        $object = new $className();
        $_widgets[$widget] = &$object;

        $args = func_get_args();
        array_shift($args);
        call_user_func_array(array($_widgets[$widget], 'render'), $args);
    }

    return $_widgets[$widget];
}

/**
 * Typecho组件基类
 *
 * @package Widget
 */
abstract class TypechoWidget
{
    /**
     * 内部数据堆栈
     *
     * @access protected
     * @var array
     */
    protected $_stack = array();

    /**
     * 数据堆栈每一行
     *
     * @access protected
     * @var array
     */
    protected $_row = array();

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
     * 格式化解析堆栈内的所有数据
     *
     * @param string $format 数据格式
     * @return void
     */
    public function parse($format)
    {
        $_rowsKey = array();

        //将数据格式化
        foreach($this->_row as $key => $val)
        {
            $_rowsKey[] = '{' . $key . '}';
        }

        foreach($this->_stack as $val)
        {
            echo str_replace($_rowsKey, $val, $format);
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
        if(empty($this->_row))
        {
            $this->_row = $value;
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
        $this->_row = current($this->_stack);
        next($this->_stack);
        return $this->_row;
    }
    
    /**
     * 重置堆栈
     *
     * @return void
     */
    public function reset()
    {
        reset($this->_stack);
    }

    /**
     * 魔术函数,用于挂接其它函数
     *
     * @access public
     * @param string $name 函数名
     * @param array $args 函数参数
     * @return void
     */
    public function __call($name, $args)
    {
        if(function_exists($name))
        {
            array_unshift($args, $this, $this->_row);
            call_user_func_array($name, $args);
        }
        else
        {
            echo isset($this->_row[$name]) ? $this->_row[$name] : NULL;
        }
    }

    /**
     * 魔术函数,用于获取内部变量
     *
     * @access public
     * @param string $name 变量名
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->_row[$name]) ? $this->_row[$name] : NULL;
    }
    
    /**
     * 设定堆栈每一行的值
     *
     * @param string $name 值对应的键值
     * @param mixed $value 相应的值
     * @return array
     */
    public function __set($name, $value)
    {
        $this->_row[$name] = $value;
    }

    /**
     * 必须实现的执行函数
     *
     * @return void
     */
    public function render()
    {
        trigger_error('Method render must be implement.', E_WARNING);
    }
}
