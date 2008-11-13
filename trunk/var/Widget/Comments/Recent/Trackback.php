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
     * @return void
     */
    public function init()
    {
        $this->parameter->setDefault(array('pageSize' => $this->options->postsListSize));
        
        $this->db()->fetchAll($this->select()->limit($this->parameter->pageSize)
        ->where('table.comments.type = ?', 'trackback')
        ->where('table.comments.status = ?', 'approved')
        ->order('table.comments.created', Typecho_Db::SORT_DESC), array($this, 'push'));
    }
}
