<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 内容的文章基类
 * 
 * @package Widget
 */
class Posts extends TypechoWidget
{
    protected $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->db = TypechoDb::get();
    }

    public function push($value)
    {
        //生成日期
        $value['year'] = date('Y', $value['created'] + $this->registry('Options')->timezone);
        $value['month'] = date('n', $value['created'] + $this->registry('Options')->timezone);
        $value['day'] = date('j', $value['created'] + $this->registry('Options')->timezone);
        
        //生成静态链接
        $value['permalink'] = TypechoRoute::parse('post', $value, $this->registry('Options')->site_url);
        return parent::push($value);
    }
    
    public function output($tag = 'li', $target = NULL, $class = NULL, $comments = false, $length = 0, $trim = '...')
    {
        foreach($this->_stack as $val)
        {
            echo "<$tag" . (empty($class) ? " class=\"$class\"" : NULL) 
            . "><a" . (empty($target) ? " target=\"$target\"" : NULL) 
            . " href=\"{$val['permalink']}\">" 
            . ($length ? typechoSubStr($val['title'], 0, $length, $trim) : $val['title']) 
            . "</a>" . ($comments ? "<span>{$val['comments_num']}</span>" : NULL) . "</$tag>";
        }
    }
    
    public function date($format)
    {
        echo date($format, $this->created);
    }
    
    public function feedUrl()
    {
        echo TypechoRoute::parse('post_rss', $this->_row, $this->registry('Options')->site_url);
    }
    
    public function commentsPostUrl()
    {
        printf($this->registry('Options')->site_url . '/do.php?mod=CommentsPost&cid=%d', $this->cid);
    }
    
    public function trackbackUrl()
    {
        printf($this->registry('Options')->site_url . '/do.php?mod=Trackback&cid=%d', $this->cid);
    }
    
    public function content($more = NULL)
    {
        $content = str_replace('<p><!--more--></p>', '<!--more-->', $this->text);
        list($abstract) = explode('<!--more-->', $content);
        return typechoFixHtml($abstract) . ($more ? '<p class="typecho-more"><a href="' . $this->permalink . '">' . $more . '</a></p>' : NULL);
    }
    
    public function excerpt($length = 100)
    {
        echo typechoSubStr(typechoStripTags($this->text), 0, $length);
    }
    
    public function comments($string, $tag = '#comments')
    {
        echo '<a href="' . $this->permalink . $tag . '">' . sprintf($string, $this->comments_num) . '</a>';
    }
    
    public function allow()
    {
        $permissions = func_get_args();
        $allow = true;
        
        foreach($permissions as $permission)
        {
            $allow &= ($this->_row['allow_' . $permission] == 'enable');
        }
        
        return $allow;
    }
    
    public function permalink($tag = NULL)
    {
        echo $this->permalink . $tag;
    }
    
    public function category($split = ',', $link = true)
    {
        $categories = 
        $this->db->fetchAll($this->db->sql()
        ->select('table.metas', 'name, slug')
        ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
        ->where('table.relationships.cid = ?', $this->cid)
        ->where('table.metas.type = ?', 'category')
        ->group('table.metas.mid')
        ->order('sort', 'ASC'));
        
        $result = array();
        foreach($categories as $row)
        {
            $result[] = $link ? '<a href="' . TypechoRoute::parse('category', $row, $this->registry('Options')->site_url) . '">'
            . $row['name'] . '</a>' : $row['name'];
        }
        
        echo implode($split, $result);
    }

    public function render($pageSize = NULL)
    {
        $pageSize = empty($pageSize) ? $this->registry('Options')->page_size : $pageSize;
        
        $rows = $this->db->fetchAll($this->db->sql()
        ->select('table.contents', 'table.contents.cid, table.contents.title, table.contents.created,
        table.contents.text, table.contents.comments_num, table.metas.slug AS category_slug, table.users.screen_name as author')
        ->join('table.metas', 'table.contents.meta = table.metas.mid')
        ->join('table.users', 'table.contents.author = table.users.uid')
        ->where('table.contents.type = ?', 'post')
        ->where('table.metas.type = ?', 'category')
        ->where('table.contents.created < ?', $this->registry('Options')->gmt_time)
        ->group('table.contents.cid')
        ->order('table.contents.created', 'DESC')
        ->page(empty($_GET['page']) ? 1 : $_GET['page'], $pageSize), array($this, 'push'));
    }
}
