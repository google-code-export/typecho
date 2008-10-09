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
     * 仅仅输出域名和路径
     * 
     * @access public
     * @return void
     */
    public function domainPath()
    {
        $parts = parse_url($this->url);
        echo $parts['host'] . (isset($parts['path']) ? $parts['path'] : NULL);
    }

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
        $select = $this->db()->sql()->select('table.metas', '`mid`, `slug` AS `url`, `name`, `description`');
        $this->db()->fetchAll($select->where('`type` = ?', 'link')->order('`sort`', Typecho_Db::SORT_ASC), array($this, 'push'));
    }
}
