<?php
/**
 * 全局统计
 * 
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 全局统计组件
 * 
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Stat extends Typecho_Widget
{
    /**
     * 全局选项
     * 
     * @access protected
     * @var Widget_Options
     */
    protected $options;

    /**
     * 用户对象
     * 
     * @access protected
     * @var Widget_User
     */
    protected $user;
    
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
    
        /** 初始化常用组件 */
        $this->options = $this->widget('Widget_Options');
        $this->user = $this->widget('Widget_User');
    }
    
    /**
     * (non-PHPdoc)
     * @see var/Typecho/Typecho_Widget#__get()
     */
    public function __get($name)
    {
        if(isset($this->_row[$name]))
        {
            return $this->_row[$name];
        }
        else
        {
            $value = NULL;
            
            switch($name)
            {
                case 'publishedPostsNum':
                    $value = $this->db->fetchObject($this->db->select(array('COUNT(cid)' => 'num'))
                    ->from('table.contents')
                    ->where('table.contents.type = ?', 'post'))->num;
                    break;
                case 'myPublishedPostsNum':
                    $value = $this->db->fetchObject($this->db->select(array('COUNT(cid)' => 'num'))
                    ->from('table.contents')
                    ->where('table.contents.type = ?', 'post')
                    ->where('table.contents.author = ?', $this->user->uid))->num;
                    break;
                case 'publishedCommentsNum':
                    $value = $this->db->fetchObject($this->db->select(array('COUNT(coid)' => 'num'))
                    ->from('table.comments')
                    ->where('table.comments.status = ?', 'approved'))->num;
                    break;
                case 'categoriesNum':
                    $value = $this->db->fetchObject($this->db->select(array('COUNT(mid)' => 'num'))
                    ->from('table.metas')
                    ->where('table.metas.type = ?', 'category'))->num;
                    break;
                default:
                    break;
            }
            
            return $value;
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see var/Typecho/Typecho_Widget#__call()
     */
    public function __call($name, $args)
    {
        echo isset($this->{$name}) ? $this->{$name} : NULL;
    }
}
