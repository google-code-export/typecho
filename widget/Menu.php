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
 * 后台菜单显示
 *
 * @package Widget
 */
class MenuWidget extends TypechoWidget
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
     * @var string
     */
    private $_currentParent = NULL;
    
    /**
     * 当前子菜单
     * 
     * @access private
     * @var string
     */
    private $_currentChild = NULL;

    /**
     * 输出父级菜单
     * 
     * @access public
     * @return string
     */
    public function outputParent($tag = NULL, $current = NULL)
    {
        $adminURL = widget('Options')->siteURL;
        foreach($this->_parentMenu as $menu)
        {
            $link = Typecho::pathToUrl($menu[1], $adminURL);
            echo (NULL === $tag ? NULL  : "<{$tag}>")
            . "<a href=\"{$link}\"" . ($menu[1] == $this->_currentParent ? ' class="current"' : NULL) 
            . " title=\"{$menu[0]}\"><span>{$menu[0]}</span></a>"
            . (NULL === $tag ? NULL  : "</{$tag}>");
        }
    }
    
    /**
     * 输出子菜单
     * 
     * @access public
     * @return string
     */
    public function outputChild($tag = NULL)
    {
        $adminURL = widget('Options')->siteURL;
        $current = 0;
        
        foreach($this->_parentMenu as $key => $menu)
        {
            if($this->_currentParent == $menu[1])
            {
                $current = $key;
            }
        }
        
        
        foreach($this->_childMenu[$current] as $menu)
        {
            $link = Typecho::pathToUrl($menu[1], $adminURL);
            echo (NULL === $tag ? NULL  : "<{$tag}>")
            . "<a href=\"{$link}\"" . ($menu[1] == $this->_currentChild ? ' class="current-2"' : NULL) 
            . " title=\"{$menu[0]}\">{$menu[0]}</a>"
            . (NULL === $tag ? NULL  : "</{$tag}>");
        }
    }
    
    /**
     * 设定当前父级菜单
     * 
     * @access public
     * @param string $parent
     * @return void
     */
    public function setCurrentParent($parent)
    {
        $this->_currentParent = $parent;
    }
    
    /**
     * 设定当前子菜单
     * 
     * @access public
     * @param string $parent
     * @return void
     */
    public function setCurrentChild($child, $title = NULL)
    {
        $this->_currentChild = $child;
        
        foreach($this->_parentMenu as $key => $menu)
        {
            if($this->_currentParent == $menu[1])
            {
                $current = $key;
            }
        }
        
        
        foreach($this->_childMenu[$current] as $menu)
        {
            if($this->_currentChild == $menu[1])
            {
                widget('Options')->title = (empty($title) ? $menu[0] : $title) . ' &raquo; ' . widget('Options')->title;
            }
        }
    }

    /**
     * 初始化菜单列表
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $this->_parentMenu = array(array(_t('状态面板'), '/admin/index.php'),
                                   array(_t('创建'), '/admin/edit.php'),
                                   array(_t('管理'), '/admin/post-list.php'),
                                   array(_t('设置'), '/admin/general.php'));
                                   
        $hookName = TypechoPlugin::name(__FILE__, 'parent');
        TypechoPlugin::callFilter($hookName, $this->_parentMenu);
        
        $this->_childMenu =  array(array(
            array(_t('概要'), '/admin/index.php'),
            array(_t('插件'), '/admin/plugin.php'),
            array(_t('外观'), '/admin/theme.php')
        ),
        array(
            array(_t('撰写文章'), '/admin/edit.php'),
            array(_t('创建页面'), '/admin/edit-page.php'),
            array(_t('上传相片'), '/admin/edit-photo.php')
        ),
        array(
            array(_t('文章'), '/admin/post-list.php'),
            array(_t('页面'), '/admin/edit-page.php'),
            array(_t('评论'), '/admin/comment-list.php'),
            array(_t('文件'), '/admin/files.php'),
            array(_t('分类'), '/admin/manage-cat.php'),
            array(_t('标签'), '/admin/manage-tag.php'),
            array(_t('用户'), '/admin/users.php'),
            array(_t('链接'), '/admin/manage-links.php'),
            array(_t('链接分类'), '/admin/edit-photo.php'),
        ),
        array(
            array(_t('基本'), '/admin/general.php'),
            array(_t('评论'), '/admin/discussion.php'),
            array(_t('文章'), '/admin/reading.php'),
            array(_t('撰写'), '/admin/writing.php'),
            array(_t('权限'), '/admin/edit-page.php'),
            array(_t('邮件'), '/admin/edit-page.php'),
            array(_t('永久链接'), '/admin/permalink.php'),
        ));
                                   
        $hookName = TypechoPlugin::name(__FILE__, 'child');
        TypechoPlugin::callFilter($hookName, $this->_childMenu);
    }
}
