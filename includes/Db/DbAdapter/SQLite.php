<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Mysql.php 103 2008-04-09 16:22:43Z magike.net $
 */

/**
 * 数据库SQLite适配器
 *
 * @package Db
 */
class TypechoSQLite implements TypechoDbAdapter
{
    /**
     * 数据库标示
     * 
     * @access private
     * @var resource
     */
    private $_dbHandle;
    
    /**
     * 过滤字段名
     * 
     * @access private
     * @param mixed $result
     * @return array
     */
    private function filterColumnName($result)
    {
        if(!$result)
        {
            return $result;
        }
    
        $tResult = array();
        
        foreach($result as $key => $val)
        {
            if(false !== ($pos = strpos($key, '.')))
            {
                $key = substr($key, $pos + 1);
            }
        
            if(false === ($pos = strpos($key, '"')))
            {
                $tResult[$key] = $val;
            }
            else
            {
                $tResult[substr($key, $pos + 1, -1)] = $val;
            }
        }

        return $tResult;
    }
    
    /**
     * 数据库连接函数
     *
     * @param TypechoConfig $config 数据库配置
     * @throws TypechoDbException
     * @return resource
     */
    public function connect(TypechoConfig $config)
    {
        if($this->_dbHandle = sqlite_open($config->file, 0666, $error))
        {
            return $this->_dbHandle;
        }

        throw new TypechoDbException(__TYPECHO_DEBUG__ ?
        $error : _t('数据库连接错误'), TypechoException::UNVAILABLE);
    }

    /**
     * 执行数据库查询
     *
     * @param string $sql 查询字符串
     * @param boolean $op 查询读写开关
     * @throws TypechoDbException
     * @return resource
     */
    public function query($query, $op = TypechoDb::READ, $action = NULL)
    {
        if($resource = @sqlite_query($query instanceof TypechoDbQuery ? $query->__toString() : $query, $this->_dbHandle))
        {
            return $resource;
        }

        throw new TypechoDbException(__TYPECHO_DEBUG__ ?
        sqlite_error_string(sqlite_last_error($this->_dbHandle)) : _t('数据库查询错误'), TypechoException::RUNTIME);
    }

    /**
     * 将数据查询的其中一行作为数组取出,其中字段名对应数组键值
     *
     * @param resource $resource 查询返回资源标识
     * @return array
     */
    public function fetch($resource)
    {
        return $this->filterColumnName(sqlite_fetch_array($resource, SQLITE_ASSOC));
    }
    
    /**
     * 将数据查询的其中一行作为对象取出,其中字段名对应对象属性
     *
     * @param resource $resource 查询的资源数据
     * @return object
     */
    public function fetchObject($resource)
    {
        return sqlite_fetch_object($resource);
    }

    /**
     * 引号转义函数
     *
     * @param string $string 需要转义的字符串
     * @return string
     */
    public function quoteValue($string)
    {
        return '\'' . str_replace(array('\'', '\\'), array('\'\'', '\\\\'), $string) . '\'';
    }

    /**
     * 对象引号过滤
     *
     * @access public
     * @param string $string
     * @return string
     */
    public function quoteColumn($string)
    {
        return '"' . $string . '"';
    }

    /**
     * 合成查询语句
     *
     * @access public
     * @param array $sql 查询对象词法数组
     * @return string
     */
    public function parseSelect(array $sql)
    {
        if(!empty($sql['join']))
        {
            foreach($sql['join'] as $val)
            {
                list($table, $condition, $op) = $val;
                $sql['table'] = "{$sql['table']} {$op} JOIN {$table} ON {$condition}";
            }
        }

        $sql['limit'] = empty($sql['limit']) ? NULL : ' LIMIT ' . $sql['limit'];
        $sql['offset'] = empty($sql['offset']) ? NULL : ' OFFSET ' . $sql['offset'];

        return 'SELECT ' . $sql['fields'] . ' FROM ' . $sql['table'] .
        $sql['where'] . $sql['group'] . $sql['order'] . $sql['limit'] . $sql['offset'];
    }

    /**
     * 取出最后一次查询影响的行数
     *
     * @param resource $resource 查询返回资源标识
     * @return integer
     */
    public function affectedRows($resource)
    {
        return sqlite_changes($this->_dbHandle);
    }
    
    /**
     * 获取数据库版本
     * 
     * @access public
     * @return unknown
     */
    public function version()
    {
        return 'SQLite ' . sqlite_libversion();
    }

    /**
     * 取出最后一次插入返回的主键值
     *
     * @param resource $resource 查询返回资源标识
     * @return integer
     */
    public function lastInsertId($resource)
    {
        return sqlite_last_insert_rowid($this->_dbHandle);
    }
}
