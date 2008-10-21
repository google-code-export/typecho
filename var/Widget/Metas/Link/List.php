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
     * 数据库对象
     * 
     * @access protected
     * @var Typecho_Db
     */
    protected $db;
    
    /**
     * 准备函数
     * 
     * @access public
     * @return void
     */
    public function prepare()
    {
        /** 初始化数据库 */
        $this->db = Typecho_Db::get();
    }

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
     * @return void
     */
    public function init()
    {
        $select = $this->db->select('mid', 'name', 'description', array('slug' => 'url'))->from('table.metas');
        $this->db->fetchAll($select->where('type = ?', 'link')->order('sort', Typecho_Db::SORT_ASC), array($this, 'push'));
    }
}
