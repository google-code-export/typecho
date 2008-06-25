<?php
/**
 * 描述性数据
 * 
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 描述性数据组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Abstract_Metas extends Typecho_Widget_Abstract_Dataset
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
     * 实例化的配置对象
     *
     * @access protected
     * @var Typecho_Widget
     */
    protected $options;

    /**
     * 插件
     *
     * @access protected
     * @var Typecho_Plugin
     */
    protected $plugin;
    
    /**
     * 构造函数,初始化数据库
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        /** 初始化常用widget */
        $this->options = Typecho_API::factory('Widget_Abstract_Options');
        
        /** 初始插件 */
        $this->plugin = _p(__FILE__, 'Filter');
    }
    
    /**
     * 获取原始查询对象
     * 
     * @access public
     * @return Typecho_Db_Query
     */
    public function select()
    {
        return $this->db->sql()->select('table.metas');
    }
    
    /**
     * 插入一条记录
     * 
     * @access public
     * @param array $options 记录插入值
     * @return integer
     */
    public function insert(array $options)
    {
        return $this->db->query($this->db->sql()->insert('table.metas')->rows($options));
    }
    
    /**
     * 更新记录
     * 
     * @access public
     * @param array $options 记录更新值
     * @param Typecho_Db_Query $condition 更新条件
     * @return integer
     */
    public function update(array $options, Typecho_Db_Query $condition)
    {
        return $this->db->query($condition->update('table.metas')->rows($options));
    }
    
    /**
     * 删除记录
     * 
     * @access public
     * @param Typecho_Db_Query $condition 删除条件
     * @return integer
     */
    public function delete(Typecho_Db_Query $condition)
    {
        return $this->db->query($condition->delete('table.metas'));
    }
    
    /**
     * 获取记录总数
     * 
     * @access public
     * @param Typecho_Db_Query $condition 计算条件
     * @return integer
     */
    public function size(Typecho_Db_Query $condition)
    {
        return $this->db->fetchObject($condition->select('table.metas', 'COUNT(`mid`) AS `num`'))->num;
    }
    
    /**
     * 通用过滤器
     * 
     * @access public
     * @param array $value 需要过滤的行数据
     * @return array
     */
    public function filter(array $value)
    {
        //生成静态链接
        $type = $value['type'];
        $routeExists = isset(Typecho_Config::get('Router')->{$type});
        
        $tmpSlug = $value['slug'];
        $value['slug'] = urlencode($value['slug']);
        
        $value['permalink'] = $routeExists ? Typecho_Router::url($type, $value, $this->options->index) : '#';
        
        /** 生成聚合链接 */
        /** RSS 2.0 */
        $value['feedUrl'] = $routeExists ? Typecho_Router::url($type, $value, $this->options->feedUrl) : '#';
        
        /** RSS 1.0 */
        $value['feedRssUrl'] = $routeExists ? Typecho_Router::url($type, $value, $this->options->feedRssUrl) : '#';
        
        /** ATOM 1.0 */
        $value['feedAtomUrl'] = $routeExists ? Typecho_Router::url($type, $value, $this->options->feedAtomUrl) : '#';
        
        $value['slug'] = $tmpSlug;
        
        $value = $this->plugin->filter($value);
        return $value;
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
        $value = $this->filter($value);
        return parent::push($value);
    }
    
    /**
     * 对数据按照sort字段排序
     * 
     * @access public
     * @param array $metas
     * @param string $type
     * @return void
     */
    public function sort(array $metas, $type)
    {
        foreach($metas as $sort => $mid)
        {
            $this->db->query($this->db->sql()->update('table.metas')->row('sort', $sort + 1)
            ->where('`mid` = ?', $mid)->where('`type` = ?', $type));
        }
    }
    
    /**
     * 合并数据
     * 
     * @access public
     * @param integer $mid 数据主键
     * @param string $type 数据类型
     * @param array $metas 需要合并的数据集
     * @return void
     */
    public function merge($mid, $type, array $metas)
    {
        $contents = Typecho_API::arrayFlatten($this->db->fetchAll($this->db->sql()->select('table.relationships', '`cid`')
        ->where('`mid` = ?', $mid)), 'cid');
    
        foreach($metas as $meta)
        {
            if($mid != $meta)
            {
                $existsContents = Typecho_API::arrayFlatten($this->db->fetchAll($this->db->sql()->select('table.relationships', '`cid`')
                ->where('`mid` = ?', $meta)), 'cid');
                
                $where = $this->db->sql()->where('`mid` = ? AND `type` = ?', $meta, $type);
                $this->delete($where);
                $diffContents = array_diff($existsContents, $contents);
                
                foreach($diffContents as $content)
                {
                    $this->db->query($this->db->sql()->insert('table.relationships')
                    ->rows(array('mid' => $mid, 'cid' => $content)));
                }
                
                unset($existsContents);
            }
        }
        
        $num = $this->db->fetchObject($this->db->sql()
        ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
        ->where('table.relationships.`mid` = ?', $mid))->num;
        
        $this->db->query($this->db->sql()->update('table.metas')
        ->row('count', $num)
        ->where('`mid` = ?', $mid));
    }
}
