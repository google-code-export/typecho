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

/** 载入父类 */
require_once 'Abstract/Metas.php';

/**
 * 分类输出组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class CategoriesWidget extends MetasWidget
{    
    /**
     * 初始化数据
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $this->db->fetchAll($this->selectSql->where('`type` = ?', 'category')
        ->order('table.metas.`sort`', TypechoDb::SORT_ASC), array($this, 'push'));
    }
}
