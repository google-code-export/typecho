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
     * @param Typecho_Widget_Request $request 请求对象
     * @param Typecho_Widget_Response $response 回执对象
     * @return void
     */
    public function init(Typecho_Widget_Request $request, Typecho_Widget_Response $response)
    {
        $this->db->fetchAll($this->select()->where('table.comments.`status` = ?', 'approved')
        ->where('table.comments.`mode` = ?', 'pingback')
        ->where('table.contents.`cid` = ?', $this->parameter()->cid)->group('table.comments.`coid`')
        ->order('table.comments.`created`', $this->parameter()->desc ? Typecho_Db::SORT_DESC : Typecho_Db::SORT_ASC), array($this, 'push'));
    }
}
