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
    /**
     * 数据库对象
     * 
     * @access protected
     * @var TypechoDb
     */
    protected $db;
    
    /**
     * 构造函数,初始化数据库
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->db = TypechoDb::get();
    }

    /**
     * 将每行的值压入堆栈
     * 
     * @access public
     * @param array $value
     * @return array
     */
    public function push($value)
    {
        //生成日期
        $value['year'] = date('Y', $value['created'] + $this->registry('Options')->timezone);
        $value['month'] = date('n', $value['created'] + $this->registry('Options')->timezone);
        $value['day'] = date('j', $value['created'] + $this->registry('Options')->timezone);
        
        //生成静态链接
        if('post' == $value['type'])
        {
            $value['permalink'] = TypechoRoute::parse('post', $value, $this->registry('Options')->index);
        }
        else if('page' == $value['type'])
        {
            $value['permalink'] = TypechoRoute::parse('page', $value, $this->registry('Options')->index);
        }
        
        return parent::push($value);
    }

    /**
     * 按行输出函数
     * 
     * @access public
     * @param string $tag 每行的Html标签
     * @param string $target 目标
     * @param string $class css类
     * @param boolean $comments 是否输出评论数
     * @param integer $length 标题截取长度
     * @param string $trim 省略号
     * @return void
     */
    public function output($tag = 'li', $target = NULL, $class = NULL, $comments = false, $length = 0, $trim = '...')
    {
        foreach($this->_stack as $val)
        {
            echo "<$tag" . (empty($class) ? " class=\"$class\"" : NULL) 
            . "><a" . (empty($target) ? " target=\"$target\"" : NULL) 
            . " href=\"{$val['permalink']}\">" 
            . ($length ? typechoSubStr($val['title'], 0, $length, $trim) : $val['title']) 
            . "</a>" . ($comments ? "<span>{$val['commentsNum']}</span>" : NULL) . "</$tag>";
        }
    }
    
    /**
     * 输出文章发布日期
     * 
     * @access public
     * @param string $format 日期格式
     * @return void
     */
    public function date($format)
    {
        echo date($format, $this->created);
    }
    
    /**
     * 输出文章聚合地址
     * 
     * @access public
     * @return void
     */
    public function feedUrl()
    {
        echo TypechoRoute::parse('post_rss', $this->_row, $this->registry('Options')->index);
    }
    
    /**
     * 输出文章评论提交地址
     * 
     * @access public
     * @return void
     */
    public function commentsPostUrl()
    {
        printf(TypechoRoute::parse('do', array('do' => 'CommentsPost'), $this->registry('Options')->index) . '?%d.%d',
        $this->cid, $this->created);
    }
    
    /**
     * 输出文章引用通告地址
     * 
     * @access public
     * @return void
     */
    public function trackbackUrl()
    {
        printf(TypechoRoute::parse('do', array('do' => 'Trackback'), $this->registry('Options')->index) . '?%d.%d',
        $this->cid, $this->created);
    }
    
    /**
     * 输出文章内容
     * 
     * @access public
     * @param string $more 文章截取后缀
     * @return void
     */
    public function content($more = NULL)
    {
        $content = str_replace('<p><!--more--></p>', '<!--more-->', $this->text);
        list($abstract) = explode('<!--more-->', $content);
        echo typechoFixHtml($abstract) . ($more ? '<p class="typecho-more"><a href="' 
        . $this->permalink . '">' . $more . '</a></p>' : NULL);
    }
    
    /**
     * 输出文章摘要
     * 
     * @access public
     * @param integer $length 摘要截取长度
     * @return void
     */
    public function excerpt($length = 100)
    {
        echo typechoSubStr(typechoStripTags($this->text), 0, $length);
    }
    
    /**
     * 输出文章评论数
     * 
     * @access public
     * @param string $string 评论数格式化数据
     * @param string $tag 评论链接锚点
     * @return void
     */
    public function commentsNum($string = 'Comments %d', $tag = '#comments')
    {
        echo '<a href="' . $this->permalink . $tag . '">' . sprintf($string, $this->commentsNum) . '</a>';
    }
    
    /**
     * 获取文章权限
     * 
     * @access public
     * @param string $permission 权限
     * @return unknown
     */
    public function allow()
    {
        $permissions = func_get_args();
        $allow = true;
        
        foreach($permissions as $permission)
        {
            $allow &= ($this->_row['allow' . ucfirst($permission)] == 'enable');
        }
        
        return $allow;
    }
    
    /**
     * 输出文章静态地址
     * 
     * @access public
     * @param string $tag 文章锚点
     * @return void
     */
    public function permalink($tag = NULL)
    {
        echo $this->permalink . $tag;
    }
    
    /**
     * 输出文章分类
     * 
     * @access public
     * @param string $split 多个分类之间分隔符
     * @param boolean $link 是否输出链接
     * @return void
     */
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
            $result[] = $link ? '<a href="' . TypechoRoute::parse('category', $row, $this->registry('Options')->index) . '">'
            . $row['name'] . '</a>' : $row['name'];
        }
        
        echo implode($split, $result);
    }

    /**
     * 入口函数
     * 
     * @access public
     * @param integer $pageSize 每页文章数
     * @return void
     */
    public function render($pageSize = NULL)
    {
        $pageSize = empty($pageSize) ? $this->registry('Options')->page_size : $pageSize;
        
        $rows = $this->db->fetchAll($this->db->sql()
        ->select('table.contents', 'table.contents.cid, table.contents.title, table.contents.created,
        table.contents.text, table.contents.commentsNum, table.metas.slug AS category, table.users.screenName as author')
        ->join('table.metas', 'table.contents.meta = table.metas.mid')
        ->join('table.users', 'table.contents.author = table.users.uid')
        ->where('table.contents.type = ?', 'post')
        ->where('table.metas.type = ?', 'category')
        ->where('table.contents.created < ?', $this->registry('Options')->gmtTime)
        ->group('table.contents.cid')
        ->order('table.contents.created', 'DESC')
        ->page(empty($_GET['page']) ? 1 : $_GET['page'], $pageSize), array($this, 'push'));
    }
}
