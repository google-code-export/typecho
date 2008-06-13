<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Mysql.php 89 2008-03-31 00:10:57Z magike.net $
 */

/**
 * 数据库PDOMysql适配器
 *
 * @package Db
 */
class TypechoPDOPgsqlDbAdapter implements TypechoDbAdapter
{
    /**
     * 数据库对象
     *
     * @access private
     * @var PDO
     */
    private $_object;

    /**
     * 数据库连接函数
     *
     * @param TypechoConfig $config 数据库配置
     * @throws TypechoDbException
     * @return resource
     */
    public function connect(TypechoConfig $config)
    {
        try
        {
            $this->_object = new PDO("pgsql:dbname={$config->database};host={$config->host};port={$config->port}", $config->user, $config->password);
            $this->_object->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_object->exec("SET NAMES '{$config->charset}'");
        }
        catch(PDOException $e)
        {
            throw new TypechoDbException(__TYPECHO_DEBUG__ ?
            $e->getMessage() : _t('数据库连接错误'), TypechoException::UNVAILABLE);
        }
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
        try
        {
            $this->_lastInsertTable = (!empty($action) && 'INSERT' == $action) ? $query->getAttribute('table') : NULL;
            $resource = $this->_object->prepare($query instanceof TypechoDbQuery ? $query->__toString() : $query);
            $resource->execute();
        }
        catch(PDOException $e)
        {
            throw new TypechoDbException(__TYPECHO_DEBUG__ ?
            $e->getMessage() : _t('数据库查询错误'), TypechoException::RUNTIME);
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
        return $resource->rowCount();
    }
    
    /**
     * 获取数据库版本
     * 
     * @access public
     * @return unknown
     */
    public function version()
    {
        $resource = $this->query('SELECT VERSION() AS version');
        $rows = $this->fetch($resource);
        return 'PostgreSQL ' . $rows['version'];
    }

    /**
     * 取出最后一次插入返回的主键值
     *
     * @param resource $resource 查询返回资源标识
     * @return integer
     */
    public function lastInsertId($resource)
    {
        return $this->_object->lastInsertId($this->_lastInsertTable);
    }
}
