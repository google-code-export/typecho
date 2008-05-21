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
require_once __TYPECHO_WIDGET_DIR__ . '/Abstract/Contents.php';

/**
 * 文章管理列表组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class AdminPostsWidget extends ContentsWidget
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
        $query = Typecho::pathToUrl('post-list.php?' . http_build_query($this->_filterQuery) . '&page={page}',
        $this->options->adminUrl);
        parent::pageNav($query);
    }

    /**
     * 入口函数
     * 
     * @access public
     * @return void
     */
    public function render($pageSize = NULL)
    {
        $this->pageSize = empty($pageSize) ? 20 : $pageSize;
        $this->currentPage = (NULL == TypechoRequest::getParameter('page')) ? 1 : TypechoRequest::getParameter('page');

        /** 构建基础查询 */
        $select = $this->selectSql->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'post', 'draft');

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
        ->page($this->currentPage, $this->pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
