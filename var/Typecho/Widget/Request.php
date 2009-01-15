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
class Typecho_Widget_Request extends Typecho_Request
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
     * 当前过滤器
     * 
     * @access private
     * @var array
     */
    private $_filter = array();
    
    /**
     * 默认数据
     * 
     * @access private
     * @var mixed
     */
    private $_default = NULL;
    
    /**
     * 支持的过滤器列表
     * 
     * @access private
     * @var string
     */
    private static $_supportFilters = array(
        'int'       =>  'intval',
        'integer'   =>  'intval',
        'search'    =>  array('Typecho_Common', 'filterSearchQuery'),
        'xss'       =>  array('Typecho_Common', 'removeXSS'),
        'url'       =>  array('Typecho_Common', 'safeUrl')
    );
    
    /**
     * 应用过滤器
     * 
     * @access private
     * @param mixed $value
     * @return void
     */
    private function _applyFilter($value)
    {
        if ($this->_filter) {
            foreach ($this->_filter as $filter) {
                $value = is_array($value) ? array_map($filter, $value) :
                call_user_func($filter, $value);
            }
        }
        
        $this->_filter = array();
        return $value;
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
     * 设置默认值
     * 
     * @access public
     * @param mixed $value 默认值
     * @return Typecho_Widget_Request
     */
    public function nil($value)
    {
        $this->_default = $value;
        return $this;
    }
    
    /**
     * 设置过滤器
     * 
     * @access public
     * @param mixed $filter 过滤器名称
     * @return Typecho_Widget_Request
     */
    public function filter()
    {
        $filters = func_get_args();
        
        foreach ($filters as $filter) {
            $this->_filter[] = is_string($filter) && isset(self::$_supportFilters[$filter]) 
            ? self::$_supportFilters[$filter] : $filter;
        }
        return $this;
    }
    
    /**
     * 获取参数列表
     * 
     * @access public
     * @return array
     */
    public function from($parameter)
    {
        if (is_array($parameter)) {
            $args = $parameter;
        } else {
            $args = func_get_args();
            $parameters = array();
        }

        foreach ($args as $name) {
            $parameters[$name] = $this->{$name};
        }

        return $parameters;
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
        if ($this->_flushed) {
            $value = isset($this->_params[$name]) ? $this->_params[$name] : $this->_default;
        } else {
            $value = parent::getParameter($name, $this->_default);
        }

        $this->_default = NULL;
        return $this->_filter ? $this->_applyFilter($value) : $value;
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
        if ($this->_flushed) {
            $this->_params[$name] = $value;
        } else {
            parent::setParameter($name, $value);
        }
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
        if ($this->_flushed) {
            return isset($this->_params[$name]);
        } else {
            return parent::isSetParameter($name);
        }
    }
}
