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
 * 定义数据库适配器
 *
 */
define('__TYPECHO_DB_ADAPTER__', 'Mysql');

/**
 * 数据库异常
 */
require_once 'Db/DbException.php';

/**
 * 数据库适配器接口
 */
require_once 'Db/DbAdapter.php';

/**
 * 数据库适配器
 */
require_once 'Db/DbAdapter/' . __TYPECHO_DB_ADAPTER__;

/**
 * sql构建器
 */
require_once 'Db/DbQuery.php';

/**
 * 包含获取数据支持方法的类
 *
 * @package TypechoDb
 */
class TypechoDb
{
    /**
     * 数据库适配器
     * @var TypechoDbAdapter
     */
    private $_adapter;
    
    /**
     * sql词法构建器
     * @var TypechoDbQuery
     */
    private $_query;
    
    /**
     * 实例化的数据库对象
     * @var TypechoDb
     */
    static private $_instance;
    
    /**
     * 数据库类构造函数
     * 
     * @param string $adapter 数据库适配器名称
     * @return void
     */
    public function __construct($adapter = __TYPECHO_DB_ADAPTER__)
    {
        //实例化适配器对象
        $this->_adapter = new $adapter();
        
        //连接数据库
        $this->adapter->connect(__TYPECHO_DB_HOST__,
                                __TYPECHO_DB_PORT__,
                                __TYPECHO_DB_NAME__, 
                                __TYPECHO_DB_USER__, 
                                __TYPECHO_DB_PASS__, 
                                __TYPECHO_DB_CHAR__);
    }
    
    /**
     * 获取数据库实例化对象
     * 用静态变量存储实例化的数据库对象,可以保证数据连接仅进行一次
     * 
     * @return void
     */
    public function get()
    {
        if(empty(self::$_instance))
        {
            //实例化数据库对象
            self::$_instance = new TypechoDb();
        }
        
        return self::$_instance;
    }
}
