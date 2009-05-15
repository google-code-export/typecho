<?php
/**
 * 没有关联的附件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 没有关联的附件组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Attachment_Free extends Widget_Abstract_Contents
{
    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        /** 构建基础查询 */
        $select = $this->select()->where('table.contents.type = ? AND (table.contents.order = 0 OR table.contents.order IS NULL)', 'attachment');
        
        /** 提交查询 */
        $select->order('table.contents.created', Typecho_Db::SORT_DESC);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
