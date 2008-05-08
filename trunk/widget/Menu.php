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
        $adminUrl = Typecho::widget('Options')->siteUrl;
        foreach($this->_parentMenu as $menu)
        {
            if(Typecho::widget('Access')->pass($menu[2], true))
            {
                $link = Typecho::pathToUrl($menu[1], $adminUrl);
                echo (NULL === $tag ? NULL  : "<{$tag}>")
                . "<a href=\"{$link}\"" . ($menu[1] == $this->_currentParent ? ' class="current"' : NULL) 
                . " title=\"{$menu[0]}\"><span>{$menu[0]}</span></a>"
                . (NULL === $tag ? NULL  : "</{$tag}>");
            }
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
        $adminUrl = Typecho::widget('Options')->siteUrl;
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
            if(Typecho::widget('Access')->pass($menu[2], true))
            {
                $link = Typecho::pathToUrl($menu[1], $adminUrl);
                echo (NULL === $tag ? NULL  : "<{$tag}>")
                . "<a href=\"{$link}\"" . ($menu[1] == $this->_currentChild ? ' class="current-2"' : NULL) 
                . " title=\"{$menu[0]}\">{$menu[0]}</a>"
                . (NULL === $tag ? NULL  : "</{$tag}>");
            }
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
        foreach($this->_parentMenu as $key => $menu)
        {
            if($parent == $menu[1])
            {
                Typecho::widget('Access')->pass($menu[2]);
            }
        }
        
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
                Typecho::widget('Access')->pass($menu[2]);
                Typecho::widget('Options')->title = (empty($title) ? $menu[0] : $title) . ' &raquo; ' . Typecho::widget('Options')->title;
            }
        }
    }
    
    /**
     * 增加一个父级菜单
     * 
     * @param string $title 菜单标题
     * @param string $plugin 插件名
     * @param string $fileName 文件名
     * @return integer
     */
    public function addParent($title, $plugin, $fileName, $group)
    {
        $this->_parentMenu[] = array($title, '/admin/go.php/' . $plugin . '/' . basename($fileName), $group);
        return count($this->_parentMenu) - 1;
    }
    
    /**
     * 增加一个子菜单
     * 
     * @param integer $parent 父级菜单索引
     * @param string $title 菜单标题
     * @param string $plugin 插件名称
     * @param string $fileName 文件名
     * @return integer
     */
    public function addChild($parent, $title, $plugin, $fileName, $group)
    {
        if(isset($this->_childMenu[$parent]))
        {
            $this->_childMenu[$parent][] = array($title, '/admin/go.php/' . $plugin . '/' . basename($fileName), $group);
            return count($this->_childMenu[$parent]) - 1;
        }
        else
        {
            return false;
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
        $this->_parentMenu = array(array(_t('状态面板'), '/admin/index.php', 'subscriber'),
                                   array(_t('创建'), '/admin/edit.php', 'contributor'),
                                   array(_t('管理'), '/admin/post-list.php', 'contributor'),
                                   array(_t('设置'), '/admin/general.php', 'contributor'));
        
        $this->_childMenu =  array(array(
            array(_t('概要'), '/admin/index.php', 'subscriber'),
            array(_t('插件'), '/admin/plugin.php', 'administrator'),
            array(_t('外观'), '/admin/theme.php', 'administrator')
        ),
        array(
            array(_t('撰写文章'), '/admin/edit.php', 'contributor'),
            array(_t('创建页面'), '/admin/edit-page.php', 'editor'),
            array(_t('上传相片'), '/admin/edit-photo.php', 'contributor')
        ),
        array(
            array(_t('文章'), '/admin/post-list.php', 'contributor'),
            array(_t('页面'), '/admin/edit-page.php', 'editor'),
            array(_t('评论'), '/admin/comment-list.php', 'contributor'),
            array(_t('文件'), '/admin/files.php', 'editor'),
            array(_t('分类'), '/admin/manage-cat.php', 'editor'),
            array(_t('标签'), '/admin/manage-tag.php', 'editor'),
            array(_t('用户'), '/admin/users.php', 'administrator'),
            array(_t('链接'), '/admin/manage-links.php', 'administrator'),
            array(_t('链接分类'), '/admin/edit-photo.php', 'administrator'),
        ),
        array(
            array(_t('基本'), '/admin/general.php', 'administrator'),
            array(_t('评论'), '/admin/discussion.php', 'administrator'),
            array(_t('文章'), '/admin/reading.php', 'administrator'),
            array(_t('撰写'), '/admin/writing.php', 'contributor'),
            array(_t('权限'), '/admin/edit-page.php', 'administrator'),
            array(_t('邮件'), '/admin/edit-page.php', 'administrator'),
            array(_t('永久链接'), '/admin/permalink.php', 'administrator'),
        ));
    }
}
