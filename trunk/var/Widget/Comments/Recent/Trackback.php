<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 最近评论组件,只显示饮用
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Comments_Recent_Trackback extends Widget_Abstract_Comments
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
        $pageSize = isset($this->parameter()->pageSize) ? $this->options->commentsListSize : $this->parameter()->pageSize;
        
        $this->db()->fetchAll($this->select()->limit($pageSize)
        ->where('table.contents.`password` IS NULL')
        ->where('table.comments.`type` = ?', 'trackback')
        ->where('table.comments.`status` = ?', 'approved')
        ->group('table.comments.`coid`')
        ->order('table.comments.`created`', Typecho_Db::SORT_DESC), array($this, 'push'));
    }
}
