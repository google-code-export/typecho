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
 * Typecho组件基类
 * 
 * @package TypechoCore
 */
abstract class TypechoComponent
{
    /**
     * 代理实例
     * @var TypechoComponent
     */
    private $_instance;
    
    /**
     * 构造函数,必须实现
     * 
     */
    abstract function __construct();
    
    /**
     * 代理方法,使用此方法可以使被代理实例具有代理实例的全部方法
     * 
     * @param TypechoComponent $component 代理实例
     * @return void
     */
    protected function proxy(TypechoComponent $component)
    {
        $this->_instance = $component;
    }
    
    /**
     * 魔术函数,用于实现所有代理实例的方法
     * 
     * @param string $method 方法名
     * @param array $args 参数列表
     * @return mixed
     */
    public function __call($method, array $args)
    {
        return call_user_func_array(array($this->_instance, $method), $args);
    }
}
