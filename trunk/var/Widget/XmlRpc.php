<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: DoWidget.php 122 2008-04-17 10:04:27Z magike.net $
 */

/**
 * XmlRpc接口
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_XmlRpc extends Widget_Abstract_Contents implements Widget_Interface_Do
{
    /**
     * 重载构造函数，初始化api与调用方法的对应
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->methods = array(
            /** WordPress API */
            'wp.getPage'            => array($this,'wpGetPage'),
            'wp.getPages'            => array($this,'wpGetPages'),
            'wp.newPage'            => array($this,'wpNewPage'),
            'wp.deletePage'            => array($this,'deletePage'),
            'wp.editPage'            => array($this,'wpEditPage'),
            'wp.getPageList'            => array($this,'wpGetPageList'),
            'wp.getAuthors'            => array($this,'wpGetAuthors'),
            'wp.getCategories'        => array($this,'mwGetCategories'),
            'wp.newCategory'            => array($this,'wpNewCategory'),
            'wp.suggestCategories'        => array($this,'wpSuggestCategories'),
            'wp.uploadFile'            => array($this,'mwNewMediaObject'),

            /** Blogger API */
            'blogger.getUsersBlogs' => array($this,'bloggerGetUsersBlogs'),
            'blogger.getUserInfo'    => array($this,'bloggerGetUserInfo'),
            'blogger.getPost'            => array($this,'bloggerGetPost'),
            'blogger.getRecentPosts' => array($this,'bloggerGetRecentPosts'),
            'blogger.getTemplate' => array($this,'bloggerGetTemplate'),
            'blogger.setTemplate' => array($this,'bloggerSetTemplate'),
            'blogger.deletePost' => array($this,'bloggerDeletePost'),

            /** MetaWeblog API (with MT extensions to structs) */
            'metaWeblog.newPost' => array($this,'mwNewPost'),
            'metaWeblog.editPost' => array($this,'mwEditPost'),
            'metaWeblog.getPost' => array($this,'mwGetPost'),
            'metaWeblog.getRecentPosts' => array($this,'mwGetRecentPosts'),
            'metaWeblog.getCategories' => array($this,'mwGetCategories'),
            'metaWeblog.newMediaObject' => array($this,'mwNewMediaObject'),

            /** MetaWeblog API aliases for Blogger API */
            'metaWeblog.deletePost' => array($this,'bloggerDeletePost'),
            'metaWeblog.getTemplate' => array($this,'bloggerGetTemplate'),
            'metaWeblog.setTemplate' => array($this,'bloggerSetTemplate'),
            'metaWeblog.getUsersBlogs' => array($this,'bloggerGetUsersBlogs'),

            /** MovableType API */
            'mt.getCategoryList' => array($this,'mtGetCategoryList'),
            'mt.getRecentPostTitles' => array($this,'mtGetRecentPostTitles'),
            'mt.getPostCategories' => array($this,'mtGetPostCategories'),
            'mt.setPostCategories' => array($this,'mtSetPostCategories'),
            'mt.publishPost' => array($this,'mtPublishPost'),

            /** PingBack */
            'pingback.ping' => array($this,'pingbackPing'),
            'pingback.extensions.getPingbacks' => array($this,'pingbackExtensionsGetPingbacks')
        );
    }

    /**
     * 检查权限 
     * 
     * @access public
     * @return void
     */
    public function checkAccess($userName, $password, $level='contributor')
    {
        /** 实例化user组件 */
        Typecho_Widget::widget('Widget_Abstract_Users')->to($userWidget);
        
        /** 验证用户名和密码 */
        $user = $userWidget->db->fetchRow($this->select()
                ->where('name = ? AND password = ?', $userName, Typecho_Common::hash($password))
                ->limit(1), array($userWidget, 'push'));
        if($user)
        {
            /** 验证权限 */
            if(array_key_exists($group, $userWidget->groups) &&
                    $userWidget->groups[$userWidget->group] <= $userWidget->groups[$level]) 
            {
                return $user;
            }
            else
            {
                $this->error = new IXR_Error(403, '权限不足');
                return false;
            }
        }
        else
        {
            $this->error = new IXR_Error(403, '无法登陆，密码错误');
            return false;
        }
    }
    
    /** about wp xmlrpc api, you can see http://codex.wordpress.org/XML-RPC*/

    /**
     * 获取pageId指定的page
     * 
     * @param int $blogId 
     * @param int $pageId 
     * @param string $userName 
     * @param string $password 
     * @access public
     * @return struct $pageStruct
     */
    public function wpGetPage($blogId, $pageId, $userName, $password)
    {
        /** 检查权限 */
        if(!$this->checkAccess($userName, $password, 'editor')) 
        {
            return $this->error;
        }

        /** 实例化化内容组件 */
        Typecho_Widget::widget('Widget_Abstract_Contents')->to($contents);

        /** 构建基础查询 */
        $select = $contents->select();

        /** 过滤id为$pageId的page */
        $select->where('table.contents.cid = ? AND table.contents.type = ?', $pageId, page);

        /** 提交查询 */
        $page = $contents->$db->fetchRow($select, array($contents, 'push'));
        $page['author_name'] = $contents->author();
        $page['excerpt'] = $contents->excerpt();

        /** 如果这个page存在则输出，否则输出错误 */
        if($page) {
            $pageStruct = array(
                    "dateCreated"   => new IXR_Date($contents->options->timezone + $page['created']),
                    "userid"        => $page['author'],
                    "page_id"       => $page['cid'],
                    /** 此处有疑问 */
                    "page_status"   => $page['type'],
                    "description"   => $page['excerpt'],
                    "title"         => $page['title'],
                    "link"          => $page['permlink'],
                    "permlink"      => $page['permalink'],
                    "categories"    => $page['categories'],
                    "excerpt"       => NULL,
                    "text_more"     => $page['content'],
                    "mt_allow_comments" => $page['allowComment'],
                    "mt_allow_pings" => $page['allowPing'],
                    "wp_slug"       => $page['slug'],
                    "wp_password"   => $page['password'],
                    "wp_author"     => $page['author_name'],
                    "wp_page_parent_id" => 0,
                    "wp_page_parent_title" => NULL,
                    "wp_page_order" => $page['cid'],
                    "wp_author_id"  => $page['author'],
                    "wp_author_display_name" => $page['author_name'],
                    );
            return $pageStruct;
        }
        else
        {
            return IXR_error(404, _t('对不起，不存在此页'));
        }
    }

    /**
     * 获取所有的page 
     * 
     * @param int $blogId 
     * @param string $userName 
     * @param string $password 
     * @access public
     * @return array(contains $pageStruct)
     */
    public function wpGetPages($blogId, $userName, $password)
    {
    }

    /**
     * 撰写一个新page
     * 
     * @param int $blogId 
     * @param string $userName 
     * @param string $password 
     * @param struct $content 
     * @param bool $publish
     * @access public
     * @return void
     */
    public function wpNewPage($blogId, $userName, $password, $content, $publish)
    {
    }

    /**
     * 删除pageId指定的page 
     * 
     * @param int $blogId 
     * @param string $userName 
     * @param string $password 
     * @param int $pageId 
     * @access public
     * @return bool
     */
    public function wpDeletePage($blogId, $userName, $password, $pageId)
    {
    }

    /**
     * 编辑pageId指定的page  
     * 
     * @param int $blogId 
     * @param int $pageId 
     * @param string $userName 
     * @param string $password 
     * @param string $content 
     * @param bool $publish 
     * @access public
     * @return bool
     */
    public function wpEditPage($blogId, $pageId, $userName, $password, $content, $publish)
    {
    }

    /**
     * 获取page列表，没有wpGetPages获得的详细 
     * 
     * @param int $blogId 
     * @param string $userName 
     * @param string $password 
     * @access public
     * @return array
     */
    public function wpGetPageList($blogId, $userName, $password)
    {
    }

    /**
     * 获得一个由blog所有作者的信息组成的数组 
     * 
     * @param int $blogId 
     * @param string $userName 
     * @param string $password 
     * @access public
     * @return struct
     */
    public function wpGetAuthors($blogId, $userName, $password)
    {
    }

    /**
     * 添加一个新的分类 
     * 
     * @param int $blogId 
     * @param string $userName 
     * @param string $password 
     * @param struct $category 
     * @access public
     * @return void
     */
    public function wpNewCategory($blogId, $userName, $password, $category)
    {
    }

    /**
     * 获取一个由给定的string开头的链接组成数组 
     * 
     * @param int $blogId 
     * @param string $userName 
     * @param string $password 
     * @param string $category 
     * @param int $max_results 
     * @access public
     * @return array
     */
    public function wpSuggestCategories($blogId, $userName, $password, $category, $max_results) 
    {
    }

    /**
     * 入口执行方法
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        if (isset($_GET['rsd'])) {
            echo 
<<<EOF
<?xml version="1.0" encoding="{$this->options->charset}"?>
<rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">
    <service>
        <engineName>Typecho</engineName>
        <engineLink>http://www.typecho.org/</engineLink>
        <homePageLink>{$this->options->siteUrl}</homePageLink>
        <apis>
            <api name="WordPress" blogID="1" preferred="true" apiLink="{$this->options->xmlRpcUrl}" />
            <api name="Movable Type" blogID="1" preferred="false" apiLink="{$this->options->xmlRpcUrl}" />
            <api name="MetaWeblog" blogID="1" preferred="false" apiLink="{$this->options->xmlRpcUrl}" />
            <api name="Blogger" blogID="1" preferred="false" apiLink="{$this->options->xmlRpcUrl}" />
        </apis>
    </service>
</rsd>
EOF;
        } else {
            new Ixr_Server($this->methods);
        }
    }
}
