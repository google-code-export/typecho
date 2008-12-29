<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入api支持 */
require_once 'Typecho/Request.php';

/**
 * 从属于控制器的服务器请求处理类
 *
 * @package Widget
 */
class Typecho_Widget_Request
{
    /**
     * 刷新后的参数
     * 
     * @access private
     * @var array
     */
    private $_params = array();
    
    /**
     * 是否刷新
     * 
     * @access private
     * @var boolean
     */
    private $_flushed = false;
    
    /**
     * 设置过滤器
     * 
     * @access private
     * @var array
     */
    private $_filters = array();

    /**
     * 获取参数列表
     * 
     * @access public
     * @return array
     */
    public function from()
    {
        $args = func_get_args();
        return call_user_func_array(array($this, 'getParametersFrom'), $args);
    }
    
    /**
     * 设置过滤器
     * <code>
     * $this->setFilters(array(
     *     'cid'    => 'intval',
     *     'mid'    => 'intval',
     *     'slug'   => array($this, 'custom')
     * ));
     * </code>
     * 
     * @access public
     * @param array $filters 过滤项
     * @return void
     */
    public function setFilters(array $filters = array())
    {
        $this->_filters = $filters;
    }
    
    /**
     * 刷新所有request
     * 
     * @access public
     * @param array $parameters 参数
     * @return void
     */
    public function flush($parameters)
    {
        $this->_flushed = true;
        
        $args = $parameters;
        if (is_string($parameters)) {
            parse_str($parameters, $args);
        }
        
        $this->_params = $args;
    }
    
    /**
     * 获取指定的http传递参数
     *
     * @access public
     * @param string $key 指定的参数
     * @param mixed $default 默认的参数
     * @return mixed
     */
    public function getParameter($name, $default = NULL)
    {
        if ($this->_flushed) {
            $value = isset($this->_params[$name]) ? $this->_params[$name] : $default;
        } else {
            $value = Typecho_Request::getParameter($name, $default);
        }
        
        return isset($this->_filters[$name]) && $this->isSetParameter($name) ? 
        (is_array($value) ? array_map($this->_filters[$name], $value) : call_user_func($this->_filters[$name], $value))
        : $value;
    }
    
    /**
     * 从参数列表指定的值中获取http传递参数
     *
     * @access public
     * @param mixed $parameter 指定的参数
     * @return unknown
     */
    public function getParametersFrom($parameter)
    {
        if (is_array($parameter)) {
            $args = $parameter;
        } else {
            $args = func_get_args();
            $parameters = array();
        }

        foreach ($args as $arg) {
            $parameters[$arg] = $this->getParameter($arg);
        }

        return $parameters;
    }
    
    /**
     * 设置http传递参数
     * 
     * @access public
     * @param string $name 指定的参数
     * @param mixed $value 参数值
     * @return void
     */
    public function setParameter($name, $value)
    {
        if ($this->_flushed) {
            $this->_params[$name] = $value;
        } else {
            Typecho_Request::setParameter($name, $value);
        }
    }
    
    /**
     * 参数是否存在
     * 
     * @access public
     * @param string $key 指定的参数
     * @return boolean
     */
    public function isSetParameter($key)
    {
        if ($this->_flushed) {
            return isset($this->_params[$key]);
        } else {
            return Typecho_Request::isSetParameter($key);
        }
    }

    /**
     * Request包的直接代理
     * 
     * @access public
     * @param string $method 方法名
     * @param array $args 参数列表
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array('Typecho_Request', $method), $args);
    }
    
    /**
     * 获取指定参数值
     * 
     * @access public
     * @param string $name 参数名
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getParameter($name);
    }
    
    /**
     * 设定指定参数
     * 
     * @access public
     * @param string $name 参数名
     * @param mixed $value 参数值
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setParameter($name, $value);
    }
    
    /**
     * 判断当前配置值是否存在
     *
     * @access public
     * @param string $name 配置名称
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->isSetParameter($name);
    }
}
