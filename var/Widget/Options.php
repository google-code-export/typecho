<?php
/**
 * 全局选项
 * 
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 全局选项组件
 * 
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Options extends Typecho_Widget
{
    /**
     * 缓存的插件配置
     * 
     * @access private
     * @var array
     */
    private $_pluginConfig = array();
    
    /**
     * 缓存的个人插件配置
     * 
     * @access private
     * @var array
     */
    private $_personalPluginConfig = array();
    
    /**
     * 数据库对象
     * 
     * @access protected
     * @var Typecho_Db
     */
    protected $db;
    
    /**
     * 构造函数,初始化组件
     * 
     * @access public
     * @param mixed $request request对象
     * @param mixed $response response对象
     * @param mixed $params 参数列表
     * @return void
     */
    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);

        /** 初始化数据库 */
        $this->db = Typecho_Db::get();
    }
    
    /**
     * RSS2.0
     * 
     * @access protected
     * @return string
     */
    protected function ___feedUrl()
    {
        return Typecho_Router::url('feed', array('feed' => '/'), $this->index);
    }
    
    /**
     * RSS1.0
     * 
     * @access protected
     * @return string
     */
    protected function ___feedRssUrl()
    {
        return Typecho_Router::url('feed', array('feed' => '/rss/'), $this->index);
    }
    
    /**
     * ATOM1.O
     * 
     * @access protected
     * @return string
     */
    protected function ___feedAtomUrl()
    {
        return Typecho_Router::url('feed', array('feed' => '/atom/'), $this->index);
    }
    
    /**
     * 评论RSS2.0聚合
     * 
     * @access protected
     * @return string
     */
    protected function ___commentsFeedUrl()
    {
        return Typecho_Router::url('feed', array('feed' => '/comments/'), $this->index);
    }
    
    /**
     * 评论RSS1.0聚合
     * 
     * @access protected
     * @return string
     */
    protected function ___commentsFeedRssUrl()
    {
        return Typecho_Router::url('feed', array('feed' => '/rss/comments/'), $this->index);
    }
    
    /**
     * 评论ATOM1.0聚合
     * 
     * @access protected
     * @return string
     */
    protected function ___commentsFeedAtomUrl()
    {
        return Typecho_Router::url('feed', array('feed' => '/atom/comments/'), $this->index);
    }
    
    /**
     * xmlrpc api地址
     * 
     * @access protected
     * @return string
     */
    protected function ___xmlRpcUrl()
    {
        return Typecho_Router::url('do', array('action' => 'xmlrpc'), $this->index);
    }
    
    /**
     * 获取解析路径前缀
     * 
     * @access protected
     * @return string
     */
    protected function ___index()
    {
        return $this->rewrite ? $this->siteUrl : Typecho_Common::url('index.php', $this->siteUrl);
    }
    
    /**
     * 获取模板路径
     * 
     * @access protected
     * @return string
     */
    protected function ___themeUrl()
    {
        return Typecho_Common::url(__TYPECHO_THEME_DIR__ . '/' . $this->theme, $this->siteUrl);
    }
    
    /**
     * 获取插件路径
     * 
     * @access protected
     * @return string
     */
    protected function ___pluginUrl()
    {
        return Typecho_Common::url(__TYPECHO_PLUGIN_DIR__, $this->siteUrl);
    }
    
    /**
     * 获取后台路径
     * 
     * @access protected
     * @return string
     */
    protected function ___adminUrl()
    {
        return Typecho_Common::url(defined('__TYPECHO_ADMIN_DIR__') ? 
        __TYPECHO_ADMIN_DIR__ : '/admin/', $this->siteUrl);
    }

	/**
	 * 获取资源路径 
	 * 
	 * @access protected
	 * @return void
	 */
	protected function ___resourceUrl()
	{
        return Typecho_Common::url(defined('__TYPECHO_RESOURCE_DIR__') ? 
        __TYPECHO_ADMIN_DIR__ : '/usr/resources/', $this->siteUrl);
	}
    
    /**
     * 获取登录地址
     * 
     * @access protected
     * @return string
     */
    protected function ___loginUrl()
    {
        return Typecho_Common::url('login.php', $this->adminUrl);
    }
    
    /**
     * 获取登录提交地址
     * 
     * @access protected
     * @return string
     */
    protected function ___loginAction()
    {
        return Typecho_Router::url('do', array('action' => 'login', 'widget' => 'Login'), 
        Typecho_Common::url('index.php', $this->siteUrl));
    }
    
    /**
     * 获取注册地址
     * 
     * @access protected
     * @return string
     */
    protected function ___registerUrl()
    {
        return Typecho_Common::url('register.php', $this->adminUrl);
    }
    
    /**
     * 获取登录提交地址
     * 
     * @access protected
     * @return string
     */
    protected function ___registerAction()
    {
        return Typecho_Router::url('do', array('action' => 'register', 'widget' => 'Register'), $this->index);
    }
    
    /**
     * 获取个人档案地址
     * 
     * @access protected
     * @return string
     */
    protected function ___profileUrl()
    {
        return Typecho_Common::url('profile.php', $this->adminUrl);
    }
    
    /**
     * 获取登出地址
     * 
     * @access protected
     * @return string
     */
    protected function ___logoutUrl()
    {
        return Typecho_Common::url('/action/logout', $this->index);
    }
    
    /**
     * 获取系统时区
     * 
     * @access protected
     * @return integer
     */
    protected function ___serverTimezone()
    {
        return Typecho_Date::$serverTimezoneOffset;
    }
    
    /**
     * 获取格林尼治标准时间
     * 
     * @access protected
     * @return integer
     */
    protected function ___gmtTime()
    {
        return Typecho_Date::gmtTime();
    }
    
    /**
     * 获取格式
     * 
     * @access protected
     * @return string
     */
    protected function ___contentType()
    {
        return isset($this->contentType) ? $this->contentType : 'text/html';
    }

    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        $this->db->fetchAll($this->db->select()->from('table.options')
        ->where('user = 0'), array($this, 'push'));
        $this->stack[] = &$this->row;
        
        /** 初始化站点信息 */
        $this->siteUrl = Typecho_Common::url(NULL, $this->siteUrl);
        $this->plugins = unserialize($this->plugins);
        
        /** 自动初始化路由表 */
        $this->routingTable = unserialize($this->routingTable);
        if (!isset($this->routingTable[0])) {
            /** 解析路由并缓存 */
            $parser = new Typecho_Router_Parser($this->routingTable);
            $parsedRoutingTable = $parser->parse();
            $this->routingTable = array_merge(array($parsedRoutingTable), $this->routingTable);
            $this->db->query($this->db->update('table.options')->rows(array('value' => serialize($this->routingTable)))
            ->where('name = ?', 'routingTable'));
        }
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
        $this->row[$value['name']] = $value['value'];
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
        echo Typecho_Common::url($path, $this->siteUrl);
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
        echo Typecho_Common::url($path, $this->index);
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
        echo Typecho_Common::url($path, $this->themeUrl);
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
        echo Typecho_Common::url($path, $this->pluginUrl);
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
        echo Typecho_Common::url($path, $this->adminUrl);
    }
    
    /**
     * 编码输出允许出现在评论中的html标签
     * 
     * @access public
     * @return void
     */
    public function commentsHTMLTagAllowed()
    {
        echo htmlspecialchars($this->commentsHTMLTagAllowed);
    }
    
    /**
     * 获取插件系统参数
     * 
     * @param mixed $pluginName 插件名称
     * @return void
     */
    public function plugin($pluginName)
    {
        if (!isset($this->_pluginConfig[$pluginName])) {
            if (!empty($this->row['plugin:' . $pluginName])
            && false !== ($options = unserialize($this->row['plugin:' . $pluginName]))) {
                $this->_pluginConfig[$pluginName] = new Typecho_Config($options);
            } else {
                throw new Typecho_Plugin_Exception(_t('插件%s的配置信息没有找到', $pluginName), 500);
            }
        }

        return $this->_pluginConfig[$pluginName];
    }
    
    /**
     * 获取个人插件系统参数
     * 
     * @param mixed $pluginName 插件名称
     * @return void
     */
    public function personalPlugin($pluginName)
    {
        if (!isset($this->_personalPluginConfig[$pluginName])) {
            if (!empty($this->row['_plugin:' . $pluginName])
            && false !== ($options = unserialize($this->row['_plugin:' . $pluginName]))) {
                $this->_personalPluginConfig[$pluginName] = new Typecho_Config($options);
            } else {
                throw new Typecho_Plugin_Exception(_t('插件%s的配置信息没有找到', $pluginName), 500);
            }
        }

        return $this->_personalPluginConfig[$pluginName];
    }
}
