<?php
/**
 * 描述记录输出
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
 * 描述记录输出组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class TagsWidget extends MetasWidget
{
    /**
     * 分割数量
     * 
     * @access private
     * @var integer
     */
    private $split;
    
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
        $size = min(intval($this->count / $this->split), $num - 1);
        
        echo $args[$size];
    }

    /**
     * 入口函数
     * 
     * @access public
     * @param string $sort 排序类型
     * @param integer $split 分割数量
     * @param boolean $ignoreZeroCount 忽略零数量
     * @return void
     */
    public function render($sort = 'count', $split = 5, $ignoreZeroCount = false)
    {
        $this->split = $split;
        $this->selectSql->where('`type` = ?', 'tag')->order('`' . $sort . '`', TypechoDb::SORT_DESC);
        
        /** 过滤标题 */
        if(empty(TypechoRoute::$current) && NULL != ($keywords = TypechoRequest::getParameter('keywords')) && $this->access->pass('editor', true))
        {
            $args = array();
            $keywords = explode(' ', $keywords);
            $args[] = implode(' OR ', array_fill(0, count($keywords), 'table.metas.`name` LIKE ?'));
            
            foreach($keywords as $keyword)
            {
                $args[] = '%' . Typecho::filterSearchQuery($keyword) . '%';
            }
            
            call_user_func_array(array($this->selectSql, 'where'), $args);
        }
        
        /** 忽略零数量 */
        if($ignoreZeroCount)
        {
            $this->selectSql->where('count > 0');
        }
        
        $this->db->fetchAll($this->selectSql, array($this, 'push'));
    }
}
