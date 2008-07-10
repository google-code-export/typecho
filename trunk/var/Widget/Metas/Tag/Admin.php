<?php
/**
 * 标签列表
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 标签列表组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Metas_Tag_Admin extends Widget_Abstract_Metas
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
     * 入口函数
     * 
     * @access public
     * @param string $pageSize 分页数量
     * @return void
     */
    public function __construct($pageSize = NULL)
    {
        parent::__construct();
        
        $this->_pageSize = empty($pageSize) ? 20 : $pageSize;
        $this->_currentPage = Typecho_Request::getParameter('page', 1);
        $select = $this->select()->where('`type` = ?', 'tag');
        
        /** 过滤标题 */
        if(NULL != ($keywords = Typecho_Request::getParameter('keywords')))
        {
            $args = array();
            $keywords = explode(' ', $keywords);
            $args[] = implode(' OR ', array_fill(0, count($keywords), 'table.metas.`name` LIKE ?'));
            
            foreach($keywords as $keyword)
            {
                $args[] = '%' . Typecho_API::filterSearchQuery($keyword) . '%';
            }
            
            call_user_func_array(array($select, 'where'), $args);
            $this->_filterQuery['keywords'] = $keywords;
        }
        
        $this->_countSql = clone $select;
        
        $select->order('table.metas.`mid`', Typecho_Db::SORT_DESC)
        ->page($this->_currentPage, $this->_pageSize);
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
        $query = Typecho_API::pathToUrl('manage-tag.php?' . http_build_query($this->_filterQuery) . '&page={page}', $this->options->adminUrl);
 
        /** 使用盒状分页 */
        $nav = new Typecho_Widget_Helper_PageNavigator_Box($this->size($this->_countSql), $this->_currentPage, $this->_pageSize, $query);
        $nav->render(_t('上一页'), _t('下一页'));
    }
}
