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
     * 初始化函数
     * 
     * @access public
     * @param Typecho_Widget_Request $request 请求对象
     * @param Typecho_Widget_Response $response 回执对象
     * @return void
     */
    public function init(Typecho_Widget_Request $request, Typecho_Widget_Response $response)
    {
        parent::__construct();
        
        $select = $this->select();
        $this->_pageSize = isset($this->parameter()->pageSize) ? 20 : $this->parameter()->pageSize;
        $this->_currentPage = $request->page or 1;
    
        /** 过滤标题 */
        if(NULL != ($keywords = $request->keywords))
        {
            $select->where('table.comments.`text` LIKE ?', '%' . Typecho_API::filterSearchQuery($keywords) . '%');
            $this->_filterQuery['keywords'] = $keywords;
        }
        
        if(in_array($request->status, array('approved', 'waiting', 'spam')))
        {
            $select->where('table.comments.`status` = ?', $request->status);
        }
    
        $this->_countSql = clone $select;
        
        $select->group('table.comments.`coid`')
        ->order('table.comments.`created`', Typecho_Db::SORT_DESC)
        ->page($this->_currentPage, $this->_pageSize);
        
        $this->db()->fetchAll($select, array($this, 'push'));
    }
    
    /**
     * 输出分页
     * 
     * @access public
     * @return void
     */
    public function pageNav()
    {
        $query = Typecho_Common::pathToUrl('comment-list.php?' . http_build_query($this->_filterQuery) . '&page={page}', $this->options()->adminUrl);

        /** 使用盒状分页 */
        $nav = new Typecho_Widget_Helper_PageNavigator_Box($this->count($this->_countSql), $this->_currentPage, $this->_pageSize, $query);
        $nav->render(_t('上一页'), _t('下一页'));
    }
}
