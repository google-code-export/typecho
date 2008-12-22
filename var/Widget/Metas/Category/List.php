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
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        $this->parameter->setDefault('split=5');
        $this->db->fetchAll($this->select()->where('type = ?', 'category')
        ->order('table.metas.sort', Typecho_Db::SORT_ASC), array($this, 'push'));
    }
    
    /**
     * 按分割数输出字符串
     * 
     * @access public
     * @param string $param 需要输出的值
     * @return void
     */
    public function split()
    {
        $args = func_get_args();
        $num = func_num_args();
        $size = min(intval($this->count / $this->parameter->split), $num - 1);
        
        echo $args[$size];
    }
}
