<?php
/**
 * 评论归档,仅显示反馈
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 评论归档组件,仅显示反馈
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Comments_Archive_Trackback extends Widget_Abstract_Comments
{
    /**
     * 构造函数,根据内容归档评论
     * 
     * @access public
     * @param integer $cid 内容主键
     * @return void
     */
    public function __construct($cid)
    {
        /** 初始化评论 */
        parent::__construct();
        
        $this->db->fetchAll($this->select()->where('table.comments.`status` = ?', 'approved')
        ->where('table.comments.`mode` = ?', 'trackback')
        ->where('table.contents.`cid` = ?', $cid)->group('table.comments.`coid`')
        ->order('table.comments.`created`', TypechoDb::SORT_DESC), array($this, 'push'));
    }
}
