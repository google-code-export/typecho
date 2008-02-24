<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

abstract class AbstractArchivesComponent extends TypechoComponent
{
    private $_stack = array();
    protected $rows = array();
    
    /**
     * 将每一行的值压入堆栈
     *
     * @param array $value 每一行的值
     * @return array
     */
    public function push(array $value)
    {
        $this->_stack[] = $value;
        return $value;
    }
    
    public function have()
    {
        return !empty($this->_stack);
    }
    
    public function get()
    {
        $this->rows = current($this->_stack);
        next($this->_stack);
        return $this->rows;
    }
    
    public function __call($name, $args)
    {
        echo isset($this->rows[$name]) ? $this->rows[$name] : NULL;
    }
}
