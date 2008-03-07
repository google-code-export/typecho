<?php

class Posts extends TypechoWidget
{
    protected $db;

    public function push($value)
    {
        //生成日期
        $value['year'] = date('Y', $value['created'] + $this->registry('Options')->timezone);
        $value['month'] = date('n', $value['created'] + $this->registry('Options')->timezone);
        $value['day'] = date('j', $value['created'] + $this->registry('Options')->timezone);
        
        //生成静态链接
        $value['permalink'] = TypechoRoute::parse('post', $value, $this->registry('Options')->site_url);
        parent::push($value);
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
    
    public function content($more = NULL)
    {
        $content = str_replace('<p><!--more--></p>', '<!--more-->', $this->text);
        list($abstract) = explode('<!--more-->', $content);
        return $abstract . ($more ? '<p class="typecho-more"><a href="' . $this->permalink . '">' . $more . '</a></p>' : NULL);
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
        
        $this->db = TypechoDb::get();
        $rows = $this->db->fetchAll($this->db->sql()
        ->select('table.contents', 'table.contents.cid, table.contents.title, table.contents.created,
        table.contents.text, table.contents.comments_num, table.metas.slug AS category_slug, table.users.screen_name as author')
        ->join('table.metas', 'table.contents.meta = table.metas.mid')
        ->join('table.users', 'table.contents.author = table.users.uid')
        ->where('table.contents.type = ?', 'post')
        ->where('table.metas.type = ?', 'category')
        ->group('table.contents.cid')
        ->order('table.contents.created', 'DESC')
        ->page(empty($_GET['page']) ? 1 : $_GET['page'], $pageSize)
        ->limit($pageSize), array($this, 'push'));
    }
}
