<?php
/**
 * 配置管理 *  * @author qining * @category typecho * @package config * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org) * @license GNU General Public License 2.0 * @version $Id$ */
/**
 * 配置管理类 *  * @author qining * @category typecho * @package Config * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org) * @license GNU General Public License 2.0 */class TypechoConfig implements Iterator
{
    /**     * 配置列表     *      * @access private     * @var array     */    private static $_config = array();    
    /**     * 当前配置     *      * @access private     * @var array     */    private $_currentConfig = array();    
    /**     * 实例化一个当前配置     *      * @access public     * @param array $config 配置列表     * @return void     */    public function __construct(array $config)    {        foreach($config as $name => $value)        {            $this->_currentConfig[$name] = $value;        }    }
    /**     * 获取一个配置     *      * @access public     * @param string $name 配置名称     * @return mixed     */    public static function get($name)    {        return self::$_config[$name];    }    
    /**     * 设置一个配置     *      * @access public     * @param string $name 配置名称     * @param mixed $value 配置值     * @return void     */    public static function set($name, $value)    {        self::$_config[$name] = is_array($value) ? new TypechoConfig($value) : $value;    }    
    /**     * 重设指针     *      * @access public     * @return void     */    public function rewind()    {        reset($this->_currentConfig);    }    
    /**     * 返回当前值     *      * @access public     * @return mixed     */    public function current()    {        return current($this->_currentConfig);    }    
    /**     * 指针后移一位     *      * @access public     * @return void     */    public function next()    {        next($this->_currentConfig);    }    
    /**     * 验证当前值是否到达最后     *      * @access public     * @return boolean     */    public function valid()    {        return false === current($this->_currentConfig) ? false : true;    }    
    /**     * 魔术函数获取一个配置值     *      * @access public     * @param string $name 配置名称     * @return mixed     */    public function __get($name)    {        return $this->__currentConfig[$name];    }    
    /**     * 魔术函数设置一个配置值     *      * @access public     * @param string $name 配置名称     * @param mixed $value 配置值     * @return void     */    public function __set($name, $value)    {        $this->__currentConfig[$name] = $value;    }    
    /**     * 判断当前配置值是否存在     *      * @access public     * @param string $name 配置名称     * @return boolean     */    public function __isset($name)    {        return isset($this->__currentConfig[$name]);    }}
