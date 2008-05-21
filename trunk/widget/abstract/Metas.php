<?php
/**
 * 描述性数据
 * 
 * @author qining
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 描述性数据组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class MetasWidget extends TypechoWidget
{
    /**
     * 分页数目
     *
     * @access protected
     * @var integer
     */
    protected $pageSize;

    /**
     * 当前页
     *
     * @access protected
     * @var integer
     */
    protected $currentPage;

    /**
     * 数据库对象
     *
     * @access protected
     * @var TypechoDb
     */
    protected $db;

    /**
     * 实例化的配置对象
     *
     * @access protected
     * @var TypechoWidget
     */
    protected $options;

    /**
     * 实例化的权限对象
     *
     * @access protected
     * @var TypechoWidget
     */
    protected $access;
    
    /**
     * 公用的选择器
     * 
     * @access protected
     * @var TypechoDbQuery
     */
    protected $selectSql;

    /**
     * 用于计算总数的sql对象
     * 
     * @access protected
     * @var TypechoDbQuery
     */
    protected $countSql;

    /**
     * 过滤器名称
     *
     * @access protected
     * @var string
     */
    protected $filterName;
    
    /**
     * 构造函数,初始化数据库
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        /** 初始化数据库 */
        $this->db = TypechoDb::get();
        
        /** 初始化常用widget */
        $this->options = Typecho::widget('Options');
        $this->access = Typecho::widget('Access');
        
        /** 初始化过滤器名称 */
        $this->filterName = TypechoPlugin::name(__FILE__);
        
        /** 初始化共用选择器 */
        $this->selectSql = $this->db->sql()->select('table.metas');
        
        /** 初始化分页变量 */
        $this->pageSize = empty($pageSize) ? $this->options->pageSize : $pageSize;
        $this->currentPage = TypechoRequest::getParameter('page') ? 1 : TypechoRequest::getParameter('page');
    }
    
    /**
     * 将每行的值压入堆栈
     *
     * @access public
     * @param array $value 每行的值
     * @return array
     */
    public function push(array $value)
    {
        //生成静态链接
        $type = $value['type'];
        $routeExists = isset(TypechoConfig::get('Route')->$type);
        
        $value['permalink'] = $routeExists ? TypechoRoute::parse($type, $value, $this->options->index) : '#';
        
        /** 生成聚合链接 */
        /** RSS 2.0 */
        $value['feedUrl'] = $routeExists ? TypechoRoute::parse('feed', 
        array('feed' => TypechoRoute::parse($type, $value)), $this->options->index) : '#';
        
        /** RSS 0.92 */
        $value['feedRssUrl'] = $routeExists ? TypechoRoute::parse('feed', 
        array('feed' => '/rss' . TypechoRoute::parse($type, $value)), $this->options->index) : '#';
        
        /** ATOM 0.3 */
        $value['feedAtomUrl'] = $routeExists ? TypechoRoute::parse('feed', 
        array('feed' => '/atom' . TypechoRoute::parse($type, $value)), $this->options->index) : '#';

        TypechoPlugin::callFilter($this->filterName, $value);
        return parent::push($value);
    }
    
    /**
     * 设置内容标签
     * 
     * @access public
     * @param integer $cid
     * @param string $tags
     * @return string
     */
    public function setTags($cid, $tags)
    {
        $tags = str_replace(array(' ', '，', ' '), ',', $tags);
        $tags = array_unique(array_map('trim', explode(',', $tags)));

        /** 取出已有tag */
        $existTags = Typecho::arrayFlatten($this->db->fetchAll(
        $this->db->sql()->select('table.metas', 'table.metas.`mid`')
        ->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $cid)
        ->where('table.metas.`type` = ?', 'tag')
        ->group('table.metas.`mid`')), 'mid');
        
        /** 删除已有tag */
        if($existTags)
        {
            foreach($existTags as $tag)
            {
                $this->db->query($this->db->sql()->delete('table.relationships')
                ->where('`cid` = ?', $cid)
                ->where('`mid` = ?', $tag));
                
                $num = $this->db->fetchObject($this->db->sql()
                ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->where('table.relationships.`mid` = ?', $tag))->num;
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $num)
                ->where('`mid` = ?', $tag));
            }
        }
        
        /** 取出插入tag */
        $insertTags = $this->getTags($tags);
        
        /** 插入tag */
        if($insertTags)
        {
            foreach($insertTags as $tag)
            {
                $this->db->query($this->db->sql()->insert('table.relationships')
                ->rows(array(
                    'mid'  =>   $tag,
                    'cid'  =>   $cid
                )));
                
                $num = $this->db->fetchObject($this->db->sql()
                ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->where('table.relationships.`mid` = ?', $tag))->num;
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $num)
                ->where('`mid` = ?', $tag));
            }
        }
    }
    
    /**
     * 根据tag获取ID
     * 
     * @access public
     * @param array $tags
     * @return array
     */
    public function getTags(array $tags)
    {
        $result = array();
        foreach($tags as $tag)
        {
            if(empty($tag))
            {
                continue;
            }
        
            $row = $this->db->fetchRow($this->db->sql()->select('table.metas', '`mid`')
            ->where('`name` = ?', $tag)->limit(1));
            
            if($row)
            {
                $result[] = $row['mid'];
            }
            else
            {
                $result[] = 
                $this->db->query($this->db->sql()->insert('table.metas')
                ->rows(array(
                    'name'  =>  $tag,
                    'slug'  =>  urlencode($tag),
                    'type'  =>  'tag',
                    'count' =>  0,
                    'sort'  =>  0,
                )));
            }
        }
        
        return $result;
    }
    
    /**
     * 设置分类
     * 
     * @access public
     * @param integer $cid
     * @param array $categories
     * @return integer
     */
    public function setCategories($cid, array $categories)
    {
        $categories = array_unique(array_map('trim', $categories));

        /** 取出已有category */
        $existCategories = Typecho::arrayFlatten($this->db->fetchAll(
        $this->db->sql()->select('table.metas', 'table.metas.`mid`')
        ->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $cid)
        ->where('table.metas.`type` = ?', 'category')
        ->group('table.metas.`mid`')), 'mid');
        
        /** 删除已有category */
        if($existCategories)
        {
            foreach($existCategories as $category)
            {
                $this->db->query($this->db->sql()->delete('table.relationships')
                ->where('`cid` = ?', $cid)
                ->where('`mid` = ?', $category));
                
                $num = $this->db->fetchObject($this->db->sql()
                ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->where('table.relationships.`mid` = ?', $category))->num;
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $num)
                ->where('`mid` = ?', $category));
            }
        }
        
        /** 插入category */
        if($categories)
        {
            foreach($categories as $category)
            {
                $this->db->query($this->db->sql()->insert('table.relationships')
                ->rows(array(
                    'mid'  =>   $category,
                    'cid'  =>   $cid
                )));
                
                $num = $this->db->fetchObject($this->db->sql()
                ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->where('table.relationships.`mid` = ?', $category))->num;
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $num)
                ->where('`mid` = ?', $category));
            }
        }
    }
    
    /**
     * 插入数据
     * 
     * @access public
     * @param array $meta 需要插入的数据结构
     * @param boolean $sort 插入时是否排序
     * @return integer
     */
    public function insertMeta(array $meta, $sort = false)
    {
        /** 取出最大的排序 */
        $maxSort = $this->db->fetchObject($this->db->sql()->select('table.metas', 'COUNT(`sort`) AS `maxSort`')
        ->where('`type` = ?', $meta['type']))->maxSort + 1;
        
        /** 构建insert结构 */
        $insert = $this->db->sql()->insert('table.metas')->rows($meta);
        
        if($sort)
        {
            $insert->row('sort', $maxSort);
        }
    
        /** 插入数据 */
        return $this->db->query($insert);
    }
    
    /**
     * 更新数据
     * 
     * @access public
     * @param array $meta 需要更新的数据结构
     * @param integer $mid 数据组件
     * @param string $type 数据类型
     * @return integer
     */
    public function updateMeta(array $meta, $mid, $type)
    {
        /** 更新数据 */
        return $this->db->query($this->db->sql()->update('table.metas')
        ->rows($meta)->where('`mid` = ?', $mid)->where('`type` = ?', $type));
    }
    
    /**
     * 删除数据以及关系
     * 
     * @access public
     * @param integer $mid
     * @return void
     */
    public function deleteMeta($mid)
    {
        $this->db->query($this->db->sql()->delete('table.metas')->where('`mid` = ?', $mid));
        $this->db->query($this->db->sql()->delete('table.relationships')->where('`mid` = ?', $mid));
    }
    
    /**
     * 入口函数
     *
     * @access public
     * @return void
     */
    public function render()
    {
        /** Just Return */
        return;
    }
}
