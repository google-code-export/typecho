<?php
/**
 * 独立页面管理列表
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 独立页面管理列表组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Page_Admin extends Widget_Abstract_Contents
{
    /**
     * 构造函数
     * 
     * @access public
     * @param integer $pageSize 分页大小
     * @return void
     */
    public function __construct($pageSize = NULL)
    {
        parent::__construct();
        Typecho_API::factory('Widget_Users_Current')->pass('editor');

        /** 构建基础查询 */
        $select = $this->select();
        
        /** 过滤状态 */
        $status = Typecho_Request::getParameter('status');
        switch($status)
        {
            case 'draft':
                $select->where('table.contents.`type` = ?', 'page_draft');
                break;
            case 'page':
                $select->where('table.contents.`type` = ?', 'page');
                break;
            case 'all':
            default:
                $select->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'page', 'page_draft');
                break;
        }
        
        /** 过滤标题 */
        if(NULL != ($keywords = Typecho_Request::getParameter('keywords')))
        {
            $args = array();
            $keywordsList = explode(' ', $keywords);
            $args[] = implode(' OR ', array_fill(0, count($keywordsList), 'table.contents.`title` LIKE ?'));
            
            foreach($keywordsList as $keyword)
            {
                $args[] = '%' . Typecho_API::filterSearchQuery($keyword) . '%';
            }
            
            call_user_func_array(array($select, 'where'), $args);
        }
        
        /** 提交查询 */
        $select->group('table.contents.`cid`')
        ->order('table.contents.`meta`', Typecho_Db::SORT_ASC);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
