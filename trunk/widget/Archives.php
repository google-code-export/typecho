<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

class Archives extends DataWidget
{
    public function run($limit = NULL)
    {
        $limit = empty($limit) ? Options::get('page_size') : $limit;
        $page = empty($_GET['page']) ? 0 : $_GET['page'];
        
        $this->db->fetch(
        $this->db->sql()
        ->select('table.posts', 'post_id, post_time')
        ->where('is_draft = 0')
        ->limit($page)
        ->order('post_time', 'DESC'),
        array($this, 'push'));
    }
}

/**
 * 使用方法:
 * Data类Widget本身包含一个可循环的数据容器
 * 调用父类的push方法可以将一行数据压入此容器
 * 输出时
 * 
 * 
 * widget('Archives', 5)->to($post);
 * 
 * while($post->fetch()):
 * 	$post->title();
 * endwhile;
 */
