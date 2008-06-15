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
     * 初始化数据
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->db->fetchAll($this->select()->where('`type` = ?', 'category')
        ->order('table.metas.`sort`', Typecho_Db::SORT_ASC), array($this, 'push'));
    }
}
