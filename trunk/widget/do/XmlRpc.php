<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入提交基类支持 **/
require_once 'ContentsPost.php';

/** 载入xmlrpc支持库 **/
require_once __TYPECHO_LIB_DIR__ . '/IXR.php';

/** 载入电子邮件支持 */
require_once __TYPECHO_LIB_DIR__ . '/PHPMailer.php';

/**
 * XMLRPC接口支持
 *
 * @package Widget
 */
class XmlRpcWidget extends ContentsPostWidget
{
    /**
     * 检查权限
     * 
     * @access private
     * @param string $userName 用户名
     * @param string $password 密码
     * @param string $group 此操作的最低用户组权限
     * @return IXR_Error
     */
    private function checkAccess($userName, $password, $group = NULL)
    {
        $user = $this->db->fetchRow($this->db->sql()
        ->select('table.users')
        ->where('name = ?', $userName)
        ->limit(1));

        if(!$user)
        {
            return new IXR_Error(403, _t('用户名不存在'));
        }

        if($password != $user['password'])
        {
            return new IXR_Error(403, _t('密码错误'));
        }

        Typecho::widget('Access')->login($user['uid'], $user['name'], $user['group']);

        if($group)
        {
            if(Typecho::widget('Access')->pass($group, true))
            {
                return new IXR_Error(403, _t('无法获得权限'));
            }
        }

        return true;
    }

    /**
     * 获取内容分类列表
     * 
     * @access private
     * @param integer $cid 内容主键
     * @return void
     */
    private function getCategories($cid)
    {
        $categories =
        $this->db->fetchAll($this->db->sql()
        ->select('table.metas', 'name')
        ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
        ->where('table.relationships.cid = ?', $cid)
        ->where('table.metas.type = ?', 'category')
        ->group('table.metas.mid')
        ->order('sort', 'ASC'));

        return typechoArrayFlatten($categories, 'name');
    }

    /**
     * 将每行的值压入堆栈
     *
     * @access public
     * @param array $value 每行的值
     * @return array
     */
    public function push($value)
    {
        //生成日期
        $value['year'] = date('Y', $value['created'] + Typecho::widget('Options')->timezone);
        $value['month'] = date('n', $value['created'] + Typecho::widget('Options')->timezone);
        $value['day'] = date('j', $value['created'] + Typecho::widget('Options')->timezone);

        //生成静态链接
        $value['permalink'] = TypechoRoute::parse($value['type'], $value, Typecho::widget('Options')->index);

        $content = str_replace('<p><!--more--></p>', '<!--more-->', $value['text']);
        list($value['abstract'], $value['more']) = explode('<!--more-->', $content);

        return parent::push($value);
    }

