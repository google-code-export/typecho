<?php
/**
 * 分类输出
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 分类输出组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Metas_Category_List extends Widget_Abstract_Metas
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
        $this->db()->fetchAll($this->select()->where('`type` = ?', 'category')
        ->order('table.metas.`sort`', Typecho_Db::SORT_ASC), array($this, 'push'));
    }
}
