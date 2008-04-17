<?php
/**
 * Typecho Blog Platform * * @author     qining * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org) * @license    GNU General Public License 2.0 * @version    $Id: Mysql.php 103 2008-04-09 16:22:43Z magike.net $ */
/**
 * 数据库Mysql适配器 * * @package Db */class TypechoMysql implements TypechoDbAdapter
{
    /**     * 数据库连接函数     *     * @param TypechoConfig $config 数据库配置     * @throws TypechoDbException     * @return resource     */    public function connect(TypechoConfig $config)    {        if($dbLink = @mysql_connect($config->host . ':' . $config->port, $config->user, $config->password))        {            if(@mysql_select_db($config->database, $dbLink))            {                if($config->charset)                {                    $this->query("SET NAMES '{$charset}'");                }                return $dbLink;            }        }
        throw new TypechoDbException(__TYPECHO_DEBUG__ ?         mysql_error() : _t('数据库连接错误'), 503);    }    
    /**     * 执行数据库查询     *     * @param string $sql 查询字符串     * @param boolean $op 查询读写开关     * @throws TypechoDbException     * @return resource     */    public function query($query, $op = TypechoDb::READ, $action = NULL)    {        if($resource = @mysql_query((string) $query))        {            return $resource;        }        
        throw new TypechoDbException(__TYPECHO_DEBUG__ ?         mysql_error() : _t('数据库查询错误'), 500);    }    
    /**     * 将数据查询的其中一行作为数组取出,其中字段名对应数组键值     *     * @param resource $resource 查询返回资源标识     * @return array     */    public function fetch($resource)    {        return mysql_fetch_assoc($resource);    }    
    /**     * 引号转义函数     *     * @param string $string 需要转义的字符串     * @return string     */    public function quotes($string)    {        return '\'' . str_replace(array('\'', '\\'), array('\'\'', '\\\\'), $string) . '\'';    }    
    /**     * 对象引号过滤     *      * @access public     * @param string $string     * @return string     */    public function quoteColumn($string)    {        return '`' . $string . '`';    }    
    /**     * 合成查询语句     *      * @access public     * @param array $sql 查询对象词法数组     * @return string     */    public function parseSelect(array $sql)    {        $sql['limit'] = empty($sql['limit']) ? NULL : ' LIMIT ' . $sql['limit'];        $sql['offset'] = empty($sql['offset']) ? NULL : ' OFFSET ' . $sql['offset'];        
        return 'SELECT ' . $sql['fields'] . ' FROM ' . $sql['table'] .         $sql['where'] . $sql['group'] . $sql['order'] . $sql['limit'] . $sql['offset'];    }
    /**     * 取出最后一次查询影响的行数     *     * @param resource $resource 查询返回资源标识     * @return integer     */    public function affectedRows($resource)    {        return mysql_affected_rows($resource);    }    
    /**     * 取出最后一次插入返回的主键值     *     * @param resource $resource 查询返回资源标识     * @return integer     */    public function lastInsertId($resource)    {        return mysql_insert_id($resource);    }}