    /**
     * Wordpress获取独立页面API
     * 
     * @access public
     * @param integer $blogId
     * @param integer $pageId 独立页面主键
     * @param string $userName 用户名
     * @param string $password 密码
     * @return array
     */
    public function wpGetPage($blogId, $pageId, $userName, $password)
    {
        if(true === ($check = $this->checkAccess($userName, $password, 'editor')))
        {
            return $check;
        }

        $page = $this->db->fetchRow($this->db->sql()
        ->select('table.contents', 'table.contents.cid, table.contents.title, table.contents.created, table.contents.tags,
        table.contents.text, table.contents.commentsNum, table.contents.author as userId, table.users.screenName as author')
        ->join('table.users', 'table.contents.author = table.users.uid', 'LEFT')
        ->where('table.contents.cid = ?', $pageId)
        ->where('table.contents.type = ?', 'page')
        ->group('table.contents.cid')
        ->limit(1), array($this, 'push'));

        if(!$page)
        {
            return new IXR_Error(404, _t('没有此页面'));
        }

        $pageStruct = array(
        'dateCreated'			=> new IXR_Date($page['created'] + Typecho::widget('Options')->timezone),
        'userid'				=> $page['userId'],
        'page_id'				=> $page['cid'],
        'page_status'			=> 'public',
        'description'			=> $page['abstract'],
        'title'					=> $page['title'],
        'link'					=> $page['permalink'],
        'permaLink'				=> $page['permalink'],
        'categories'			=> $this->getCategories($page['cid']),
        'excerpt'				=> NULL,
        'text_more'				=> $page['more'],
        'mt_allow_comments'		=> $page['post_allow_comment'],
        'mt_allow_pings'		=> $page['post_allow_ping'],
        'mt_keywords'			=> $page['tags'],
        'wp_slug'				=> $page['slug'],
        'wp_password'			=> $page['password'],
        'wp_author'			    => $page['author'],
        'wp_page_parent_id'		=> 0,
        'wp_page_parent_title'	=> NULL,
        'wp_page_order'			=> $page['cid'],
        'wp_author_id'			=> $page['userId'],
        'wp_author_display_name'=> $page['author']
        );

        return $pageStruct;
    }

    public function wpGetPages($blogId, $userName, $password)
    {
        if(true === ($check = $this->checkAccess($userName, $password, 'editor')))
        {
            return $check;
        }

        $pages = $this->db->fetchRow($this->db->sql()
        ->select('table.contents', 'table.contents.cid, table.contents.title, table.contents.created, table.contents.tags,
        table.contents.text, table.contents.commentsNum, table.contents.author as userId, table.users.screenName as author')
        ->join('table.users', 'table.contents.author = table.users.uid', 'LEFT')
        ->where('table.contents.type = ?', 'page')
        ->group('table.contents.cid'), array($this, 'push'));
        $pagesStruct = array();

        foreach($pages as $page)
        {
            $pagesStruct = array(
            'dateCreated'			=> new IXR_Date($page['created'] + Typecho::widget('Options')->timezone),
            'userid'				=> $page['userId'],
            'page_id'				=> $page['cid'],
            'page_status'			=> 'public',
            'description'			=> $page['abstract'],
            'title'					=> $page['title'],
            'link'					=> $page['permalink'],
            'permaLink'				=> $page['permalink'],
            'categories'			=> $this->getCategories($page['cid']),
            'excerpt'				=> NULL,
            'text_more'				=> $page['more'],
            'mt_allow_comments'		=> $page['post_allow_comment'],
            'mt_allow_pings'		=> $page['post_allow_ping'],
            'mt_keywords'			=> $page['tags'],
            'wp_slug'				=> $page['slug'],
            'wp_password'			=> $page['password'],
            'wp_author'			    => $page['author'],
            'wp_page_parent_id'		=> 0,
            'wp_page_parent_title'	=> NULL,
            'wp_page_order'			=> $page['cid'],
            'wp_author_id'			=> $page['userId'],
            'wp_author_display_name'=> $page['author']
            );
        }

        return $pagesStruct;
    }

    public function wpNewPage($blogId, $userName, $password, $page, $publish)
    {
        if(true === ($check = $this->checkAccess($userName, $password, 'editor')))
        {
            return $check;
        }

        $page['post_type'] = 'page';
        return $this->mwNewPost($blogId, $userName, $password, $page, $publish);
    }

    public function wpDeletePage($blogId, $userName, $password, $pageId)
    {
        if(true === ($check = $this->checkAccess($userName, $password, 'editor')))
        {
            return $check;
        }

        $this->db->query($this->db->sql()
        ->delete('table.contents')
        ->where('cid = ?', $pageId));

        return true;
    }

    public function wpEditPage($blogId, $pageId, $userName, $password, $content, $publish)
    {
        if(true === ($check = $this->checkAccess($userName, $password, 'editor')))
        {
            return $check;
        }

        $content['post_type'] = 'page';
        return $this->mwEditPost($pageId, $userName, $password, $content, $publish);
    }

    public function wpGetPageList($blogId, $userName, $password)
    {
        if(true === ($check = $this->checkAccess($userName, $password, 'editor')))
        {
            return $check;
        }

        $pages = $this->db->fetchRow($this->db->sql()
        ->select('table.contents', 'table.contents.cid, table.contents.title, table.contents.created,
        table.contents.tags, table.contents.text, table.contents.commentsNum')
        ->where('table.contents.type = ?', 'page'));
        $pagesStruct = array();

        foreach($pages as $page)
        {
            $pagesStruct[] = array(
                "dateCreated"	    => new IXR_Date($page['created'] + Typecho::widget('Options')->timezone),
                "page_id"		    => $page["cid"],
                "page_title"		=> $page["title"],
                "page_parent_id"	=> 0
            );
        }

        return $pagesStruct;
    }

    public function wpGetAuthors($blogId, $userName, $password)
    {
        if(true === ($check = $this->checkAccess($userName, $password)))
        {
            return $check;
        }

        $struct = array('user_id'      => Typecho::widget('Access')->uid,
                        'user_login'   => Typecho::widget('Access')->name,
                        'display_name' => Typecho::widget('Access')->screenName,
                        'user_email'   => Typecho::widget('Access')->mail,
                        'meta_value'   => '');

        return array($struct);
    }

    public function wpNewCategory($blogId, $userName, $password, $category)
    {
        if(true === ($check = $this->checkAccess($userName, $password, 'editor')))
        {
            return $check;
        }

        $maxCategory = $this->db->fetchRow($this->db->sql()
        ->select('table.metas', 'max(sort) AS maxSort')
        ->where('type = ?', 'category'));

        $maxCategory = empty($maxCategory['maxSort']) ? 0 : $maxCategory['maxSort'];

        $input['name'] = $category['name'];
        $input['slug'] = empty($category['slug']) ?
        typechoSlugName($category['slug'], $this->getAutoIncrement('metas'))
        : typechoSlugName($category['name'], $this->getAutoIncrement('metas'));
        $input['description'] = empty($category['description']) ? $category['description'] : $category['name'];
        $input['count'] = 0;
        $input['sort'] = $maxCategory + 1;

        $categoryId = $this->db->query($this->db->sql()
        ->insert('table.metas')
        ->rows($input));

        if(!$categoryId)
        {
            return new IXR_Error(500, _t('分类增加错误'));
        }

        return $categoryId;
    }

    public function wpSuggestCategories($blogId, $userName, $password, $category, $maxResults = 1)
    {
        if(true === ($check = $this->checkAccess($userName, $password)))
        {
            return $check;
        }

        return $this->db->fetchRow($this->db->sql()
        ->select('table.metas')
        ->where('type = ?', 'category')
        ->where('name LIKE ?', $category . '%')
        ->order('sort')
        ->limit($maxResults));
    }

    public function bloggerGetUsersBlogs($blogId, $userName, $password)
    {
        if(true === ($check = $this->checkAccess($userName, $password)))
        {
            return $check;
        }

        $struct = array(
            'isAdmin' => true,
            'url'	  => Typecho::widget('Options')->siteUrl,
            'blogid'  => Typecho::widget('Access')->uid,
            'blogName'=> Typecho::widget('Options')->title
        );

        return array($struct);
    }

    public function bloggerGetUserInfo($blogId, $userName, $password)
    {
        if(true === ($check = $this->checkAccess($userName, $password)))
        {
            return $check;
        }

        return array('nickname'  => Typecho::widget('Access')->screenName,
                     'userid'    => Typecho::widget('Access')->uid,
                     'url'       => Typecho::widget('Access')->url,
                     'email'     => Typecho::widget('Access')->mail,
                     'lastname'  => Typecho::widget('Access')->name,
                     'firstname' => Typecho::widget('Access')->name);
    }

    public function bloggerGetPost($blogId, $postId, $userName, $password)
    {
        if(true === ($check = $this->checkAccess($userName, $password)))
        {
            return $check;
        }

        $post = $this->db->fetchRow($this->db->sql()
        ->select('table.contents', 'table.contents.cid, table.contents.title, table.contents.created, table.contents.tags,
        table.contents.text, table.contents.commentsNum, table.contents.author as userId, table.users.screenName as author')
        ->join('table.users', 'table.contents.author = table.users.uid', 'LEFT')
        ->where('table.contents.cid = ?', $postId)
        ->where('table.contents.type = ?', 'post')
        ->group('table.contents.cid')
        ->limit(1), array($this, 'push'));

        if(!$post)
        {
            return new IXR_Error(500, _t('此文章不存在'));
        }

        if(!$this->havePostPermission($post['userId']))
        {
            return new IXR_Error(403, _t('没有获取此文章的权限'));
        }

        $content  = '<title>' . $post['title'] . '</title>';
        if($categories = $this->getCategories($post['cid']))
        {
            foreach($categories as $category)
            {
                $content .= '<category>' . $category . '</category>';
            }
        }
        $content .= stripslashes($post['text']);

        $struct = array('userid'      => $post['userId'],
                        'dateCreated' => new IXR_Date(date('Ymd\TH:i:s', $post['created'] + Typecho::widget('Options')->timezone)),
                        'content'     => $content,
                        'postid'      => $post['cid']);

        return $struct;
    }

    public function bloggerGetRecentPosts($blogId, $userName, $password, $postsNum)
    {
        if(true === ($check = $this->checkAccess($userName, $password)))
        {
            return $check;
        }

        $sql = $this->db->sql()
        ->select('table.contents', 'table.contents.cid, table.contents.title, table.contents.created, table.contents.tags,
        table.contents.text, table.contents.commentsNum, table.contents.author as userId, table.users.screenName as author')
        ->join('table.users', 'table.contents.author = table.users.uid', 'LEFT')
        ->where('table.contents.type = ?', 'post')
        ->group('table.contents.cid')
        ->order('table.contents.created', 'DESC')
        ->limit(1);

        if(!Typecho::widget('Access')->pass('editor', true))
        {
            $sql->where('table.contents.author = ?', Typecho::widget('Access')->uid);
        }

        $posts = $this->db->fetchAll($sql, array($this, 'push'));

        if(!$posts)
        {
            return new IXR_Error(500, _t('没有找到任何文章'));
        }

        $structs = array();
        foreach($posts as $post)
        {
            $content  = '<title>' . $post['title'] . '</title>';
            if($categories = $this->getCategories($post['cid']))
            {
                foreach($categories as $category)
                {
                    $content .= '<category>' . $category . '</category>';
                }
            }
            $content .= stripslashes($post['text']);

            $structs[] = array('userid'      => $post['userId'],
                               'dateCreated' => new IXR_Date(date('Ymd\TH:i:s', $post['created'] + Typecho::widget('Options')->timezone)),
                               'content'     => $content,
                               'postid'      => $post['cid']);
        }

        return $structs;
    }

    public function bloggerGetTemplate($blogId, $userName, $password, $template)
    {
        if(true === ($check = $this->checkAccess($userName, $password, 'administrator')))
        {
            return $check;
        }

        return NULL;
    }

    public function bloggerSetTemplate($blogId, $userName, $password, $content, $template)
    {
        if(true === ($check = $this->checkAccess($userName, $password, 'administrator')))
        {
            return $check;
        }

        return true;
    }
    
    public function typechoSendMail($blogId, $userName, $password, $subject, $body, $reply, array $send)
    {
        if(true === ($check = $this->checkAccess($userName, $password, 'administrator')))
        {
            return $check;
        }
        
        
    }

    public function render()
    {
        $methods = array(
        // WordPress API
        'wp.getPage'			=> array($this, 'wpGetPage'),
        'wp.getPages'			=> array($this, 'wpGetPages'),
        'wp.newPage'			=> array($this, 'wpNewPage'),
        'wp.deletePage'			=> array($this, 'wpDeletePage'),
        'wp.editPage'			=> array($this, 'wpEditPage'),
        'wp.getPageList'		=> array($this, 'wpGetPageList'),
        'wp.getAuthors'			=> array($this, 'wpGetAuthors'),
        'wp.getCategories'		=> array($this, 'mwGetCategories'),
        'wp.newCategory'		=> array($this, 'wpNewCategory'),
        'wp.suggestCategories'	=> array($this, 'wpSuggestCategories'),
        'wp.uploadFile'			=> array($this, 'mwNewMediaObject'),

        // Blogger API
        'blogger.getUsersBlogs'    => array($this, 'bloggerGetUsersBlogs'),
        'blogger.getUserInfo'      => array($this, 'bloggerGetUserInfo'),
        'blogger.getPost' 	       => array($this, 'bloggerGetPost'),
        'blogger.getRecentPosts'   => array($this, 'bloggerGetRecentPosts'),
        'blogger.getTemplate'      => array($this, 'bloggerGetTemplate'),
        'blogger.setTemplate'      => array($this, 'bloggerSetTemplate'),
        'blogger.deletePost'       => array($this, 'bloggerDeletePost'),

        // MetaWeblog API
        'metaWeblog.newPost'        => array($this, 'mwNewPost'),
        'metaWeblog.editPost'       => array($this, 'mwEditPost'),
        'metaWeblog.getPost'        => array($this, 'mwGetPost'),
        'metaWeblog.getRecentPosts' => array($this, 'mwGetRecentPosts'),
        'metaWeblog.getCategories'  => array($this, 'mwGetCategories'),
        'metaWeblog.newMediaObject' => array($this, 'mwNewMediaObject'),

        // MetaWeblog API
        'metaWeblog.deletePost'    => array($this, 'bloggerDeletePost'),
        'metaWeblog.getTemplate'   => array($this, 'bloggerGetTemplate'),
        'metaWeblog.setTemplate'   => array($this, 'bloggerSetTemplate'),
        'metaWeblog.getUsersBlogs' => array($this, 'bloggerGetUsersBlogs'),

        // MovableType API
        'mt.getCategoryList'     => array($this, 'mtGetCategoryList'),
        'mt.getRecentPostTitles' => array($this, 'mtGetRecentPostTitles'),
        'mt.getPostCategories'   => array($this, 'mtGetPostCategories'),
        'mt.setPostCategories'   => array($this, 'mtSetPostCategories'),
        'mt.supportedMethods'    => array($this, 'mtSupportedMethods'),
        'mt.supportedTextFilters'=> array($this, 'mtSupportedTextFilters'),
        'mt.publishPost'         => array($this, 'mtPublishPost'),

        // PingBack
        'pingback.ping'                    => array($this, 'pingbackPing'),
        'pingback.extensions.getPingbacks' => array($this, 'pingbackExtensionsGetPingbacks'),
        
        //Typecho API
        'typecho.sendMail'                 => array($this, 'typechoSendMail'),
        'typecho.trackback'                => array($this, 'typechoTrackback'),
        'typecho.pingback'                 => array($this, 'typechoPingback')
        );

        if(!isset($GLOBALS['HTTP_RAW_POST_DATA']))
        {
            $GLOBALS['HTTP_RAW_POST_DATA'] = file_get_contents("php://input");
        }
        if(isset($GLOBALS['HTTP_RAW_POST_DATA']))
        {
            $GLOBALS['HTTP_RAW_POST_DATA'] = trim($GLOBALS['HTTP_RAW_POST_DATA']);
        }

        if(isset($_GET['rsd']))
        {
            echo '<?xml version="1.0" encoding="';
            Typecho::widget('Options')->charset();
            echo '"?>
            <rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">
            <service>
            <engineName>Typecho</engineName>
            <engineLink>http://www.typecho.org/</engineLink>
            <homePageLink>';
            Typecho::widget('Options')->siteUrl();
            echo '</homePageLink>
            <apis>
            <api name="WordPress" blogID="1" preferred="true" apiLink="';
            Typecho::widget('Options')->index('XmlRpc.do');
            echo '" />
            <api name="Movable Type" blogID="1" preferred="false" apiLink="';
            Typecho::widget('Options')->index('XmlRpc.do');
            echo '" />
            <api name="MetaWeblog" blogID="1" preferred="false" apiLink="';
            Typecho::widget('Options')->index('XmlRpc.do');
            echo '" />
            <api name="Blogger" blogID="1" preferred="false" apiLink="';
            Typecho::widget('Options')->index('XmlRpc.do');
            echo '" />
            </apis>
            </service>
            </rsd>';
        }
        else
        {
            new IXR_Server($methods);
        }
    }
}
