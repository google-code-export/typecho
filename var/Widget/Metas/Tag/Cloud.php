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
     * 分割数量
     * 
     * @access private
     * @var integer
     */
    private $_split;
    
    /**
     * 入口函数
     * 
     * @access public
     * @param string $sort 排序类型
     * @param integer $split 分割数量
     * @param boolean $ignoreZeroCount 忽略零数量
     * @return void
     */
    public function __construct($sort = 'count', $split = 5, $ignoreZeroCount = false)
    {
        parent::__construct();
        
        $this->_split = $split;
        $select = $this->select()->where('`type` = ?', 'tag')->order('`' . $sort . '`', Typecho_Db::SORT_DESC);
        
        /** 过滤标题 */
        if(empty(Typecho_Router::$current) &&
        NULL != ($keywords = Typecho_Request::getParameter('keywords')) &&
        Typecho_API::factory('Widget_Users_Current')->pass('editor', true))
        {
            $args = array();
            $keywords = explode(' ', $keywords);
            $args[] = implode(' OR ', array_fill(0, count($keywords), 'table.metas.`name` LIKE ?'));
            
            foreach($keywords as $keyword)
            {
                $args[] = '%' . Typecho_API::filterSearchQuery($keyword) . '%';
            }
            
            call_user_func_array(array($select, 'where'), $args);
        }
        
        /** 忽略零数量 */
        if($ignoreZeroCount)
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
        $size = min(intval($this->count / $this->_split), $num - 1);
        
        echo $args[$size];
    }
}
