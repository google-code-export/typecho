<?php
/**
 * 按日期归档列表组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 按日期归档列表组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Post_Date extends Typecho_Widget
{
    /**
     * 构造函数,设置日期
     * 
     * @access public
     * @param string $type 归档类型
     * @param string $format 日期格式
     * @param imteger $pageSize 输出个数
     * @return void
     */
    public function __construct($type = 'month', $format = 'Y-m', $pageSize = NULL)
    {
        $db = Typecho_Db::get();
    
        $posts = $db->fetchAll($db->sql()->select('table.contents', '`created`')
        ->where('type = ?', 'post')
        ->where('table.contents.`created` < ?', Typecho_API::factory('Widget_Options')->gmtTime)
        ->order('table.contents.`created`', Typecho_Db::SORT_DESC));
        
        $result = array();
        foreach($posts as $post)
        {
            $date = date($format, $post['created']);
            if(isset($result[$date]))
            {
                $result[$date]['count'] ++;
            }
            else
            {
                $result[$date]['year'] = date('Y', $post['created']);
                $result[$date]['month'] = date('m', $post['created']);
                $result[$date]['day'] = date('d', $post['created']);
                $result[$date]['date'] = $date;
                $result[$date]['count'] = 1;
            }
        }
        
        foreach($result as $row)
        {
            $row['permalink'] = Typecho_Router::url('archive_' . $type, $row, Typecho_API::factory('Widget_Options')->index);
            $this->push($row);
        }
    }
}
