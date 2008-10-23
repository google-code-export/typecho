<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Mysql.php 89 2008-03-31 00:10:57Z magike.net $
 */

/** 数据库适配器接口 */
require_once 'Typecho/Db/Adapter.php';

/**
 * 数据库PDOMysql适配器
 *
 * @package Db
 */
abstract class Typecho_Db_Adapter_Pdo implements Typecho_Db_Adapter
{
    /**
     * 数据库对象
     *
     * @access protected
     * @var PDO
     */
    protected $_object;

    /**
     * 数据库连接函数
     *
     * @param Typecho_Config $config 数据库配置
     * @throws Typecho_Db_Exception
     * @return resource
     */
    public function connect(Typecho_Config $config)
    {
        try
        {
            $this->_object = $this->init($config);
            $this->_object->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->_object;
        }
        catch(PDOException $e)
        {
            /** 数据库异常 */
            require_once 'Typecho/Db/Exception.php';
            throw new Typecho_Db_Exception($e->getMessage(), Typecho_Exception::UNVAILABLE);
        }
    }

    /**
     * 初始化数据库 
     * 
     * @param Typecho_Config $config 数据库配置
     * @abstract
     * @access public
     * @return PDO
     */
    abstract public function init(Typecho_Config $config);

    /**
     * 执行数据库查询
     *
     * @param string $query 数据库查询SQL字符串
     * @param mixed $handle 连接对象
     * @param integer $op 数据库读写状态
     * @param string $action 数据库动作
     * @throws Typecho_Db_Exception
     * @return resource
     */
    public function query($query, $handle, $op = Typecho_Db::READ, $action = NULL)
    {
        try
        {
            $this->_lastInsertTable = (!empty($action) && 'INSERT' == $action) ? $query->getAttribute('table') : NULL;
            $resource = $handle->prepare($query instanceof Typecho_Db_Query ? $query->__toString() : $query);
            $resource->execute();
        }
        catch(PDOException $e)
        {
            /** 数据库异常 */
            require_once 'Typecho/Db/Exception.php';
            throw new Typecho_Db_Exception($e->getMessage(), Typecho_Exception::RUNTIME);
        }

        return $resource;
    }

    /**
     * 将数据查询的其中一行作为数组取出,其中字段名对应数组键值
     *
     * @param resource $resource 查询返回资源标识
     * @return array
     */
    public function fetch($resource)
    {
        return $resource->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * 将数据查询的其中一行作为对象取出,其中字段名对应对象属性
     *
     * @param resource $resource 查询的资源数据
     * @return object
     */
    public function fetchObject($resource)
    {
        return $resource->fetchObject();
    }

    /**
     * 引号转义函数
     *
     * @param string $string 需要转义的字符串
     * @return string
     */
    public function quoteValue($string)
    {
        return $this->_object->quote($string);
    }

    /**
     * 对象引号过滤
     *
     * @access public
     * @param string $string
     * @return string
     */
    public function quoteColumn($string){}

    /**
     * 合成查询语句
     *
     * @access public
     * @param array $sql 查询对象词法数组
     * @return string
     */
    public function parseSelect(array $sql){}

    /**
     * 取出最后一次查询影响的行数
     *
     * @param resource $resource 查询的资源数据
     * @param mixed $handle 连接对象
     * @return integer
     */
    public function affectedRows($resource, $handle)
    {
        return $resource->rowCount();
    }

    /**
     * 取出最后一次插入返回的主键值
     *
     * @param resource $resource 查询的资源数据
     * @param mixed $handle 连接对象
     * @return integer
     */
    public function lastInsertId($resource, $handle)
    {
        return $handle->lastInsertId();
    }
}
