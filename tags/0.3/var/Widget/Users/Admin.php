<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 后台成员列表组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Users_Admin extends Widget_Abstract_Users
{
    /**
     * 分页计算对象
     * 
     * @access private
     * @var Typecho_Db_Query
     */
    private $_countSql;
    
    /**
     * 所有文章个数
     * 
     * @access private
     * @var integer
     */
    private $_total = false;
    
    /**
     * 分页大小
     * 
     * @access private
     * @var integer
     */
    private $_pageSize;
    
    /**
     * 当前页
     * 
     * @access private
     * @var integer
     */
    private $_currentPage;
    
    /**
     * 仅仅输出域名和路径
     * 
     * @access protected
     * @return void
     */
    protected function ___domainPath()
    {
        $parts = parse_url($this->url);
        return $parts['host'] . (isset($parts['path']) ? $parts['path'] : NULL);
    }
    
    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        $this->parameter->setDefault('pageSize=20');
        $select = $this->select();
        $this->_currentPage = Typecho_Request::getParameter('page', 1);
    
        /** 过滤标题 */
        if (NULL != ($keywords = $this->request->keywords)) {
            $select->where('name LIKE ? OR screenName LIKE ?',
            '%' . Typecho_Common::filterSearchQuery($keywords) . '%',
            '%' . Typecho_Common::filterSearchQuery($keywords) . '%');
        }
    
        $this->_countSql = clone $select;
        
        $select->order('table.users.uid', Typecho_Db::SORT_ASC)
        ->page($this->_currentPage, $this->parameter->pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
    
    /**
     * 输出分页
     * 
     * @access public
     * @return void
     */
    public function pageNav()
    {
        $query = $this->request->uri('page={page}');;

        /** 使用盒状分页 */
        $nav = new Typecho_Widget_Helper_PageNavigator_Box(false === $this->_total ? $this->_total = $this->size($this->_countSql) : $this->_total,
        $this->_currentPage, $this->parameter->pageSize, $query);
        $nav->render('&laquo;', '&raquo;');
    }
}