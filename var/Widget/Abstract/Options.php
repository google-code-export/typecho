<?php

class Widget_Abstract_Options extends Typecho_Widget_Abstract_Dataset
{
    public function __construct()
    {
        parent::__construct();

        $this->db->fetchAll($this->select()
        ->where('`user` = 0'), array($this, 'push'));
        $this->_stack[] = &$this->_row;

        /** 初始化站点信息 */
        $this->charset = __TYPECHO_CHARSET__;
        $this->siteUrl = Typecho_API::pathToUrl(NULL, $this->siteUrl);
        $this->index = $this->rewrite ? $this->siteUrl : Typecho_API::pathToUrl('/index.php', $this->siteUrl);
        $this->themeUrl = Typecho_API::pathToUrl(__TYPECHO_THEME_DIR__ . '/' . $this->theme, $this->siteUrl);
        $this->attachmentUrl = Typecho_API::pathToUrl(__TYPECHO_ATTACHMENT_DIR__, $this->siteUrl);
        $this->pluginUrl = Typecho_API::pathToUrl(__TYPECHO_PLUGIN_DIR__, $this->siteUrl);
        $this->gmtTime = time() - idate('Z');
        
        /** 初始化Feed地址 */
        $this->feedUrl = Typecho_Router::url('feed', array('feed' => '/'), $this->index);
        $this->feedRssUrl = Typecho_Router::url('feed', array('feed' => '/rss/'), $this->index);
        $this->feedAtomUrl = Typecho_Router::url('feed', array('feed' => '/atom/'), $this->index);

        /** 初始化常用地址 */
        $this->xmlRpcUrl = Typecho_Router::url('do', array('widget' => 'XmlRpc'), $this->index);
        $this->adminUrl = Typecho_API::pathToUrl('/admin/', $this->siteUrl);
    }
    
    /**
     * 获取原始查询对象
     * 
     * @access public
     * @return Typecho_Db_Query
     */
    public function select()
    {
        return $this->db->sql()->select('table.options');
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
        return $this->db->query($this->db->sql()->insert('table.options')->rows($options));
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
        return $this->db->query($condition->update('table.options')->rows($options));
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
        return $this->db->query($condition->delete('table.options'));
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
        return $this->db->fetchObject($condition->select('table.options', 'COUNT(`name`) AS `num`'))->num;
    }

    /**
     * 重载父类push函数,将所有变量值压入堆栈
     *
     * @access public
     * @param array $value 每行的值
     * @return array
     */
    public function push(array $value)
    {
        //将行数据按顺序置位
        $this->_row[$value['name']] = $value['value'];
        return $value;
    }
    
    /**
     * 输出网站路径
     * 
     * @access public
     * @param string $path 子路径
     * @return void
     */
    public function siteUrl($path = NULL)
    {
        echo Typecho_API::pathToUrl($path, $this->siteUrl);
    }
    
    /**
     * 输出解析地址
     * 
     * @access public
     * @param string $path 子路径
     * @return void
     */
    public function index($path = NULL)
    {
        echo Typecho_API::pathToUrl($path, $this->index);
    }
    
    /**
     * 输出模板路径
     * 
     * @access public
     * @param string $path 子路径
     * @return void
     */
    public function themeUrl($path = NULL)
    {
        echo Typecho_API::pathToUrl($path, $this->themeUrl);
    }
    
    /**
     * 输出插件路径
     * 
     * @access public
     * @param string $path 子路径
     * @return void
     */
    public function pluginUrl($path = NULL)
    {
        echo Typecho_API::pathToUrl($path, $this->pluginUrl);
    }
    
    /**
     * 输出后台路径
     * 
     * @access public
     * @param string $path 子路径
     * @return void
     */
    public function adminUrl($path = NULL)
    {
        echo Typecho_API::pathToUrl($path, $this->adminUrl);
    }
    
    /**
     * 归档标题
     * 
     * @access public
     * @param string $format 标题格式
     * @return void
     */
    public function archiveTitle($format = '%s')
    {
        echo sprintf($format, $this->archiveTitle);
    }
}
