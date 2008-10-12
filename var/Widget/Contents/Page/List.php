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
 * @package Widget_Contents_Page_List
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Page_List extends Widget_Abstract_Contents
{
    /**
     * 初始化函数
     * 
     * @access public
     * @param Typecho_Widget_Request $request 请求对象
     * @param Typecho_Widget_Response $response 回执对象
     * @param Typecho_Config $parameter 个体参数
     * @return void
     */
    public function init(Typecho_Widget_Request $request, Typecho_Widget_Response $response, Typecho_Config $parameter)
    {
        $this->db()->fetchAll($this->select()->where('table.contents.`type` = ?', 'page')
        ->group('table.contents.`cid`')->order('table.contents.`meta`', Typecho_Db::SORT_ASC), array($this, 'push'));
    }
}
