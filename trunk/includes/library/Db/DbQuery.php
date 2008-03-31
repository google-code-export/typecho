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
 * Typecho数据库查询语句构建类
 * 使用方法:
 * $query = new TypechoDbQuery();	//或者使用DB积累的sql方法返回实例化对象
 * $query->select('posts', 'post_id, post_title')
 * ->where('post_id = %d', 1)
 * ->limit(1);
 * echo $query;
 * 打印的结果将是
 * SELECT post_id, post_title FROM posts WHERE 1=1 AND post_id = 1 LIMIT 1
 * 
 *
 * @package Db
 */
class TypechoDbQuery
{
    /**
     * 数据库适配器
     * 
     * @var TypechoDbAdapter
     */
    private $_adapter;
    
    /**
     * 数据库SQL查询语句
     *
     * @var string
     */
    private $_sql;
    
    /**
     * 查询语句预结构,由数组构成,方便组合为SQL查询字符串
     *
     * @var array
     */
    private $_sqlPreBuild;

    /**
     * 构造函数,引用数据库适配器作为内部数据
     * 
     * @param TypechoDbAdapter $adapter 数据库适配器
     * @return void
     */
    public function __construct(TypechoDbAdapter $adapter)
    {
        $this->_adapter = &$adapter;
    }
    
    /**
     * 过滤表前缀,表前缀由table.构成
     * 
     * @param string $string 需要解析的字符串
     * @return string
     */
    private function filterPrefix($string)
    {
        return substr(preg_replace("/([^_a-zA-Z0-9-]+)table\.([0-9a-zA-Z-]+)/i", "\\1" . __TYPECHO_DB_PREFIX__ . "\\2", ' ' . $string), 1);
    }
    
    /**
     * 初始化参数
     * 
     * @return void
     */
    public function init()
    {
        $this->_sql = NULL;
        $this->_sqlPreBuild = array(
            'action' => NULL,
            'table'  => NULL,
            'fields' => NULL,
            'join'   => array(),
            'where'  => '1 = 1',
            'limit'  => NULL,
            'offset' => NULL,
            'order'  => NULL,
            'group'  => NULL,
            'rows'   => array(),
        );
    }
    
    /**
     * 获取当前SQL操作方式
     * 返回的值有INSERT,DELETE,SELECT,UPDATE
     * 
     * @return string
     */
    public function action()
    {
        return $this->_sqlPreBuild['action'];
    }
    
    /**
     * 连接表
     * 
     * @param string $table 需要连接的表
     * @param string $condition 连接条件
     * @param string $po 连接方法(LEFT, RIGHT, INNER)
     * @return TypechoDbQuery
     */
    public function join($table, $condition, $op = 'INNER')
    {
        $this->_sqlPreBuild['join'][] = array($this->filterPrefix($table), $this->filterPrefix($condition), $op);
        return $this;
    }
    
    /**
     * AND条件查询语句
     * 
     * @param string $condition 查询条件
     * @param mixed $param 条件值
     * @return TypechoDbQuery
     */
    public function where()
    {
        $condition = func_get_arg(0);
        $condition = $this->filterPrefix(str_replace('?', "'%s'", $condition));
        $operator = empty($this->_sqlPreBuild['where']) ? '' : ' AND';
    
        if(func_num_args() <= 1)
        {
            $this->_sqlPreBuild['where'] .= $operator . ' (' . $condition . ')';
        }
        else
        {
            $args = func_get_args();
            array_shift($args);
            $this->_sqlPreBuild['where'] .= $operator . ' (' . vsprintf($condition, array_map(array($this->_adapter, 'quotes'), $args)) . ')';
        }
        
        return $this;
    }
    
    /**
     * OR条件查询语句
     * 
     * @param string $condition 查询条件
     * @param mixed $param 条件值
     * @return TypechoDbQuery
     */
    public function orWhere()
    {
        $condition = func_get_arg(0);
        $condition = $this->filterPrefix(str_replace('?', "'%s'", $condition));
        $operator = empty($this->_sqlPreBuild['where']) ? '' : ' OR';
    
        if(func_num_args() <= 1)
        {
            $this->_sqlPreBuild['where'] .= $operator . ' (' . $condition . ')';
        }
        else
        {
            $args = func_get_args();
            array_shift($args);
            $this->_sqlPreBuild['where'] .= $operator . ' (' . vsprintf($condition, array_map(array($this->_adapter, 'quotes'), $args)) . ')';
        }
        
        return $this;
    }
    
    /**
     * 查询行数限制
     * 
     * @param integer $limit 需要查询的行数
     * @return TypechoDbQuery
     */
    public function limit($limit)
    {
        $this->_sqlPreBuild['limit'] = ' LIMIT ' . intval($limit);
        return $this;
    }
    
    /**
     * 查询行数偏移量
     * 
     * @param integer $offset 需要偏移的行数
     * @return TypechoDbQuery
     */
    public function offset($offset)
    {
        $this->_sqlPreBuild['offset'] = ' OFFSET ' . intval($offset);
        return $this;
    }
    
    /**
     * 分页查询
     * 
     * @param integer $page 页数
     * @param integer $pageSize 每页行数
     * @return TypechoDbQuery
     */
    public function page($page, $pageSize)
    {
        $pageSize = intval($pageSize);
        $this->_sqlPreBuild['limit'] = ' LIMIT ' . $pageSize;
        $this->_sqlPreBuild['offset'] = ' OFFSET ' . (max(intval($page), 1) - 1) * $pageSize;
        return $this;
    }
    
