<?php
/**
 * 文章管理列表
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入父类 */
require_once 'Posts.php';

/**
 * 文章管理列表组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class AdminPostsWidget extends PostsWidget
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
        $num = $this->db->fetchObject($this->countSql->select('table.contents', 'COUNT(table.contents.`cid`) AS `num`'))->num;
        $query = 'post-list.php?' . http_build_query($this->_filterQuery) . '&page={page}';

        $nav = new TypechoWidgetNavigator($num,
                                          $this->_currentPage,
                                          $this->_pageSize,
                                          Typecho::pathToUrl($query, $this->options->adminUrl));

        $nav->makeBoxNavigator(_t('上一页'), _t('下一页'));
    }

    /**
     * 入口函数
     * 
     * @access public
     * @return void
     */
    public function render($pageSize = NULL)
    {
        $this->_pageSize = empty($pageSize) ? 20 : $pageSize;
        $this->_currentPage = (NULL == TypechoRequest::getParameter('page')) ? 1 : TypechoRequest::getParameter('page');

        /** 构建基础查询 */
        $select = $this->db->sql()
        ->select('table.contents', 'table.contents.`cid`, table.contents.`title`, table.contents.`slug`, table.contents.`created`,
        table.contents.`type`, table.contents.`text`, table.contents.`commentsNum`, 
        table.users.`screenName` AS `author`, table.contents.`author` AS `authorId`')
        ->join('table.users', 'table.contents.`author` = table.users.`uid`', TypechoDb::LEFT_JOIN)
        ->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'post', 'draft');

        /** 过滤分类 */
        if(NULL != ($category = TypechoRequest::getParameter('category')))
        {
            $select->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
            ->where('table.relationships.`mid` = ?', $category);
            
            $this->_filterQuery['category'] = $category;
        }

        /** 获取状态过滤条件 */
        if(NULL != ($status = TypechoRequest::getParameter('status')))
        {
            $this->_filterQuery['status'] = $status;
        }
        
        /** 如果具有编辑以上权限,可以查看所有文章,反之只能查看自己的文章 */
        if(!Typecho::widget('Access')->pass('editor', true) || 
        ('myDraft' == $status || 'myPost' == $status || 'my' == $status))
        {
            $select->where('table.contents.`author` = ?', Typecho::widget('Access')->uid);
        }
        
        /** 过滤状态 */
        switch($status)
        {
            case 'myDraft':
            case 'allDraft':
                $select->where('table.contents.`type` = ?', 'draft');
                break;
            case 'myPost':
            case 'allPost':
                $select->where('table.contents.`type` = ?', 'post');
                break;
            case 'my':
            case 'all':
            default:
                $select->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'post', 'draft');
                break;
        }
        
        /** 过滤标题 */
        if(NULL != ($keywords = TypechoRequest::getParameter('keywords')))
        {
            $select->where('table.contents.`title` LIKE ?', '%' . Typecho::filterSearchQuery($keywords) . '%');
            
            $this->_filterQuery['keywords'] = $keywords;
        }
        
        /** 给计算数目对象赋值,克隆对象 */
        $this->countSql = clone $select;
        
        /** 提交查询 */
        $select->group('table.contents.`cid`')
        ->order('table.contents.`created`', TypechoDb::SORT_DESC)
        ->page($this->_currentPage, $this->_pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
