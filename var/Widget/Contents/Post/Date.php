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
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        /** 设置参数默认值 */
        $this->parameter->setDefault('format=Y-m&type=month');
    
        $posts = $this->db->fetchAll($this->db->select('created')->from('table.contents')
        ->where('type = ?', 'post')
        ->where('table.contents.created < ?', $this->widget('Widget_Options')->gmtTime)
        ->order('table.contents.created', Typecho_Db::SORT_DESC));
        
        $result = array();
        foreach($posts as $post)
        {
            $date = date($this->parameter->format, $post['created']);
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
            $row['permalink'] = Typecho_Router::url('archive_' . $this->parameter->type, $row, $this->widget('Widget_Options')->index);
            $this->push($row);
        }
    }
}
