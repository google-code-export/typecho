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
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        $this->db()->fetchAll($this->db()->sql()->select('table.options')
        ->where('`user` = 0'), array($this, 'push'));
        $this->_stack[] = &$this->_row;

        /** 初始化站点信息 */
        $this->charset = __TYPECHO_CHARSET__;
        $this->siteUrl = Typecho_Common::pathToUrl(NULL, $this->siteUrl);
        $this->index = $this->rewrite ? $this->siteUrl : Typecho_Common::pathToUrl('/index.php', $this->siteUrl);
        $this->themeUrl = Typecho_Common::pathToUrl(__TYPECHO_THEME_DIR__ . '/' . $this->theme, $this->siteUrl);
        $this->attachmentUrl = Typecho_Common::pathToUrl(__TYPECHO_ATTACHMENT_DIR__, $this->siteUrl);
        $this->pluginUrl = Typecho_Common::pathToUrl(__TYPECHO_PLUGIN_DIR__, $this->siteUrl);
        $this->gmtTime = time() - idate('Z');
        
        /** 获取插件列表 */
        $this->plugins = unserialize($this->plugins);
        
        /** 初始化Feed地址 */
        $this->feedUrl = Typecho_Router::url('feed', array('feed' => '/'), $this->index);
        $this->feedRssUrl = Typecho_Router::url('feed', array('feed' => '/rss/'), $this->index);
        $this->feedAtomUrl = Typecho_Router::url('feed', array('feed' => '/atom/'), $this->index);
        
        /** 初始化评论Feed地址 */
        $this->commentsFeedUrl = Typecho_Router::url('feed', array('feed' => '/comments/'), $this->index);
        $this->commentsFeedRssUrl = Typecho_Router::url('feed', array('feed' => '/rss/comments/'), $this->index);
        $this->commentsFeedAtomUrl = Typecho_Router::url('feed', array('feed' => '/atom/comments/'), $this->index);

        /** 初始化常用地址 */
        $this->xmlRpcUrl = Typecho_Router::url('do', array('widget' => 'XmlRpc'), $this->index);
        $this->adminUrl = Typecho_Common::pathToUrl(defined('__TYPECHO_ADMIN_DIR__') ? 
        __TYPECHO_ADMIN_DIR__ : '/admin/', $this->siteUrl);
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
        echo Typecho_Common::pathToUrl($path, $this->siteUrl);
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
        echo Typecho_Common::pathToUrl($path, $this->index);
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
        echo Typecho_Common::pathToUrl($path, $this->themeUrl);
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
        echo Typecho_Common::pathToUrl($path, $this->pluginUrl);
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
        echo Typecho_Common::pathToUrl($path, $this->adminUrl);
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
    
    /**
     * 获取插件的配置信息
     * 
     * @access public
     * @param string $pluginName 插件名称
     * @return array
     */
    public function plugin($pluginName)
    {
        if(!isset($this->_pluginConfig[$pluginName]))
        {
            if(!empty($this->_row['plugin:' . $pluginName])
            && false !== ($options = unserialize($this->_row['plugin:' . $pluginName])))
            {
                $this->_pluginConfig[$pluginName] = new Typecho_Config($options);
            }
            else
            {
                throw new Typecho_Plugin_Exception(_t('插件%s的配置信息没有找到', $pluginName), Typecho_Exception::RUNTIME);
            }
        }

        return $this->_pluginConfig[$pluginName];
    }
}
