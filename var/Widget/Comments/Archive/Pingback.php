<?php
/**
 * 评论归档,仅显示广播
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 评论归档组件,仅显示广播
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Comments_Archive_Pingback extends Widget_Abstract_Comments
{
    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        $this->parameter->setDefault('desc=0');
    
        $this->db->fetchAll($this->select()->where('table.comments.status = ?', 'approved')
        ->where('table.comments.mode = ?', 'pingback')
        ->where('table.comments.cid = ?', $this->parameter->cid)
        ->order('table.comments.created', $this->parameter->desc ? Typecho_Db::SORT_DESC : Typecho_Db::SORT_ASC), array($this, 'push'));
    }
}
