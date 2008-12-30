<?php
/**
 * 独立页面列表
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 独立页面列表组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Page_List extends Widget_Abstract_Contents
{
    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        $this->db->fetchAll($this->select()->where('table.contents.type = ?', 'page')
        ->where('table.contents.status = ?', 'publish')
        ->order('table.contents.order', Typecho_Db::SORT_ASC), array($this, 'push'));
    }
}
