<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Db.php 107 2008-04-11 07:14:43Z magike.net $
 */

/** 配置管理 */
require_once 'Typecho/Config.php';

/** sql构建器 */
require_once 'Typecho/Db/Query.php';

/**
 * 包含获取数据支持方法的类.
 * 必须定义__TYPECHO_DB_HOST__, __TYPECHO_DB_PORT__, __TYPECHO_DB_NAME__,
 * __TYPECHO_DB_USER__, __TYPECHO_DB_PASS__, __TYPECHO_DB_CHAR__
 *
 * @package Db
 */
class Typecho_Db
{
    /** 读取数据库 */
    const READ = true;
    
    /** 写入数据库 */
    const WRITE = false;
    
    /** 升序方式 */
    const SORT_ASC = 'ASC';
    
    /** 降序方式 */
    const SORT_DESC = 'DESC';
    
    /** 表内连接方式 */
    const INNER_JOIN = 'INNER';
    
    /** 表外连接方式 */
    const OUTER_JOIN = 'OUTER';
    
    /** 表左连接方式 */
    const LEFT_JOIN = 'LEFT';
    
    /** 表外连接方式 */
    const RIGHT_JOIN = 'RIGHT';
    
    /** 数据库查询操作 */
    const SELECT = 'SELECT';
    
    /** 数据库更新操作 */
    const UPDATE = 'UPDATE';
    
    /** 数据库插入操作 */
    const INSERT = 'INSERT';
    
    /** 数据库删除操作 */
    const DELETE = 'DELETE';

    /**
     * 数据库适配器
     * @var Typecho_Db_Adapter
     */
    private $_adapter;

    /**
     * sql词法构建器
     * @var Typecho_Db_Query
     */
    private $_query;

    /**
     * 实例化的数据库对象
     * @var Typecho_Db
     */
    private static $_instance;

    /**
     * 数据库类构造函数
     *
     * @return void
     * @throws Typecho_Db_Exception
     */
    public function __construct()
    {
        /** 判断是否定义配置 */
        Typecho_Config::need('Db');
    
        /** 数据库适配器 */
        require_once 'Typecho/Db/Adapter/' . Typecho_Config::get('Db')->adapter . '.php';
        $adapter = 'Typecho_Db_Adapter_' . Typecho_Config::get('Db')->adapter;

        //实例化适配器对象
        $this->_adapter = new $adapter();

        //连接数据库
        $this->_adapter->connect(Typecho_Config::get('Db'));
    }

    /**
     * 获取SQL词法构建器实例化对象
     *
     * @return Typecho_Db_Query
     */
    public function sql()
    {
        return new Typecho_Db_Query($this->_adapter);
    }

    /**
     * 获取数据库实例化对象
     * 用静态变量存储实例化的数据库对象,可以保证数据连接仅进行一次
     *
     * @return Typecho_Db
     */
    public static function get()
    {
        if(empty(self::$_instance))
        {
            //实例化数据库对象
            self::$_instance = new Typecho_Db();
        }

        return self::$_instance;
    }

    /**
     * 执行查询语句
     *
     * @param mixed $query 查询语句或者查询对象
     * @param boolean $op 数据库读写状态
     * @param string $action 操作动作
     * @return mixed
     */
    public function query($query, $op = self::READ, $action = self::SELECT)
    {
        //在适配器中执行查询
        if($query instanceof Typecho_Db_Query)
        {
            $action = $query->getAttribute('action');
            $op = (self::UPDATE == $action || self::DELETE == $action 
            || self::INSERT == $action) ? self::WRITE : self::READ;
        }

        $resource = $this->_adapter->query($query, $op, $action);

        if($action)
        {
            //根据查询动作返回相应资源
            switch($action)
            {
                case self::UPDATE:
                case self::DELETE:
                    return $this->_adapter->affectedRows($resource);
                case self::INSERT:
                    return $this->_adapter->lastInsertId($resource);
                case self::SELECT:
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
        $resource = $this->query($query, self::READ);
        $result = array();
        
        /** 取出过滤器 */
        if(!empty($filter))
        {
            list($object, $method) = $filter;
        }

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
        $resource = $this->query($query, self::READ);
        
        /** 取出过滤器 */
        if(!empty($filter))
        {
            list($object, $method) = $filter;
        }

        return ($rows = $this->_adapter->fetch($resource)) ?
        ($filter ? call_user_func(array(&$object, $method), $rows) : $rows) :
        array();
    }
    
    /**
     * 一次取出一个对象
     *
     * @param mixed $query 查询对象
     * @return array
     */
    public function fetchObject($query)
    {
        $resource = $this->query($query, self::READ);
        return $this->_adapter->fetchObject($resource);
    }
    
    /**
     * 获取数据库版本
     * 
     * @access public
     * @return string
     */
    public function version()
    {
        return $this->_adapter->version();
    }
}
