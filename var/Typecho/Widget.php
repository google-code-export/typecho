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

/** Typecho_Plugin */
require_once 'Typecho/Plugin.php';

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
     * widget的唯一序列号
     * 
     * @access private
     * @var integer
     */
    private static $_widgetResouceId = 0;
    
    /**
     * 当前组件的序列号
     * 
     * @access private
     * @var string
     */
    private $_resourceId = NULL;
    
    /**
     * 帮手列表
     * 
     * @access private
     * @var array
     */
    private $_helpers = array();

    /**
     * 内部数据堆栈
     *
     * @access protected
     * @var array
     */
    protected $stack = array();

    /**
     * 数据堆栈每一行
     *
     * @access protected
     * @var array
     */
    protected $row = array();
    
    /**
     * 当前堆栈指针顺序值,从1开始
     * 
     * @access public
     * @var integer
     */
    public $sequence = 0;
    
    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
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
        
        if ((!empty($value) && $request == $value) || 
        (empty($value) && !empty($request))) {
            return $this;
        } else {
            /** Typecho_Widget_Helper_Empty */
            require_once 'Typecho/Widget/Helper/Empty.php';
            return new Typecho_Widget_Helper_Empty();
        }
    }
    
    /**
     * post事件触发
     * 
     * @return mixed
     */
    public function onPost()
    {
        if ($this->request->isPost()) {
            return $this;
        } else {
            /** Typecho_Widget_Helper_Null */
            require_once 'Typecho/Widget/Helper/Empty.php';
            return new Typecho_Widget_Helper_Empty();
        }
    }
    
    /**
     * 获取对象插件句柄
     * 
     * @access public
     * @param string $handle 句柄
     * @return Typecho_Plugin
     */
    public function plugin($handle = NULL)
    {
        return Typecho_Plugin::factory(empty($handle) ? get_class($this) : $handle);
    }

    /**
     * 工厂方法,将类静态化放置到列表中
     * 
     * @access public
     * @param string $alias 组件别名
     * @param mixed $params 传递的参数
     * @param mixed $request 前端参数
     * @param boolean $enableResponse 是否允许http回执
     * @return object
     * @throws Typecho_Exception
     */
    public static function widget($alias, $params = NULL, $request = NULL, $enableResponse = true)
    {
        list($className) = explode('@', $alias);
        if (!isset(self::$_widgetPool[$alias])) {
            $fileName = str_replace('_', '/', $className) . '.php';            
            require_once $fileName;
            
            /** 如果类不存在 */
            if (!class_exists($className)) {
                /** Typecho_Exception */
                require_once 'Typecho/Widget/Exception.php';
                throw new Typecho_Widget_Exception($className);
            }
            
            self::$_widgetPool[$alias] = new $className();
            
            if (!empty($params)) {
                self::$_widgetPool[$alias]->parameter->setDefault($params, true);
            }
            
            if (!empty($request)) {
                self::$_widgetPool[$alias]->request->flush($request);
            }
            
            if (!$enableResponse) {
                self::$_widgetPool[$alias]->response->disable();
            }
            
            self::$_widgetPool[$alias]->execute();
        }
        
        return self::$_widgetPool[$alias];
    }

    /**
     * 将类本身赋值
     *
     * @param string $variable 变量名
     * @return void
     */
    public function to(&$variable)
    {
        return $variable = $this;
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

        /** 过滤数据行 */
        foreach ($this->row as $key => $val) {
            if (is_array($val) || is_object($val)) {
                unset($this->row[$key]);
            }
        }

        //将数据格式化
        foreach ($this->row as $key => $val) {
            $rowsKey[] = '{' . $key . '}';
        }

        foreach ($this->stack as $val) {
            /** 过滤数据行 */
            foreach ($val as $inkey => $inval) {
                if (is_array($inval) || is_object($inval)) {
                    unset($val[$inkey]);
                }
            }
            echo str_replace($rowsKey, $val, $format) . "\n";
        }
        
        /** 重置指针 */
        reset($this->row);
        reset($this->stack);
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
        $this->row = $value;

        $this->stack[] = $value;
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
        return !empty($this->stack);
    }

    /**
     * 返回堆栈每一行的值
     *
     * @return array
     */
    public function next()
    {
        if ($this->stack) {
            $this->row = &$this->stack[key($this->stack)];
            next($this->stack);
            $this->sequence ++;
        }
        
        if (!$this->row) {
            reset($this->stack);
            $this->sequence = 0;
            return false;
        }
        
        return $this->row;
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
        echo $this->{$name};
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
        return isset($this->row[$name]) ? $this->row[$name] : (method_exists($this, $method = '___' . $name)
        ? $this->row[$name] = $this->$method() : 
        (isset($this->_helpers[$name]) ? $this->_helpers[$name] : $this->__helper($name)));
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
        $this->row[$name] = $value;
    }
    
    /**
     * 验证堆栈值是否存在
     * 
     * @access public
     * @param string $name
     * @return boolean
     */
    public function __isSet($name)
    {
        return isset($this->row[$name]);
    }
    
    /**
     * 载入组件帮手
     * 
     * @access public
     * @param string $name 帮手名称
     * @return string
     */
    public final function __helper($name)
    {
        switch ($name) {
            case 'request':
                $this->_helpers[$name] = new Typecho_Widget_Request();
                break;
            case 'response':
                $this->_helpers[$name] = new Typecho_Widget_Response();
                break;
            case 'parameter':
                $this->_helpers[$name] = Typecho_Config::factory();
                break;
            default:
                $method = '___' . $name;
                $result = $this->plugin()->trigger($plugged)->{$method}($name, $this);
                return $plugged ? $result : NULL;
        }
        
        return $this->_helpers[$name];
    }
    
    /**
     * 获取当前唯一序列化值
     * 
     * @access public
     * @return unknown
     */
    public final function __toString()
    {
        if (empty($this->_resourceId)) {
            self::$_widgetResouceId ++;
            $this->_resourceId = get_class($this) . '-' . self::$_widgetResouceId;
        }
        
        return $this->_resourceId;
    }
}
