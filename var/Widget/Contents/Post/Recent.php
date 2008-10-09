<?php
/**
 * 最新文章
 * 
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 最新评论组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Post_Recent extends Widget_Abstract_Contents
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
        $pageSize = isset($this->parameter()->pageSize) ? $this->options()->postsListSize : $this->parameter()->pageSize;
    
        $this->db()->fetchAll($this->select()->where('table.contents.`password` IS NULL')
        ->where('table.contents.`created` < ?', $this->options()->gmtTime)
        ->where('table.contents.`type` = ?', 'post')
        ->group('table.contents.`cid`')
        ->order('table.contents.`created`', Typecho_Db::SORT_DESC)
        ->limit($pageSize), array($this, 'push'));
    }
}
