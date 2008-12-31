<?php
/**
 * 标签云
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 标签云组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Metas_Tag_Cloud extends Widget_Abstract_Metas
{
    /**
     * 入口函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        $this->parameter->setDefault(array('sort' => 'count', 'split' => 5, 'ignoreZeroCount' => false, 'desc' => true));
        $select = $this->select()->where('type = ?', 'tag')->order($this->parameter->sort,
        $this->parameter->desc ? Typecho_Db::SORT_DESC : Typecho_Db::SORT_ASC);
        
        /** 忽略零数量 */
        if($this->parameter->ignoreZeroCount)
        {
            $select->where('count > 0');
        }
        
        $this->db->fetchAll($select, array($this, 'push'));
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
