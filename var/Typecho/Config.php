<?php
/**
 * 配置管理
 *
 * @category typecho
 * @package config
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 配置管理类
 *
 * @category typecho
 * @package Config
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Config implements Iterator
{
    /**
     * 当前配置
     *
     * @access private
     * @var array
     */
    private $_currentConfig = array();

    /**
     * 实例化一个当前配置
     *
     * @access public
     * @param array $config 配置列表
     * @return void
     */
    public function __construct(array $config)
    {
        foreach($config as $name => $value)
        {
            $this->_currentConfig[$name] = $value;
        }
    }
    
    /**
     * 工厂模式实例化一个当前配置
     *
     * @access public
     * @param array $config 配置列表
     * @return void
     */
    public static function factory(array $config)
    {
        return new Typecho_Config($config);
    }

    /**
     * 重设指针
     *
     * @access public
     * @return void
     */
    public function rewind()
    {
        reset($this->_currentConfig);
    }

    /**
     * 返回当前值
     *
     * @access public
     * @return mixed
     */
    public function current()
    {
        return current($this->_currentConfig);
    }

    /**
     * 指针后移一位
     *
     * @access public
     * @return void
     */
    public function next()
    {
        next($this->_currentConfig);
    }
    
    /**
     * 获取当前指针
     * 
     * @access public
     * @return void
     */
    public function key()
    {
        return key($this->_currentConfig);
    }

    /**
     * 验证当前值是否到达最后
     *
     * @access public
     * @return boolean
     */
    public function valid()
    {
        return false === current($this->_currentConfig) ? false : true;
    }

    /**
     * 魔术函数获取一个配置值
     *
     * @access public
     * @param string $name 配置名称
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->_currentConfig[$name]) ? $this->_currentConfig[$name] : NULL;
    }

    /**
     * 魔术函数设置一个配置值
     *
     * @access public
     * @param string $name 配置名称
     * @param mixed $value 配置值
     * @return void
     */
    public function __set($name, $value)
    {
        $this->_currentConfig[$name] = $value;
    }
    
    /**
     * 直接输出默认配置值
     * 
     * @access public
     * @param string $name 配置名称
     * @param array $args 参数
     * @return void
     */
    public function __call($name, $args)
    {
        echo $this->_currentConfig[$name];
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
        return isset($this->_currentConfig[$name]);
    }
    
    /**
     * 魔术方法,打印当前配置数组
     * 
     * @access public
     * @return string
     */
    public function __toString()
    {
        return serialize($this->_currentConfig);
    }
}
