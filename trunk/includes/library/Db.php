<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 定义数据库适配器 **/
define('__TYPECHO_DB_ADAPTER__', 'Mysql');

/**
 * 定义数据库查询读写状态
 * true表示读状态
 * false表示写状态
 *
 */
define('__TYPECHO_DB_READ__', true);
define('__TYPECHO_DB_WRITE__', false);

/** 数据库异常 **/
require_once 'Db/DbException.php';

/** 数据库适配器接口 **/
require_once 'Db/DbAdapter.php';

/** 数据库适配器 **/
require_once 'Db/DbAdapter/' . __TYPECHO_DB_ADAPTER__ . '.php';

/** sql构建器 **/
require_once 'Db/DbQuery.php';

/**
 * 包含获取数据支持方法的类
 * 必须定义__TYPECHO_DB_HOST__, __TYPECHO_DB_PORT__, __TYPECHO_DB_NAME__,
 * __TYPECHO_DB_USER__, __TYPECHO_DB_PASS__, __TYPECHO_DB_CHAR__
 *
 * @package Db
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
     * @throws TypechoDbException
     */
    public function __construct($adapter = __TYPECHO_DB_ADAPTER__)
    {
        $adapter = 'Typecho' . __TYPECHO_DB_ADAPTER__;
        
        //检测常量是否已经被定义
        if(!defined($const = '__TYPECHO_DB_HOST__') || 
        !defined($const = '__TYPECHO_DB_PORT__') || 
        !defined($const = '__TYPECHO_DB_NAME__') || 
        !defined($const = '__TYPECHO_DB_USER__') || 
        !defined($const = '__TYPECHO_DB_PASS__') ||
        !defined($const = '__TYPECHO_DB_CHAR__'))
        {
            throw new TypechoDbException(_t('未定义的数据常量 %s', $const), __TYPECHO_EXCEPTION_500__);
        }
        
        //实例化适配器对象
        $this->_adapter = new $adapter();
        
        //连接数据库
        $this->_adapter->connect(__TYPECHO_DB_HOST__, 
                                 __TYPECHO_DB_PORT__, 
                                 __TYPECHO_DB_NAME__, 
                                 __TYPECHO_DB_USER__, 
                                 __TYPECHO_DB_PASS__, 
                                 __TYPECHO_DB_CHAR__);
    }
    
    /**
     * 获取SQL词法构建器实例化对象
     * 
     * @return TypechoDbQuery
     */
    public function sql()
    {
        if(empty($this->_query))
        {
            $this->_query = new TypechoDbQuery($this->_adapter);
        }
     
        $this->_query->init();
        return $this->_query;
    }
    
    /**
     * 获取数据库实例化对象
     * 用静态变量存储实例化的数据库对象,可以保证数据连接仅进行一次
     * 
     * @return TypechoDb
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
    
    /**
     * 执行查询语句
     * 
     * @param mixed $query 查询语句或者查询对象
     * @param boolean $op 数据库读写状态
     * @return mixied
     */
    public function query($query, $op = __TYPECHO_DB_READ__)
    {
        //在适配器中执行查询
        $resource = $this->_adapter->query((string) $query, $op);
        
        if($query instanceof TypechoDbQuery)
        {
            //根据查询动作返回相应资源
            switch($query->action())
            {
                case 'UPDATE':
                case 'DELETE':
                    return $this->_adapter->affectedRows($resource);
                case 'INSERT':
                    return $this->_adapter->affectedRows($resource);
                case 'SELECT':
                default:
                    return $resource;
            }
        }
        else
        {
            //如果直接执行查询语句则返回资源
            return $resource;
        }
    }
    
    /**
     * 一次取出所有行
     * 
     * @param mixed $query 查询对象
     * @param array $filter 行过滤器函数,将查询的每一行作为第一个参数传入指定的过滤器中
     * @return array
     */
    public function fetchAll($query, array $filter = NULL)
    {
        //执行查询
        $resource = $this->query($query, __TYPECHO_DB_READ__);
        $result = array();
        list($object, $method) = $filter;
        
        //取出每一行
        while($rows = $this->_adapter->fetch($resource))
        {
            //判断是否有过滤器
            $result[] = $filter ? call_user_func(array(&$object, $method), $rows) : $rows;
        }
        
        return $result;
    }
    
    /**
     * 一次取出一行
     * 
     * @param mixed $query 查询对象
     * @param array $filter 行过滤器函数,将查询的每一行作为第一个参数传入指定的过滤器中
     * @return array
     */
    public function fetchRow($query, array $filter = NULL)
    {
        $resource = $this->query($query, DB_READ);
        list($object, $method) = $filter;
        
        return ($rows = $this->_adapter->fetch($resource)) ?
        ($filter ? call_user_func(array(&$object, $method), $rows) : $rows) :
        array();
    }
}
