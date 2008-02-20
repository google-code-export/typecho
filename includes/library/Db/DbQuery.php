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
 * Typecho数据库构建类
 *
 */
class TypechoDbQuery
{
    /**
     * 数据库适配器
     * @var TypechoDbAdapter
     */
    private $_adapter;
    
    /**
     * 数据库SQL查询语句
     * @var string
     */
    private $_sql;
    
    /**
     * 查询语句预结构,由数组构成,方便组合为SQL查询字符串
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
    
    private function filterPrefix($string)
    {
        return substr(preg_replace("/([^_a-zA-Z0-9-]+)table\.([0-9a-zA-Z-]+)/i", "\\1" . __DBPREFIX__ . "\\2", ' ' . $string), 1);
    }
    
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
    
    public function action()
    {
        return $this->_sqlPreBuild['action'];
    }
    
    public function join($table, $condition, $op = 'INNER')
    {
        $this->_sqlPreBuild['join'][] = array($this->filterPrefix($table), $this->filterPrefix($condition), $op);
        return $this;
    }
    
    public function where()
    {
        if(func_num_args() <= 1)
        {
            $this->_sqlPreBuild['where'] .= ' AND (' . func_get_arg(0) . ')';
        }
        else
        {
            $args = func_get_args();
            $string = $this->filterPrefix(str_replace(array('%s', '%d'), array("'%s'", '%s'), array_shift($args)));
            $this->_sqlPreBuild['where'] .= ' AND (' . vsprintf($string, array_map(array($this->_adapter, 'quotes'), $args)) . ')';
        }
        
        return $this;
    }
    
    public function orWhere()
    {
        if(func_num_args() <= 1)
        {
            $this->_sqlPreBuild['where'] .= ' OR (' . func_get_arg(0) . ')';
        }
        else
        {
            $args = func_get_args();
            $string = $this->filterPrefix(str_replace(array('%s', '%d'), array("'%s'", '%s'), array_shift($args)));
            $this->_sqlPreBuild['where'] .= ' OR (' . vsprintf($string, array_map(array($this->_adapter, 'quotes'), $args)) . ')';
        }
        
        return $this;
    }
    
    public function limit($limit)
    {
        $this->_sqlPreBuild['limit'] = ' LIMIT ' . intval($limit);
        return $this;
    }
    
    public function offset($limit)
    {
        $this->_sqlPreBuild['offset'] = ' OFFSET ' . intval($limit);
        return $this;
    }
    
    public function rows($rows)
    {
        $this->_sqlPreBuild['rows'] = array_map(array($this->_adapter, 'quotes'), $rows);
        return $this;
    }
    
    public function row($key,$value)
    {
        $this->_sqlPreBuild['rows'][$key] = $value;
        return $this;
    }
    
    public function order($orderby, $sort = NULL)
    {
        $this->_sqlPreBuild['order'] = ' ORDER BY ' . $this->filterPrefix($orderby) . (empty($sort) ? NULL : ' ' . $sort);
        return $this;
    }
    
    public function group($key)
    {
        $this->_sqlPreBuild['group'] = ' GROUP BY ' . $this->filterPrefix($key);
        return $this;
    }
    
    public function select($table,$fields = '*')
    {
        $this->_sqlPreBuild['action'] = 'SELECT';
        $this->_sqlPreBuild['fields'] = $fields;
        $this->_sqlPreBuild['table'] = $this->filterPrefix($table);
        return $this;
    }
    
    public function update($table)
    {
        $this->_sqlPreBuild['action'] = 'UPDATE';
        $this->_sqlPreBuild['table'] = $this->filterPrefix($table);
        return $this;
    }
    
    public function delete($table)
    {
        $this->_sqlPreBuild['action'] = 'DELETE';
        $this->_sqlPreBuild['table'] = $this->filterPrefix($table);
        return $this;
    }
    
    public function insert($table)
    {
        $this->_sqlPreBuild['action'] = 'INSERT';
        $this->_sqlPreBuild['table'] = $this->filterPrefix($table);
        return $this;
    }
    
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
