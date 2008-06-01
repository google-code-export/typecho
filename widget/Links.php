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
        
        $db->fetchAll($db->sql()->select('table.metas', '`mid`, `slug` AS `url`, `name`, `description`')
        ->where('`type` = ?', 'link')->order('`sort`', TypechoDb::SORT_ASC), array($this, 'push'));
    }
}
