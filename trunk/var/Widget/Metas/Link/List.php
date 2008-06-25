<?php
/**
 * 链接输出
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 链接输出组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Metas_Link_List extends Typecho_Widget
{
    /**
     * 初始化数据
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        $db = Typecho_Db::get();
        $select = $db->sql()->select('table.metas', '`mid`, `slug` AS `url`, `name`, `description`');
        
        $db->fetchAll($select->where('`type` = ?', 'link')->order('`sort`', Typecho_Db::SORT_ASC), array($this, 'push'));
    }
}
