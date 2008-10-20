<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Widget.php 107 2008-04-11 07:14:43Z magike.net $
 */

/** Typecho_Widget_Request */
require_once 'Typecho/Widget/Request.php';

/** Typecho_Widget_Response */
require_once 'Typecho/Widget/Response.php';

/** Typecho_Config */
require_once 'Typecho/Config.php';

/**
 * Typecho组件基类
 *
 * @package Widget
 */
abstract class Typecho_Widget
{
    /**
     * widget对象池
     * 
     * @access private
     * @var array
     */
    private static $_widgetPool = array();

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
     * 配置信息
     * 
     * @access protected
     * @var Typecho_Config
     */
    protected $parameter;
    
    /**
     * 请求信息
     * 
     * @access protected
     * @var Typecho_Widget_Request
     */
    protected $request;
    
    /**
     * 回执信息
     * 
     * @access protected
     * @var Typecho_Widget_Response
     */
    protected $response;
    
    /**
     * 当前堆栈指针顺序值,从1开始
     * 
     * @access public
     * @var integer
     */
    public $sequence = 0;
    
    /**
     * 构造函数
     * 
     * @access public
     * @param mixed $params 传递的参数
     * @return void
     */
    public function __construct($params = array())
    {
        /** 初始化参数 */
        $this->parameter = Typecho_Config::factory($params);
        $this->request = Typecho_Widget_Request::getInstance();
        $this->response = Typecho_Widget_Response::getInstance();
        
        /** 准备 */
        $this->prepare();
        
        /** 初始化 */
        $this->init();
    }
    
    /**
     * 准备函数
     * 
     * @access public
     * @return void
     */
    public function prepare()
    {}
    
    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function init()
    {}
    
    /**
     * request事件触发
     * 
     * @access public
     * @param string $name 触发条件名
     * @param string $value 触发条件值
     * @return mixed
     */
    public function onRequest($name, $value = NULL)
    {
        $request = $this->request->{$name};
        
        if((!empty($value) && $request == $value) || 
        (empty($value) && !empty($name)))
        {
            return $this;
        }
        else
        {
            /** Typecho_Widget_Helper_Null */
            require_once 'Typecho/Widget/Helper/Empty.php';
            return new Typecho_Widget_Helper_Empty();
        }
    }
    
    /**
     * 获取对象插件句柄
     * 
     * @access public
     * @param string $adapter 适配器类型
     * @return Typecho_Plugin
     */
    public function getPlugin($adapter)
    {
        /** Typecho_Plugin */
        require_once 'Typecho/Plugin.php';
        return Typecho_Plugin::factory(get_class($this), $adapter);
    }

    /**
     * 工厂方法,将类静态化放置到列表中
     * 
     * @access public
     * @param string $className
     * @return object
     * @throws Typecho_Exception
     */
    public static function widget($className)
    {
        if(!isset(self::$_widgetPool[$className]))
        {
            $fileName = str_replace('_', '/', $className) . '.php';            
            require_once $fileName;
            
            /** 如果类不存在 */
            if(!class_exists($className))
            {
                /** Typecho_Exception */
                require_once 'Typecho/Widget/Exception.php';
                throw new Typecho_Widget_Exception($className);
            }
            
            $params = array_slice(func_get_args(), 1);
            self::$_widgetPool[$className] = call_user_func_array(array(new ReflectionClass($className), 'newInstance'), $params);
        }
        
        return self::$_widgetPool[$className];
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
        $_rowsKey = array();

        /** 过滤数据行 */
        foreach($this->_row as $key => $val)
        {
            if(is_array($val) || is_object($val))
            {
                unset($this->_row[$key]);
            }
        }

        //将数据格式化
        foreach($this->_row as $key => $val)
        {
            $_rowsKey[] = '{' . $key . '}';
        }

        foreach($this->_stack as $val)
        {
            /** 过滤数据行 */
            foreach($val as $inkey => $inval)
            {
                if(is_array($inval) || is_object($inval))
                {
                    unset($val[$inkey]);
                }
            }
            echo str_replace($_rowsKey, $val, $format) . "\n";
        }
        
        /** 重置指针 */
        reset($this->_row);
        reset($this->_stack);
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
        $this->_row = $value;

        $this->_stack[] = $value;
        return $value;
    }
    
    /**
     * 根据余数输出
     * 
     * @access public
     * @param string $param 需要输出的值
     * @return void
     */
    public function alt()
    {
        $args = func_get_args();
        $num = func_num_args();
        $split = $this->sequence % $num;
        echo $args[(0 == $split ? $num : $split) -1];
    }
    
    /**
     * 输出顺序值
     * 
     * @access public
     * @return void
     */
    public function sequence()
    {
        echo $this->sequence;
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
    public function next()
    {
        $this->_row = &$this->_stack[key($this->_stack)];
        next($this->_stack);
        $this->sequence ++;
        
        if(!$this->_row)
        {
            $this->_row = reset($this->_stack);
            $this->sequence = 0;
            return false;
        }
        
        return $this->_row;
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
        echo isset($this->_row[$name]) ? $this->_row[$name] : NULL;
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
     * @return void
     */
    public function __set($name, $value)
    {
        $this->_row[$name] = $value;
    }
    
    /**
     * 验证堆栈值是否存在
     * 
     * @access public
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_row[$name]);
    }
}
