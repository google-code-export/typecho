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
 * 全局变量获取
 *
 * @package Component
 */
class OptionsComponent extends TypechoComponent
{
    /**
     * 全局变量存储
     * @var array
     */
    private $_options = array();
    
    /**
     * 构造函数,查询所有变量
     * 
     */
    public function __construct()
    {
        $db = TypechoDb::get();
        
        $db->fetchRows($db->sql()->select('table.statics', 'static_name, static_value'),
        array($this, 'push'));
    }
    
    /**
     * 将每一行的值存入私有变量中
     * 
     * @param array $rows 每一行数据
     * @return void
     */
    public function push(array $rows)
    {
        $this->_options[$rows['static_name']] = $rows['static_value'];
    }
    
    /**
     * 魔术函数获取变量值
     * 
     * @param string $name 变量名
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_options[$name];
    }
    
    /**
     * 魔术函数打印变量值
     * 
     * @param string $name 变量名
     * @param array $args 参数
     * @return void
     */
    public function __call($name, array $args)
    {
        echo $this->_options[$name];
    }
}