    /**
     * 指定需要写入的栏目及其值
     * 
     * @param array $rows
     * @return TypechoDbQuery
     */
    public function rows(array $rows)
    {
        $this->_sqlPreBuild['rows'] = array_map(array($this->_adapter, 'quotes'), $rows);
        return $this;
    }
    
    /**
     * 指定需要写入栏目及其值
     * 单行且不会转义引号
     * 
     * @param string $key 栏目名称
     * @param mixed $value 指定的值
     * @return TypechoDbQuery
     */
    public function row($key,$value)
    {
        $this->_sqlPreBuild['rows'][$key] = $value;
        return $this;
    }
    
    /**
     * 排序顺序(ORDER BY)
     * 
     * @param string $orderby 排序的索引
     * @param string $sort 排序的方式(ASC, DESC)
     * @return TypechoDbQuery
     */
    public function order($orderby, $sort = NULL)
    {
        $this->_sqlPreBuild['order'] = ' ORDER BY ' . $this->filterPrefix($orderby) . (empty($sort) ? NULL : ' ' . $sort);
        return $this;
    }
    
    /**
     * 集合聚集(GROUP BY)
     * 
     * @param string $key 聚集的键值
     * @return TypechoDbQuery
     */
    public function group($key)
    {
        $this->_sqlPreBuild['group'] = ' GROUP BY ' . $this->filterPrefix($key);
        return $this;
    }
    
    /**
     * 查询记录操作(SELECT)
     * 
     * @param string $table 查询的表
     * @param string $fields 需要查询的栏目
     * @return TypechoDbQuery
     */
    public function select($table,$fields = '*')
    {
        $this->_sqlPreBuild['action'] = 'SELECT';
        $this->_sqlPreBuild['fields'] = $this->filterPrefix($fields);
        $this->_sqlPreBuild['table'] = $this->filterPrefix($table);
        return $this;
    }
    
    /**
     * 更新记录操作(UPDATE)
     * 
     * @param string $table 需要更新记录的表
     * @return TypechoDbQuery
     */
    public function update($table)
    {
        $this->_sqlPreBuild['action'] = 'UPDATE';
        $this->_sqlPreBuild['table'] = $this->filterPrefix($table);
        return $this;
    }
    
    /**
     * 删除记录操作(DELETE)
     * 
     * @param string $table 需要删除记录的表
     * @return TypechoDbQuery
     */
    public function delete($table)
    {
        $this->_sqlPreBuild['action'] = 'DELETE';
        $this->_sqlPreBuild['table'] = $this->filterPrefix($table);
        return $this;
    }
    
    /**
     * 插入记录操作(INSERT)
     * 
     * @param string $table 需要插入记录的表
     * @return TypechoDbQuery
     */
    public function insert($table)
    {
        $this->_sqlPreBuild['action'] = 'INSERT';
        $this->_sqlPreBuild['table'] = $this->filterPrefix($table);
        return $this;
    }
    
    /**
     * 构造最终查询语句
     * 
     * @return string
     */
    public function __toString()
    {
        if(!(empty($this->_sql)))
        {
            return $this->_sql;
        }
        
        switch($this->_sqlPreBuild['action'])
        {
            case 'SELECT':
            {
                if($this->_sqlPreBuild['join'])
                {
                    foreach($this->_sqlPreBuild['join'] as $val)
                    {
                        list($table, $condition,$op) = $val;
                        $this->_sqlPreBuild['table'] = "({$this->_sqlPreBuild['table']} {$op} JOIN {$table} ON {$condition})";
                    }
                }
                
                $this->_sql = 'SELECT ' 
                . $this->_sqlPreBuild['fields'] . ' FROM ' 
                . $this->_sqlPreBuild['table'] 
                . ' WHERE ' . $this->_sqlPreBuild['where'] 
                . $this->_sqlPreBuild['group'] 
                . $this->_sqlPreBuild['order'] 
                . $this->_sqlPreBuild['limit']
                . $this->_sqlPreBuild['offset'];
                break;
            }
            case 'INSERT':
            {
                return 'INSERT INTO '
                . $this->_sqlPreBuild['table'] 
                . '(`' . implode('` , `', array_keys($this->_sqlPreBuild['rows'])) . '`)'
                . ' VALUES '
                . "('" . implode("' , '", array_values($this->_sqlPreBuild['rows'])) . "')"
                . $this->_sqlPreBuild['limit'];
                break;
            }
            case 'DELETE':
            {
                return 'DELETE FROM '
                . $this->_sqlPreBuild['table'] 
                . ' WHERE ' . $this->_sqlPreBuild['where'] 
                . $this->_sqlPreBuild['limit'];
                break;
            }
            case 'UPDATE':
            {
                $columns = array();
                if(isset($this->_sqlPreBuild['rows']))
                {
                    foreach($this->_sqlPreBuild['rows'] as $key => $val)
                    {
                        $columns[] = "`$key` = $val";
                    }
                }
                
                return 'UPDATE '
                . $this->_sqlPreBuild['table'] 
                . ' SET ' . implode(' , ',$columns)
                . ' WHERE ' . $this->_sqlPreBuild['where']
                . $this->_sqlPreBuild['limit'];
                break;
            }
            default:
            {
                $this->_sql = NULL;
                break;
            }
        }
        
        return $this->_sql;
    }
}
