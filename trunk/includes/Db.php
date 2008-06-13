<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Db.php 107 2008-04-11 07:14:43Z magike.net $
 */

/** 异常基类 */
require_once 'Exception.php';

/** 配置管理 */
require_once 'Config.php';

/** 数据库异常 */
require_once 'Db/DbException.php';

/** 数据库适配器接口 */
require_once 'Db/DbAdapter.php';

/** sql构建器 */
require_once 'Db/DbQuery.php';

/**
 * 包含获取数据支持方法的类.
 * 必须定义__TYPECHO_DB_HOST__, __TYPECHO_DB_PORT__, __TYPECHO_DB_NAME__,
 * __TYPECHO_DB_USER__, __TYPECHO_DB_PASS__, __TYPECHO_DB_CHAR__
 *
 * @package Db
 */
class TypechoDb
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
    private static $_instance;

    /**
     * 数据库类构造函数
     *
     * @return void
     * @throws TypechoDbException
     */
    public function __construct()
    {
        /** 判断是否定义配置 */
        TypechoConfig::need('Db');
    
        /** 数据库适配器 */
        require_once 'Db/DbAdapter/' . TypechoConfig::get('Db')->adapter . '.php';
        $adapter = 'Typecho' . TypechoConfig::get('Db')->adapter . 'DbAdapter';

        //实例化适配器对象
        $this->_adapter = new $adapter();

        //连接数据库
        $this->_adapter->connect(TypechoConfig::get('Db'));
    }

    /**
     * 获取SQL词法构建器实例化对象
     *
     * @return TypechoDbQuery
     */
    public function sql()
    {
        return new TypechoDbQuery($this->_adapter);
    }

    /**
     * 获取数据库实例化对象
     * 用静态变量存储实例化的数据库对象,可以保证数据连接仅进行一次
     *
     * @return TypechoDb
     */
    public static function get()
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
     * @param string $action 操作动作
     * @return mixed
     */
    public function query($query, $op = TypechoDb::READ, $action = TypechoDb::SELECT)
    {
        //在适配器中执行查询
        if($query instanceof TypechoDbQuery)
        {
            $action = $query->getAttribute('action');
            $op = (TypechoDb::UPDATE == $action || TypechoDb::DELETE == $action 
            || TypechoDb::INSERT == $action) ? TypechoDb::WRITE : TypechoDb::READ;
        }

        $resource = $this->_adapter->query($query, $op, $action);

        if($action)
        {
            //根据查询动作返回相应资源
            switch($action)
            {
                case TypechoDb::UPDATE:
                case TypechoDb::DELETE:
                    return $this->_adapter->affectedRows($resource);
                case TypechoDb::INSERT:
                    return $this->_adapter->lastInsertId($resource);
                case TypechoDb::SELECT:
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
        $resource = $this->query($query, TypechoDb::READ);
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
        $resource = $this->query($query, TypechoDb::READ);
        
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
        $resource = $this->query($query, TypechoDb::READ);
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
