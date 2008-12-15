<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 后台评论输出组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Comments_Admin extends Widget_Abstract_Comments
{
    /**
     * 用于过滤的条件
     * 
     * @access private
     * @var array
     */
    private $_filterQuery = array();
    
    /**
     * 分页计算对象
     * 
     * @access private
     * @var Typecho_Db_Query
     */
    private $_countSql;
    
    /**
     * 当前页
     * 
     * @access private
     * @var integer
     */
    private $_currentPage;
    
    /**
     * 所有文章个数
     * 
     * @access private
     * @var integer
     */
    private $_total = false;
    
    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        $select = $this->select();
        $this->parameter->setDefault('pageSize=20');
        $this->_currentPage = $this->request->getParameter('page', 1);
    
        /** 过滤标题 */
        if (NULL != ($keywords = $this->request->keywords)) {
            $select->where('table.comments.text LIKE ?', '%' . Typecho_Common::filterSearchQuery($keywords) . '%');
            $this->_filterQuery['keywords'] = $keywords;
        }
        
        if (in_array($this->request->status, array('approved', 'waiting', 'spam'))) {
            $select->where('table.comments.status = ?', $this->request->status);
        } else {
            $select->where('table.comments.status = ? OR table.comments.status = ?', 'approved', 'waiting');
        }
    
        $this->_countSql = clone $select;
        
        $select->order('table.comments.created', Typecho_Db::SORT_DESC)
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
        $query = Typecho_Common::url('manage-comments.php?' . http_build_query($this->_filterQuery) . '&page={page}', $this->options->adminUrl);

        /** 使用盒状分页 */
        $nav = new Typecho_Widget_Helper_PageNavigator_Box(false === $this->_total ? $this->_total = $this->size($this->_countSql) : $this->_total,
        $this->_currentPage, $this->parameter->pageSize, $query);
        $nav->render(_t('&laquo;'), _t('&raquo;'));
    }
}
