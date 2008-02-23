<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

class TypechoMysql implements TypechoDbAdapter
{
    /**
     * connect to database
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $password
     * @param string $charset
     * @throws LtException
     * @return resource
     */
    public function connect($host, $port, $db, $user, $password, $charset = NULL)
    {
        if($dbLink = @mysql_connect($host . ':' . $port, $user, $password))
        {
            if(@mysql_select_db($db, $dbLink))
            {
                if($charset)
                {
                    $this->query("SET NAMES '{$charset}'");
                }
                return $dbLink;
            }
        }

        throw new TypechoDbException(mysql_error());
    }
    
    /**
     * SQL query
     *
     * @param string $sql
     * @param boolean $op
     * @throws LtException
     * @return resource
     */
    public function query($sql, $op = __TYPECHO_DB_READ__)
    {
        if($resource = @mysql_query($sql))
        {
            return $resource;
        }
        
        throw new TypechoDbException(mysql_error());
    }
    
    /**
     * fetch rows
     *
     * @param resource $resource
     * @throws LtException
     * @return array
     */
    public function fetch($resource)
    {
        return mysql_fetch_assoc($resource);
    }
    
    /**
     * quotes string
     *
     * @param string $string
     * @return string
     */
    public function quotes($string)
    {
        return str_replace("'", "''", $string);
    }

    /**
     * get affected rows of last query
     *
     * @param resource $resource
     * @return integer
     */
    public function affectedRows($resource)
    {
        return mysql_affected_rows($resource);
    }
    
    /**
     * get last insert id of last query
     *
     * @param resource $resource
     * @return integer
     */
    public function lastInsertId($resource)
    {
        return mysql_insert_id($resource);
    }
}
