<?php
/**
 * 链接输出
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 链接输出组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class LinksWidget extends TypechoWidget
{
    /**
     * 初始化数据
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $db = TypechoDb::get();
        $select = $db->sql()->select('table.metas', '`mid`, `slug` AS `url`, `name`, `description`');
        
        /** 过滤标题 */
        if(empty(TypechoRoute::$current) && NULL != ($keywords = TypechoRequest::getParameter('keywords')) && Typecho::widget('Access')->pass('editor', true))
        {
            $args = array();
            $keywords = explode(' ', $keywords);
            $args[] = implode(' OR ', array_fill(0, count($keywords), 'table.metas.`name` LIKE ?'));
            
            foreach($keywords as $keyword)
            {
                $args[] = '%' . Typecho::filterSearchQuery($keyword) . '%';
            }
            
            call_user_func_array(array($select, 'where'), $args);
        }
        
        $db->fetchAll($select->where('`type` = ?', 'link')->order('`sort`', TypechoDb::SORT_ASC), array($this, 'push'));
    }
}
