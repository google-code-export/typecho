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
     * 当前页面
     *
     * @access private
     * @var string
     */
    private $_currentUrl;

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
     * 当前增加项目链接
     * @var string
     */
    public $addLink;

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

        /** 初始化常用组件 */
        $this->options = $this->widget('Widget_Options');
        $this->user = $this->widget('Widget_User');
    }

    /**
     * 执行函数,初始化菜单
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        $this->_parentMenu = array(NULL, _t('控制台'), _t('创建'), _t('管理'), _t('设置'));

        $this->_childMenu =  array(
        array(
            array(_t('登录'), _t('登录到%s', $this->options->title), 'login.php', 'visitor'),
            array(_t('注册'), _t('注册到%s', $this->options->title), 'register.php', 'visitor')
        ),
        array(
            array(_t('概要'), _t('网站概要'), 'index.php', 'subscriber'),
            array(_t('个人设置'), _t('个人设置'), 'profile.php', 'subscriber'),
            array(_t('插件'), _t('插件管理'), 'plugins.php', 'administrator'),
            array(array('Widget_Plugins_Config', 'getMenuTitle'), array('Widget_Plugins_Config', 'getMenuTitle'), 'options-plugin.php?config=', 'administrator', true),
            array(_t('外观'), _t('网站外观'), 'themes.php', 'administrator'),
            array(array('Widget_Themes_Files', 'getMenuTitle'), array('Widget_Themes_Files', 'getMenuTitle'), 'theme-editor.php', 'administrator', true),
            array(array('Widget_Themes_Config', 'getMenuTitle'), array('Widget_Themes_Config', 'getMenuTitle'), 'options-theme.php', 'administrator', true),
            array(_t('升级'), _t('升级程序'), 'upgrade.php', 'administrator', true),
            array(_t('欢迎'), _t('欢迎使用'), 'welcome.php', 'subscriber', true)
        ),
        array(
            array(_t('撰写文章'), _t('撰写新文章'), 'write-post.php', 'contributor'),
            array(array('Widget_Contents_Post_Edit', 'getMenuTitle'), array('Widget_Contents_Post_Edit', 'getMenuTitle'), 'write-post.php?cid=', 'contributor', true),
            array(_t('创建页面'), _t('创建新页面'), 'write-page.php', 'editor'),
            array(array('Widget_Contents_Page_Edit', 'getMenuTitle'), array('Widget_Contents_Page_Edit', 'getMenuTitle'), 'write-page.php?cid=', 'editor', true),
        //    array(_t('上传相片'), _t('上传新相片'), '/admin/edit-photo.php', 'contributor')
        ),
        array(
            array(_t('文章'), _t('管理文章'), 'manage-posts.php', 'contributor', false, Typecho_Common::url('write-post.php', $this->options->adminUrl)),
            array(array('Widget_Contents_Post_Admin', 'getMenuTitle'), array('Widget_Contents_Post_Admin', 'getMenuTitle'), 'manage-posts.php?uid=', 'contributor', true),
            array(_t('独立页面'), _t('管理独立页面'), 'manage-pages.php', 'editor', false, Typecho_Common::url('write-page.php', $this->options->adminUrl)),
            array(_t('评论'), _t('管理评论'), 'manage-comments.php', 'contributor'),
            array(array('Widget_Comments_Admin', 'getMenuTitle'), array('Widget_Comments_Admin', 'getMenuTitle'), 'manage-comments.php?cid=', 'contributor', true),
        //    array(_t('文件'), _t('管理文件'), '/admin/files.php', 'editor'),
            array(_t('标签和分类'), _t('标签和分类'), 'manage-metas.php', 'editor'),
            array(_t('附件'), _t('管理附件'), 'manage-medias.php', 'editor'),
            array(array('Widget_Contents_Attachment_Edit', 'getMenuTitle'), array('Widget_Contents_Attachment_Edit', 'getMenuTitle'), 'media.php?cid=', 'contributor', true),
            array(_t('用户'), _t('管理用户'), 'manage-users.php', 'administrator', false, Typecho_Common::url('user.php', $this->options->adminUrl)),
            array(_t('新增用户'), _t('新增用户'), 'user.php', 'administrator', true),
            array(array('Widget_Users_Edit', 'getMenuTitle'), array('Widget_Users_Edit', 'getMenuTitle'), 'user.php?uid=', 'administrator', true),
        //    array(_t('链接'), _t('管理链接'), '/admin/manage-links.php', 'administrator'),
        //    array(_t('链接分类'), _t('管理链接分类'), '/admin/manage-link-cat.php', 'administrator'),
        ),
        array(
            array(_t('基本'), _t('基本设置'), 'options-general.php', 'administrator'),
            array(_t('评论'), _t('评论设置'), 'options-discussion.php', 'administrator'),
            array(_t('文章'), _t('阅读设置'), 'options-reading.php', 'administrator'),
        //    array(_t('撰写'), _t('撰写习惯设置'), '/admin/option-writing.php', 'contributor'),
        //    array(_t('权限'), _t('权限设置'), '/admin/access.php', 'administrator'),
        //    array(_t('邮件'), _t('邮件设置'), '/admin/mail.php', 'administrator'),
            array(_t('永久链接'), _t('永久链接设置'), 'options-permalink.php', 'administrator'),
        ));

        /** 获取扩展菜单 */
        $panelTable = unserialize($this->options->panelTable);
        $extendingParentMenu = empty($panelTable['parent']) ? array() : $panelTable['parent'];
        $extendingChildMenu = empty($panelTable['child']) ? array() : $panelTable['child'];

        foreach ($extendingParentMenu as $key => $val) {
            $this->_parentMenu[10 + $key] = $val;
        }

        foreach ($extendingChildMenu as $key => $val) {
            $this->_childMenu[$key] = isset($this->_childMenu[$key]) ? $this->_childMenu[$key] : array();
            if (isset($this->_parentMenu[$key])) {
                $this->_childMenu[$key] = array_merge($this->_childMenu[$key], $val);
            }
        }

        $this->_currentUrl = $this->request->makeUriByRequest();
        $childMenu = $this->_childMenu;
        $adminUrl = $this->options->adminUrl;

        foreach ($childMenu as $parentKey => $parentVal) {
            foreach ($parentVal as $childKey => $childVal) {
                $link = Typecho_Common::url($childVal[2], $adminUrl);

                $currentParts = parse_url($this->_currentUrl);
                $parts = parse_url($link);

                /** 精准比对 */
                if ($currentParts['path'] == $parts['path']) {
                    $validate = true;

                    if (!empty($parts['query'])) {
                        parse_str($parts['query'], $out);
                        if (empty($currentParts['query'])) {
                            $validate = false;
                        } else {
                            parse_str($currentParts['query'], $currentOut);

                            if (!empty($out)) {
                                if (!empty($currentOut)) {
                                    foreach ($out as $outKey => $outVal) {
                                        if (!isset($currentOut[$outKey])) {
                                            $validate = false;
                                            break;
                                        }
                                    }
                                } else {
                                    $validate = false;
                                }
                            }
                        }
                    }

                    if ($validate) {
                        $this->_currentParent =  $parentKey;
                        $this->_currentChild =  $childKey;
                    }
                }

                if ('visitor' != $childVal[3] && !$this->user->pass($childVal[3], true)) {
                    unset($this->_childMenu[$parentKey][$childKey]);
                }
            }

            if (0 == count($this->_childMenu[$parentKey])) {
                unset($this->_parentMenu[$parentKey]);
            }
        }

        $level = isset($this->_childMenu[$this->_currentParent][$this->_currentChild][3]) ?
        $this->_childMenu[$this->_currentParent][$this->_currentChild][3] : 'administrator';
        if ('visitor' != $level) {
            $this->user->pass($level);
        }
        
        if (isset($this->_childMenu[$this->_currentParent][$this->_currentChild][5])) {
            $this->addLink = $this->_childMenu[$this->_currentParent][$this->_currentChild][5];
        }

        if (is_array($this->_childMenu[$this->_currentParent][$this->_currentChild][1])) {
            list($widget, $method) = $this->_childMenu[$this->_currentParent][$this->_currentChild][1];
            $this->title = Typecho_Widget::widget($widget)->$method();
        } else {
            $this->title = $this->_childMenu[$this->_currentParent][$this->_currentChild][1];
        }

        array_shift($this->_parentMenu);
        array_shift($this->_childMenu);
        $this->_currentParent --;
    }

    /**
     * 获取当前菜单
     *
     * @access public
     * @return array
     */
    public function getCurrentMenu()
    {
        return $this->_currentParent < 0 ? NULL : $this->_childMenu[$this->_currentParent][$this->_currentChild];
    }

    /**
     * 输出父级菜单
     *
     * @access public
     * @return string
     */
    public function output($class = 'focus', $childClass = 'focus')
    {
        $adminUrl = $this->options->adminUrl;

        foreach ($this->_parentMenu as $key => $title) {
            $current = reset($this->_childMenu[$key]);
            $link = Typecho_Common::url($current[2], $adminUrl);

            echo "<dt" . ($key == $this->_currentParent ? ' class="' . $class . '"' : NULL) . "><a href=\"{$link}\" title=\"{$title}\">{$title}</a></dt>\n";

            echo "<dd><ul>\n";
            foreach ($this->_childMenu[$key] as $inkey => $menu) {
                if (!isset($menu[4]) || !$menu[4] || ($key == $this->_currentParent && $inkey == $this->_currentChild)) {
                    $link = Typecho_Common::url($menu[2], $adminUrl);

                    if (is_array($menu[0])) {
                        list($widget, $method) = $menu[0];
                        $title = $this->widget($widget)->$method();
                    } else {
                        $title = $menu[0];
                    }

                    echo "<li" . ($key == $this->_currentParent && $inkey == $this->_currentChild ? ' class="' . $childClass . '"' : NULL) .
                    "><a href=\"" . ($key == $this->_currentParent && $inkey == $this->_currentChild ? $this->_currentUrl : $link) .
                    "\" title=\"{$title}\">{$title}</a></li>\n";
                }
            }
            echo "</ul></dd>\n";
        }
    }
}
