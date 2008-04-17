<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: DbAdapter.php 97 2008-04-04 04:39:54Z magike.net $
 */

/**
 * Typecho数据库适配器
 * 定义通用的数据库适配接口
 *
 * @package Db
 */
interface TypechoDbAdapter
{
    /**
     * 数据库连接函数
     *
     * @param TypechoConfig $config 数据库配置
     * @return resource
     */
    public function connect(TypechoConfig $config);

    /**
     * 执行数据库查询
     *
     * @param string $query 数据库查询SQL字符串
     * @param boolean $op 数据库读写状态
     * @return resource
     */
    public function query($query, $op = TypechoDb::READ, $action = NULL);

    /**
     * 将数据查询的其中一行作为数组取出,其中字段名对应数组键值
     *
     * @param resource $resource 查询的资源数据
     * @return array
     */
    public function fetch($resource);

    /**
     * 引号转义函数
     *
     * @param string $string 需要转义的字符串
     * @return string
     */
    public function quotes($string);

    /**
     * 对象引号过滤
     *
     * @access public
     * @param string $string
     * @return string
     */
    public function quoteColumn($string);

    /**
     * 合成查询语句
     *
     * @access public
     * @param array $sql 查询对象词法数组
     * @return string
     */
    public function parseSelect(array $sql);

    /**
     * 取出最后一次查询影响的行数
     *
     * @param resource $resource 查询的资源数据
     * @return integer
     */
    public function affectedRows($resource);

    /**
     * 取出最后一次插入返回的主键值
     *
     * @param resource $resource 查询的资源数据
     * @return integer
     */
    public function lastInsertId($resource);
}
