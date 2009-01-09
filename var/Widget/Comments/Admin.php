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
        $this->_currentPage = $this->request->nil(1)->page;
    
        /** 过滤标题 */
        if (NULL != ($keywords = $this->request->filter('search')->keywords)) {
            $select->where('table.comments.text LIKE ?', '%' . $keywords . '%');
        }
        
        /** 如果具有贡献者以上权限,可以查看所有评论,反之只能查看自己的评论 */
        if (!$this->user->pass('contributor', true)) {
            $select->where('table.comments.ownerId = ?', $this->user->uid);
        } else {
            if ('on' == $this->request->__typecho_all_comments) {
                $this->response->setCookie('__typecho_all_comments', 'on');
            } else {
                if ('off' == $this->request->__typecho_all_comments) {
                    $this->response->setCookie('__typecho_all_comments', 'off');
                }
                $select->where('table.comments.ownerId = ?', $this->user->uid);
            }
        }
        
        if (in_array($this->request->status, array('approved', 'waiting', 'spam'))) {
            $select->where('table.comments.status = ?', $this->request->status);
        } else if ('all' != $this->request->status) {
            $select->where('table.comments.status = ?', 'approved');
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
        $query = $this->request->uri('page={page}');

        /** 使用盒状分页 */
        $nav = new Typecho_Widget_Helper_PageNavigator_Box(false === $this->_total ? $this->_total = $this->size($this->_countSql) : $this->_total,
        $this->_currentPage, $this->parameter->pageSize, $query);
        $nav->render(_t('&laquo;'), _t('&raquo;'));
    }
}
