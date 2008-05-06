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
 * 全局变量管理
 *
 * @package Widget
 */
class OptionsWidget extends TypechoWidget
{
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
     * 按命名空间获取插件列表
     *
     * @access public
     * @param string $namespace 命名空间
     * @return array
     */
    public function plugins($namespace)
    {
        $plugins = unserialize($this->plugins);
        return array_merge(empty($plugins[$namespace]) ? array() : $plugins[$namespace],
        empty($plugins['*']) ? array() : $plugins['*']);
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
        echo Typecho::pathToUrl($path, $this->siteUrl);
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
        echo Typecho::pathToUrl($path, $this->index);
    }
    
    /**
     * 输出模板路径
     * 
     * @access public
     * @param string $path 子路径
     * @return void
     */
    public function templateUrl($path = NULL)
    {
        echo Typecho::pathToUrl($path, $this->templateUrl);
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
        echo Typecho::pathToUrl($path, $this->adminUrl);
    }

    /**
     * 运行入口函数
     *
     * @access public
     * @return void
     */
    public function render()
    {
        $db = TypechoDb::get();

        $db->fetchAll($db->sql()
        ->select('table.options')
        ->where('`user` = 0'), array($this, 'push'));
        $this->_stack[] = $this->_row;

        $this->charset = __TYPECHO_CHARSET__;
        $this->index = $this->rewrite ? $this->siteUrl : Typecho::pathToUrl('/index.php', $this->siteUrl);
        $this->templateUrl = Typecho::pathToUrl($this->templateDirectory . '/' . $this->template, $this->siteUrl);
        $this->attachmentUrl = Typecho::pathToUrl($this->attachmentDirectory, $this->siteUrl);
        $this->gmtTime = time() - intval(date('Z'));
        $this->rssUrl = TypechoRoute::parse('rss', NULL, $this->index);
        $this->adminUrl = Typecho::pathToUrl('/admin/', $this->siteUrl);
    }
}
