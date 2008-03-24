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
 * 全局变量管理
 * 
 * @package Options
 */
class Options extends TypechoWidget
{
    /**
     * 动态获取网站地址
     * 
     * @access private
     * @return string
     */
    private function getSiteUrl()
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }
    
    /**
     * 重载父类push函数,将所有变量值压入堆栈
     * 
     * @access public
     * @param array $value 每行的值
     * @return array
     */
    public function push(array $value)
    {
        //将行数据按顺序置位
        $this->_row[$value['name']] = $value['value'];
        $this->_stack[] = $value;
        return $value;
    }

    /**
     * 运行入口函数
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $db = TypechoDb::get();

        $db->fetchAll($db->sql()
        ->select('table.options')
        ->where('user = 0'), array($this, 'push'));

        $this->_row['siteURL'] = $this->getSiteUrl();
        $this->_row['index'] = $this->_row['rewrite'] ? $this->_row['siteURL'] : $this->_row['siteURL'] . '/index.php';
        $this->_row['templateURL'] = $this->_row['siteURL'] . '/var/template/' . $this->_row['template'];
        $this->_row['gmtTime'] = time() + intval(date('Z'));
        $this->_row['xmlrpcURL'] = $this->_row['index'] . '/XMLPRC.do';
        $this->_row['rssURL'] = TypechoRoute::parse('rss', NULL, $this->_rows['index']);
        $this->_row['adminURL'] = $this->_row['siteURL'] . '/admin.php';
        
        header('content-Type: text/html;charset= ' . $this->_row['charset']);
    }
}
