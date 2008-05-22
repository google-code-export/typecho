<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入文章基类支持 **/
require_once __TYPECHO_WIDGET_DIR__ . '/Abstract/Comments.php';

class AdminCommentsWidget extends CommentsWidget
{
    /**
     * 用于过滤的条件
     * 
     * @access private
     * @var array
     */
    private $_filterQuery = array();
    
    /**
     * 输出内容分页
     *
     * @access public
     * @return void
     */
    public function pageNav()
    {
        $query = Typecho::pathToUrl('comment-list.php?' . http_build_query($this->_filterQuery) . '&page={page}', $this->options->adminUrl);
        parent::pageNav($query);
    }

    /**
     * 入口函数
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $select = $this->selectSql;
    
        /** 过滤标题 */
        if(NULL != ($keywords = TypechoRequest::getParameter('keywords')))
        {
            $select->where('table.comments.`text` LIKE ?', '%' . Typecho::filterSearchQuery($keywords) . '%');
            $this->_filterQuery['keywords'] = $keywords;
        }
    
        $this->countSql = clone $select;
        
        $select->group('table.comments.`coid`')
        ->order('table.comments.`created`', TypechoDb::SORT_DESC)
        ->page($this->currentPage, $this->pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
