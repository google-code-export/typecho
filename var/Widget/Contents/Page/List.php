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
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        $this->db->fetchAll($this->select()->where('table.contents.type = ?', 'page')
        ->order('table.contents.meta', Typecho_Db::SORT_ASC), array($this, 'push'));
    }
}
