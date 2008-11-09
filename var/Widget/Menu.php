<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 后台菜单显示
 *
 * @package Widget
 */
class Widget_Menu extends Typecho_Widget
{
    /**
     * 父菜单列表
     * 
     * @access private
     * @var array
     */
    private $_parentMenu = array();
    
    /**
     * 子菜单列表
     * 
     * @access private
     * @var array
     */
    private $_childMenu = array();
    
    /**
     * 当前父菜单
     * 
     * @access private
     * @var integer
     */
    private $_currentParent = 1;
    
    /**
     * 当前子菜单
     * 
     * @access private
     * @var integer
     */
    private $_currentChild = 0;
    
    /**
     * 全局选项
     * 
     * @access protected
     * @var Widget_Options
     */
    protected $options;

    /**
     * 用户对象
     * 
     * @access protected
     * @var Widget_User
     */
    protected $user;
    
    /**
     * 当前菜单标题
     * @var string
     */
    public $title;
    
    /**
     * 准备函数
     * 
     * @access public
     * @return void
     */
    public function prepare()
    {
        /** 初始化常用组件 */
        $this->options = $this->widget('Widget_Options');
        $this->user = $this->widget('Widget_User');
    }
    
    /**
     * 构造函数,初始化菜单
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        $this->_parentMenu = array(NULL, _t('控制台'), _t('创建'), _t('管理'), _t('设置'));
        
        $this->_childMenu =  array(
        array(
            array(_t('登录'), _t('登录到%s', $this->options->title), '/admin/login.php', 'visitor'),
        ),
        array(
            array(_t('概要'), _t('网站概要'), '/admin/index.php', 'subscriber'),
            array(_t('插件'), _t('插件管理'), '/admin/plugin.php', 'administrator'),
            array(_t('外观'), _t('管理网站外观'), '/admin/theme.php', 'administrator')
        ),
        array(
            array(_t('撰写文章'), _t('撰写新文章'), '/admin/edit.php', 'contributor'),
            array(_t('创建页面'), _t('创建新页面'), '/admin/edit-page.php', 'editor'),
        //    array(_t('上传相片'), _t('上传新相片'), '/admin/edit-photo.php', 'contributor')
        ),
        array(
            array(_t('文章'), _t('管理文章'), '/admin/post-list.php', 'contributor'),
            array(_t('页面'), _t('管理页面'), '/admin/page-list.php', 'editor'),
            array(_t('评论'), _t('管理评论'), '/admin/comment-list.php', 'contributor'),
        //    array(_t('文件'), _t('管理文件'), '/admin/files.php', 'editor'),
            array(_t('分类'), _t('管理分类'), '/admin/manage-cat.php', 'editor'),
            array(_t('标签'), _t('管理标签'), '/admin/manage-tag.php', 'editor'),
            array(_t('用户'), _t('管理用户'), '/admin/users.php', 'administrator'),
            array(_t('链接'), _t('管理链接'), '/admin/manage-links.php', 'administrator'),
        //    array(_t('链接分类'), _t('管理链接分类'), '/admin/manage-link-cat.php', 'administrator'),
        ),
        array(
            array(_t('基本'), _t('基本设置'), '/admin/general.php', 'administrator'),
            array(_t('评论'), _t('评论设置'), '/admin/discussion.php', 'administrator'),
            array(_t('文章'), _t('文章设置'), '/admin/reading.php', 'administrator'),
            array(_t('撰写'), _t('撰写习惯设置'), '/admin/writing.php', 'contributor'),
        //    array(_t('权限'), _t('权限设置'), '/admin/access.php', 'administrator'),
        //    array(_t('邮件'), _t('邮件设置'), '/admin/mail.php', 'administrator'),
        //    array(_t('永久链接'), _t('永久链接设置'), '/admin/permalink.php', 'administrator'),
        ));
        
        $this->_parentMenu = $this->plugin()->parentMenu($this->_parentMenu);
        $this->_childMenu = $this->plugin()->childMenu($this->_childMenu);
        
        $host = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
        $url = 'http://' . $host . $_SERVER['REQUEST_URI'];
        $childMenu = $this->_childMenu;
        $match = 0;
        $adminUrl = $this->options->siteUrl;
        
        foreach($childMenu as $parentKey => $parentVal)
        {
            foreach($parentVal as $childKey => $childVal)
            {
                $link = Typecho_Common::pathToUrl($childVal[2], $adminUrl);
                if(0 === strpos($url, $link) && strlen($link) > $match)
                {
                    $this->_currentParent =  $parentKey;
                    $this->_currentChild =  $childKey;
                }
                
                if('visitor' != $childVal[3] && !$this->user->pass($childVal[3], true))
                {
                    unset($this->_childMenu[$parentKey][$childKey]);
                }
            }
            
            if(0 == count($this->_childMenu[$parentKey]))
            {
                unset($this->_parentMenu[$parentKey]);
            }
        }

        if('visitor' != $this->_childMenu[$this->_currentParent][$this->_currentChild][3])
        {
            $this->user->pass($this->_childMenu[$this->_currentParent][$this->_currentChild][3]);
        }
        
        $this->title = $this->_childMenu[$this->_currentParent][$this->_currentChild][1];
        array_shift($this->_parentMenu);
        array_shift($this->_childMenu);
        $this->_currentParent --;
    }

    /**
     * 输出父级菜单
     * 
     * @access public
     * @return string
     */
    public function output($class = 'focus', $childClass = 'focus')
    {
        $adminUrl = $this->options->siteUrl;
        
        foreach($this->_parentMenu as $key => $title)
        {
            $current = reset($this->_childMenu[$key]);
            $link = Typecho_Common::pathToUrl($current[2], $adminUrl);

            echo "<dt" . ($key == $this->_currentParent ? ' class="' . $class . '"' : NULL) . "><a href=\"{$link}\" title=\"{$title}\">{$title}</a></dt>\r\n";
            
            echo "<dd><ul>\r\n";
            foreach($this->_childMenu[$key] as $inkey => $menu)
            {
                $link = Typecho_Common::pathToUrl($menu[2], $adminUrl);
                echo "<li" . ($key == $this->_currentParent && $inkey == $this->_currentChild ? ' class="' . $childClass . '"' : NULL) . 
                "><a href=\"{$link}\" title=\"{$menu[0]}\">{$menu[0]}</a></li>\r\n";
            }
            echo "</ul></dd>\r\n";
        }
    }
}
