<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * XmlRpc接口
 *
 * @author blankyao
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_XmlRpc extends Widget_Abstract_Contents implements Widget_Interface_Do
{
    /**
     * 当前错误
     * 
     * @access private
     * @var IXR_Error
     */
    private $error;

    /**
     * 如果这里没有重载, 每次都会被默认执行
     * 
     * @access public
     * @param boolen $run 是否执行
     * @return void
     */
    public function execute($run = false)
    {
        if ($run) {
            parent::execute();
        }
    }

    /**
     * 检查权限
     *
     * @access public
     * @return void
     */
    public function checkAccess($userName, $password, $level = 'contributor')
    {
        /** 验证用户名和密码 */
        $select = $this->db->select()->from('table.users')->where('name = ?', $userName)->limit(1);
        $user = $this->db->fetchRow($select, array($this->user, push));
        if ($user && Typecho_Common::hashValidate($password, $user['password'])) {
            /** 登录操作 */
            /** 登录后用$this->user即可调用当前用户 */
            $this->user->login($user['uid']);
            
            /** 验证权限 */
            if ($this->user->pass($level, true)) {
                return true;
            } else {
                $this->error = new IXR_Error(403, _t('权限不足'));
                return false;
            }
        } else {
            $this->error = new IXR_Error(403, _t('无法登陆, 密码错误'));
            return false;
        }
    }

    private function getPostExtended($content)
    {
        $post = Typecho_Common::fixHtml($content);
        $post = explode('<!--more-->', $content, 2);
        return array($post[0], isset($post[1]) ? $post[1] : NULL);
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
        if(!$this->checkAccess($userName, $password)) {
            return $this->error;
        }

        /** 获取页面 */
        try {
            /** 由于Widget_Contents_Page_Edit是从request中获取参数, 因此我们需要强行设置flush一下request */
            /** widget方法的第三个参数可以指定强行转换传入此widget的request参数 */
            /** 此组件会进行复杂的权限检测 */
            $page = $this->widget('Widget_Contents_Page_Edit', NULL, "cid={$pageId}");
        } catch (Typecho_Widget_Exception $e) {
            /** 截获可能会抛出的异常(参见 Widget_Contents_Page_Edit 的 execute 方法) */
            return new IXR_Error($e->getCode(), $e->getMessage());
        }

        /** 取得文章作者的名字*/
        $page['author_name'] = $this->author->name;
        $page['author_screen_name'] = $this->author->screenName;
        /** 对文章内容做截取处理，以获得description和text_more*/
        list($excerpt, $more) = $this->getPostExtended($page->text);

        $pageStruct = array(
                'dateCreated'   => new IXR_Date($this->options->timezone + $page->created),
                'userid'        => $page->authorId,
                'page_id'       => $page->cid,
                'page_status'   => $page->status,
                'description'   => $excerpt,
                'title'         => $page->title,
                'link'          => $page->permalink,
                'permalink'     => $page->permalink,
                'categories'    => $page->categories,
                'excerpt'       => $excerpt,
                'text_more'     => $more,
                'mt_allow_comments' => $page->allowComment,
                'mt_allow_pings' => $page->allowPing,                          
                'wp_slug'        => $page->slug,
                'wp_password'   => $page->password,
                'wp_author'     => $page->author->name,
                'wp_page_parent_id' => 0,
                'wp_page_parent_title' => NULL,
                'wp_page_order' => $page->meta,     //meta是描述字段, 在page时表示顺序
                'wp_author_id'  => $page->authorId,
                'wp_author_display_name' => $page->author->screenName,
            );
        
        return $pageStruct;
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
        if (!$this->checkAccess($userName, $password)) {
            return $this->error;
        }

        /** 过滤type为page的contents */
        /** 同样需要flush一下, 需要取出所有status的页面 */
        $pages = $this->widget('Widget_Contents_Page_Admin', NULL, 'status=all');

        /** 初始化要返回的数据结构 */
        $pageStructs = array();

        while ($pages->next()) {
            /** 对文章内容做截取处理，以获得description和text_more*/
            list($excerpt, $more) = $this->getPostExtended($pages->text);
            $pageStructs[] = array(
                'dateCreated'   => new IXR_Date($this->options->timezone + $pages->created),
                'userid'        => $pages->authorId,
                'page_id'       => $pages->cid,
                /** todo:此处有疑问 */
                'page_status'   => $pages->status,
                'description'   => $excerpt,
                'title'         => $pages->title,
                'link'          => $pages->permalink,
                'permalink'     => $pages->permalink,
                'categories'    => $pages->categories,
                'excerpt'       => $excerpt,
                'text_more'     => $more,
                'mt_allow_comments' => $pages->allowComment,
                'mt_allow_pings' => $pages->allowPing,                          
                'wp_slug'        => $pages->slug,
                'wp_password'   => $pages->password,
                'wp_author'     => $pages->author->name,
                'wp_page_parent_id' => 0,
                'wp_page_parent_title' => NULL,
                'wp_page_order' => $pages->meta,     //meta是描述字段, 在page时表示顺序
                'wp_author_id'  => $pages->authorId,
                'wp_author_display_name' => $pages->author->screenName,
            );
        }
        
        return $pageStructs;
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
        if(!$this->checkAccess($userName, $password, 'editor'))
        {
            return $this->error;
        }
        $content['post_type'] = 'page';
        $this->mwNewPost($blogId, $userName, $password, $content, $publish);
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
        if(!$this->checkAccess($userName, $password, 'editor'))
        {
            return $this->error;
        }
        $condition = $this->db->sql()->where('cid = ?', $pageId);
        if($this->postIsWriteable($condition) && $this->delete($condition))
        {
            $this->db->query($this->db->sql()->delete('table.comments')
                    ->where('cid = ?', $pageId));
        }
        else
        {
            return(new IXR_Error(500,_t("无法删除页面")));
        }
        return true;
    }

    /**
     * 编辑pageId指定的page
     *
     * @param int $blogId
     * @param int $pageId
     * @param string $userName
     * @param string $password
     * @param struct $content
     * @param bool $publish
     * @access public
     * @return bool
     */
    public function wpEditPage($blogId, $pageId, $userName, $password, $content, $publish)
    {
        $content['type'] = 'page';
        $this->mwEditPost($blogId, $pageId, $userName, $password, $content, $publish);
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
        if(!$this->checkAccess($userName, $password, 'editor'))
        {
            return ($this->error);
        }
        $pages = $this->widget('Widget_Contents_Page_Admin', NULL, 'status=all');
        /**初始化*/
        $pageStructs = array();
        
        while($pages->next())
        {
            $pageStructs[] = array(
                    'dateCreated'   => new IXR_Date($this->options->timezone + $pages->created),
                    'page_id'       => $pages->cid,
                    'page_title'    => $pages->title,
                    'page_parent_id'=> 0,
                    );
        }
        
        return $pageStructs;
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
        if(!$this->checkAccess($userName, $password, 'editor'))
        {
            return ($this->error);
        }

        /** 构建查询*/
        $select = $this->db->select('table.users.uid', 'table.users.name', 'table.users.screenName')->from('table.users');
        $this->db->fetchAll($select, array($this, 'push'));

        $authors = array();
        while($this->next())
        {
            $authors[] = array(
                    'user_id'       => $this->uid,
                    'user_login'    => $this->name,
                    'display_name'  => $this->screenName,
                    );
        }
        return $authors;
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
        if(!$this->checkAccess($userName, $password))
        {
            return ($this->error);
        }

        /** 开始接受数据 */
        $option['name'] = $category['name'];
        $option['slug'] = Typecho_Common::slugName(empty($category['slug']) ? $category['name'] : $category['slug']);
        $option['type'] = 'category';
        $option['description'] = isset($category['description']) ? $category['description'] : $category['name'];

        /** 初始化meta widget，然后插入*/
        $meta = $this->widget('Widget_Abstract_Metas');
        if(!$meta->insert($option)) {
            return new IXR_Error(500, _t('对不起,提交文章时发生错误.'));
        }
        return true;
    }

    /**
     * 获取由给定的string开头的链接组成的数组
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
        if(!$this->checkAccess($userName, $password))
        {
            return ($this->error);
        }

        $meta = $this->widget('Widget_Abstract_Metas');

        /** 构造出查询语句并且查询*/
        $key = Typecho_Common::filterSearchQuery($category);
        $key = '%' . $key . '%';
        $select = $meta->select()->where('table.metas.type = ? AND (table.metas.name LIKE ? OR slug LIKE ?)', 'category', $key, $key);
        $this->db->fetchAll($select, array($this, 'push'));
        /** 初始化categorise数组*/
        $categories = array();
        while($this->next())
        {
            $categories[] = array(
                    'category_id'   => $this->mid,
                    'category_name' => $this->name,
                    );
        }
        return $categories;
    }

    /**about MetaWeblog API, you can see http://www.xmlrpc.com/metaWeblogApi*/
    /**
     * MetaWeblog API
     *
     * @param int $blogId
     * @param string $userName
     * @param string $password
     * @param struct $content
     * @param bool $publish
     * @access public
     * @return int
     */
    public function mwNewPost($blogId, $userName, $password, $content, $publish)
    {
        /** 检查权限*/
        if(!$this->checkAccess($userName, $password))
        {
            return $this->error;
        }

        /** 取得content内容 */
        $input = array();
        $input['title'] = trim($content['title']) == NULL ? _t('未命名文档') : $content['title'];
        $input['slug'] = $content['slug'];
        //todo:将IXR_Date转换为时间戳
        //$input['created'] = $content['dateCreated'];
        $input['text'] = isset($content['mt_text_more']) && $content['mt_text_more'] ? $content['description']."\n<!--more-->\n".$content['mt_text_more'] : $content['description'];
        $input['authorId'] = ('edit' != $content['do'] ? $this->user->uid : NULL);
        $input['categories'] = $content['categories'];
        $input['type'] = $content['post_type'];
        $input['status'] = true == $publish ? 'publish' : 'draft';
        if(!$this->checkAccess($userName, $password, 'editor'))
        {
            $input['status'] = 'waiting';
        }
        $input['password'] = isset($content["wp_password"]) ? $content["wp_password"] : NULL;

        /** for $input['allowComment']*/
        if(isset($content["mt_allow_comments"]))
        {
            if(!is_numeric($content["mt_allow_comments"]))
            {
                switch($content["mt_allow_comments"])
                {
                    case "closed":
                        $input["allowComment"] = 0;
                        break;
                    case "open":
                        $input["allowComment"] = 1;
                        break;
                    default:
                        $input["allowComment"] = $this->options->defaultAllowComment;
                        break;
                }
            }
            else
            {
                switch((int) $content["mt_allow_comments"])
                {
                    case 0:
                        $input["allowComment"] = 0;
                        break;
                    case 1:
                        $input["allowComment"] = 1;
                        break;
                    default:
                        $input["allowComment"] = $this->options->defaultAllowComment;
                        break;
                }
            }
        }
        else
        {
            $input["allowComment"] = $this->options->defaultAllowComment;
        }

        /** for $input[allowPing]*/
        if(isset($content["mt_allow_pings"]))
        {
            if(!is_numeric($content["mt_allow_pings"]))
            {
                switch($content["mt_allow_pings"])
                {
                    case "closed":
                        $input["allowPing"] = 0;
                        break;
                    case "open":
                        $input["allowPing"] = 1;
                        break;
                    default:
                        $input["allowPing"] = $this->options->defaultAllowPing;
                        break;
                }
            }
            else
            {
                switch((int) $content["mt_allow_pings"])
                {
                    case 0:
                        $input["allowPing"] = 0;
                        break;
                    case 1:
                        $input["allowPing"] = 1;
                        break;
                    default:
                        $input["allowPing"] = $this->options->defaultAllowPing;
                        break;
                }
            }
        }
        else
        {
            $input["allowPing"] = $this->options->defaultAllowPing;
        }
        $input['allowFeed'] = $this->options->defaultAllowFeed;
        if($content['do'] == 'edit')
        {
            /** 执行修改动作*/
            $insertId = $this->update($input, $this->db->sql()->where('cid = ?',
                        $content['post_id']));
        }
        else
        {
            /** 执行插入*/
            if(!$insertId = $this->insert($input))
            {
                return new IXR_Error(500, _t('对不起,该文章不能更新.'));
            }
        }
        if($insertId && 'page' != $input['type'] && 'page_draft' != $input['type'])
        {
            /** 插入分类 */
            $categories = array_unique(array_map('trim', $input['categories']));

            /** 取出已有category */
            $existCategories = Typecho_Common::arrayFlatten($this->db->fetchAll(
            $this->db->select('table.metas.mid')
            ->from('table.metas')
            ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
            ->where('table.relationships.cid = ?', $insertId)
            ->where('table.metas.type = ?', 'category')), 'mid');
            
            /** 删除已有category */
            if ($existCategories) {
                foreach ($existCategories as $category) {
                    $this->db->query($this->db->delete('table.relationships')
                    ->where('cid = ?', $insertId)
                    ->where('mid = ?', $category));
                    
                    if ($count) {
                        $this->db->query($this->db->update('table.metas')
                        ->setKeywords('')       //让系统忽略count关键字
                        ->expression('count', 'count - 1')
                        ->where('mid = ?', $category));
                    }
                }
            }
            
            /** 插入category */
            if ($input['categories']) {
                foreach ($input['categories'] as $category) {
                    $selectCat = $this->db->select()->from('table.metas')->where('table.metas.name = ?',
                            $category)->where('table.metas.type = ?', 'category')->limit(1);
                    $cat = $this->db->fetchRow($selectCat);
                    $this->db->query($this->db->insert('table.relationships')
                    ->rows(array(
                        'mid'  =>   $cat['mid'],
                        'cid'  =>   $insertId
                    )));
                    
                    if ($count) {
                        $this->db->query($this->db->update('table.metas')
                        ->setKeywords('')       //让系统忽略count关键字
                        ->expression('count', 'count + 1')
                        ->where('mid = ?', $category));
                    }
                }
            }
        }
        return $insertId;

    }

    /**
     * 编辑post
     *
     * @param int $postId
     * @param string $userName
     * @param string $password
     * @param struct $content
     * @param bool $publish
     * @access public
     * @return int
     */
    public function mwEditPost($postId, $userName, $password, $content, $publish)
    {
        
        if(!$this->checkAccess($userName, $password))
        {
            return $this->error;
        }
        /** 过滤id为$postId的post */
        $select = $this->select()->where('table.contents.cid = ? AND table.contents.type = ?', $postId, 'post')->limit(1);

        /** 提交查询 */
        $post = $this->db->fetchRow($select, array($this, 'filter'));

        /** 验证权限*/
        if($post['authorId'] != $this->user->uid && !$this->checkAccess($userName, $password, 'administrator'))
        {
            return new IXR_Error('503', _t('对不起，你没有权限编辑此文章'));
        }

        $content['do'] = 'edit';
        $content['post_id'] = $postId;
        $content['publish'] = $publish;
        $data = serialize($content);
        $this->mwNewPost(1, $userName, $password, $content, $publish);
    }

    /**
     * 获取指定id的post 
     * 
     * @param int $postId 
     * @param string $userName 
     * @param string $password 
     * @access public
     * @return void
     */
    public function mwGetPost($postId, $userName, $password)
    {
        if(!$this->checkAccess($userName, $password))
        {
            return $this->error;
        }

        try {
            $post = $this->widget('Widget_Contents_Post_Edit', NULL, "cid={$postId}");
        } catch (Typecho_Widget_Exception $e) {
            return new IXR_Error($e->getCode(), $e->getMessage());
        }

        /** 对文章内容做截取处理，以获得description和text_more*/
        list($excerpt, $more) = $this->getPostExtended($post->text);
        /** 只需要分类的name*/
        $theCategory = array();
        foreach($post->categories as $category)
        {
            $theCategory = $category['name'];
        }

        $postStruct = array(
                'dateCreated'   => new IXR_Date($this->options->timezone + $post->created),
                'userid'        => $post->authorId,
                'postid'       => $post->cid,
                'description'   => $excerpt,
                'title'         => $post->title,
                'link'          => $post->permalink,
                'permalink'     => $post->permalink,
                'categories'    => $theCategory,
                'mt_excerpt'    => $excerpt,
                'mt_text_more'  => $more,
                'mt_allow_comments' => $post->allowComment,
                'mt_allow_pings' => $post->allowPing,
                'wp_slug'       => $post->slug,
                'wp_password'   => $post->password,
                'wp_author'     => $post->author->name,
                'wp_author_id'  => $post->authorId,
                'wp_author_display_name' => $post->author->screenName,
                );
        return $postStruct;
    }

    /**
     * 获取前$postsNum个post
     *
     * @param int $blogId
     * @param string $userName
     * @param string $password
     * @param int $postsNum
     * @access public
     * @return postStructs
     */
    public function mwGetRecentPosts($blogId, $userName, $password, $postsNum)
    {
        if(!$this->checkAccess($userName, $password))
        {
            return $this->error;
        }

        $posts = $this->widget('Widget_Contents_Post_Admin', "pageSize=$postsNum", 'status=all');

        $postStructs = array();
        /** 如果这个post存在则输出，否则输出错误 */
        while($posts->next())
        {
            /** 对文章内容做截取处理，以获得description和text_more*/
            list($excerpt, $more) = $this->getPostExtended($posts->text);
            /** 只需要分类的name*/
            $theCategory = array();
            foreach($posts->categories as $category)
            {
                $theCategory = $category['name'];
            }
             
            $postStruct = array(
                    'dateCreated'   => new IXR_Date($this->options->timezone + $posts->created),
                    'userid'        => $posts->authorId,
                    'postid'       => $posts->cid,
                    'description'   => $excerpt,
                    'title'         => $posts->title,
                    'link'          => $posts->permalink,
                    'permalink'     => $posts->permalink,
                    'categories'    => $theCategory,
                    'mt_excerpt'    => $excerpt,
                    'mt_text_more'  => $more,
                    'mt_allow_comments' => $posts->allowComment,
                    'mt_allow_pings' => $posts->allowPing,
                    'wp_slug'       => $posts->slug,
                    'wp_password'   => $posts->password,
                    'wp_author'     => $posts->author->name,
                    'wp_author_id'  => $posts->authorId,
                    'wp_author_display_name' => $posts->author->screenName,
                    );
            $postStructs[] = $postStruct;
        }
        if($postStructs)
        {
            return $postStructs;

        }
        else
        {
           return new IXR_Error(404, _t('对不起，没有任何文章'));
        }
    }

    /**
     * 获取所有的分类
     *
     * @param int $blogId
     * @param string $userName
     * @param string $password
     * @access public
     * @return categoryStructs
     */
    public function mwGetCategories($blogId, $userName, $password)
    {
        if(!$this->checkAccess($userName, $password))
        {
            return ($this->error);
        }

        $meta = $this->widget('Widget_Abstract_Metas');

        $this->db->fetchAll($meta->select()->where('type = ?', 'category')
        ->order('table.metas.order', Typecho_Db::SORT_ASC), array($this, 'push'));
        /** 初始化category数组*/
        $categoryStructs = array();
        while($this->next())
        {
            $categoryStructs[] = array(
                    'categoryId'    => $this->mid,
                    'parentId'      => 0,
                    'description'   => $this->description,
                    'categoryName'  => $this->name,
                    'htmlUrl'       => $this->permalink,
                    'rssUrl'        => $this->feedRssUrl,
                    );
        }
        return $categoryStructs;
    }

    /**
     * mwNewMediaObject
     *
     * @param int $blogId
     * @param string $userName
     * @param string $password
     * @param mixed $data
     * @access public
     * @return void
     */
    public function mwNewMediaObject($blogId, $userName, $password, $data)
    {
        /** typecho核心并不提供附件功能，如果需要此功能需要调用相关插件*/
        $upload = $this->plugin()->trigger($hasUploaded)->newMediaObject($data);
        if($hasUpload)
        {
            return $upload;
        }
        else
        {
            return IXR_Error(500, '不支持文件上传功能');
        }
    }

    /**
     * 获取 $postNum个post title
     *
     * @param int $blogId
     * @param string $userName
     * @param string $password
     * @param int $postNum
     * @access public
     * @return postTitleStructs
     */
    public function mtGetRecentPostTitles($blogId, $userName, $password, $postsNum)
    {
        if(!$this->checkAccess($userName, $password))
        {
            return ($this->error);
        }

        /** 读取数据*/
        $posts = $this->widget('Widget_Contents_Post_Admin', "pageSize=$postsNum", 'status=all');
        /**初始化*/
        $postTitleStructs = array();
        while($posts->next())
        {
            $postTitleStructs[] = array(
                    'dateCreated'   => new IXR_Date($this->options->timezone + $posts->created),
                    'userid'        => $posts->authorId,
                    'postid'        => $posts->cid,
                    'title'         => $posts->title,
                    );
        }
        return $postTitleStructs;

    }

    /**
     * 获取分类列表
     *
     * @param int $blogId
     * @param string $userName
     * @param string $password
     * @access public
     * @return categories
     */
    public function mtGetCategoryList($blogId, $userName, $password)
    {
        if(!$this->checkAccess($userName, $password))
        {
            return ($this->error);
        }
        $meta = $this->widget('Widget_Abstract_Metas');

        /** 构造出查询语句并且查询*/
        $select = $meta->select()->where('table.metas.type = ?', 'category');
        $this->db->fetchAll($select, array($this, 'push'));

        /** 初始化categorise数组*/
        $categories = array();
        while($this->next())
        {
            $categories[] = array(
                    'categoryId'   => $this->mid,
                    'categoryName' => $this->name,
                    );
        }
        return $categories;

    }

    /**
     * 获取指定post的分类
     *
     * @param int $postId
     * @param string $userName
     * @param string $password
     * @access public
     * @return void
     */
    public function mtGetPostCategories($postId, $userName, $password)
    {
        if(!$this->checkAccess($userName, $password))
        {
            return $this->error;
        }

        try {
            $post = $this->widget('Widget_Contents_Post_Edit', NULL, "cid={$postId}");
        } catch (Typecho_Widget_Exception $e) {
            return new IXR_Error($e->getCode(), $e->getMessage());
        }
        
        /** 格式化categories*/
        $categories = array();
        foreach($post->categories as $category)
        {
            $categories[] = array(
                    'categoryName'      => $category['name'],
                    'categoryId'        => $category['mid'],
                    'isPrimary'         => true,
                    );
        }
        return $categories;
    }

    /**
     * 修改post的分类
     *
     * @param int $postId
     * @param string $userName
     * @param string $password
     * @param string $categories
     * @access public
     * @return bool
     */
    public function mtSetPostCategories($postId, $userName, $password, $categories)
    {
        if(!$this->checkAccess($userName, $password, 'editor'))
        {
            return $this->error;
        }

        /** 先删除原来的relationships*/
        $this->db->query($this->db->sql()->where('cid = ?', $postId)->delete('table.relationships'));
        /** 插入新的relationships*/
        foreach($categoies as $category)
        {
            $categoryId = $category['categoryId'];
            $array = array(
                    'cid'   => $postId,
                    'mid'   => $categoryId,
                    );
            $this->db->query($this->db->sql()->insert()->rows($array));
        }
        return true;
    }

    /**
     * 发布(重建)数据
     *
     * @param int $postId
     * @param string $userName
     * @param string $password
     * @access public
     * @return bool
     */
    public function mtPublishPost($postId, $userName, $password)
    {
        if(!$this->checkAccess($userName, $password, 'editor'))
        {
            return $this->error;
        }

        /** 过滤id为$postId的post */
        $select = $this->select()->where('table.contents.cid = ? AND table.contents.type = ?', $postId, 'post')->limit(1);

        /** 提交查询 */
        $post = $this->$db->fetchRow($select, array($this, 'filter'));
        if($this->authorId != $this->user->uid && !$this->checkAccess($userName, $password, 'administrator'))
        {
            return new IXR_Error(403, '权限不足.');
        }

        /** 暂时只做成发布*/
        $content = array();
        $this->update($content, $this->db->sql()->where('table.contents.cid = ?', $postId));


    }

    /**
     * 取得当前用户的所有blog
     *
     * @param int $blogId
     * @param string $userName
     * @param string $password
     * @access public
     * @return void
     */
    public function bloggerGetUsersBlogs($blogId, $userName, $password)
    {
        if(!$this->checkAccess($userName, $password))
        {
            return $this->error;
        }

        $struct = array();
        $struct[] = array(
			'isAdmin' => true,
			'url'	    => $this->options->siteUrl,
			'blogid'   => 1,
			'blogName' => $this->options->title
		);
        return $struct;
    }

    /**
     * 返回当前用户的信息
     *
     * @param int $blogId
     * @param string $userName
     * @param string $password
     * @access public
     * @return void
     */
    public function bloggerGetUserInfo($blogId, $userName, $password)
    {
        if(!$this->checkAccess($userName, $password))
        {
            return $this->error;
        }

        $struct = array(
                'nickname'  => $this->user->name,
                'usrid'     => $this->user->authorId,
                'url'       => $this->user->url,
                'email'     => $this->user->mail,
                'lastname'  => $this->user->screenName,
                'firstname' => $this->user->screenName,
                );
        return $struct;
    }

    /**
     * 获取当前作者的一个指定id的post的详细信息
     *
     * @param int $blogId
     * @param int $postId
     * @param string $userName
     * @param string $password
     * @access public
     * @return void
     */
    public function bloggerGetPost($blogId, $postId, $userName, $password)
    {
        if(!$this->checkAccess($userName, $password))
        {
            return $this->error;
        }

        try {
            $post = $this->widget('Widget_Contents_Post_Edit', NULL, "cid={$postId}");
        } catch (Typecho_Widget_Exception $e) {
            return new IXR_Error($e->getCode(), $e->getMessage());
        }
        
        $content = '<title>' . $post->title . '</title>';
        $content .= '<category>' . $post->categaries['0']['name'];
        $content .= stripslashes($post->text);

        $struct = array(
                'userid'        => $post->authorId,
                'dateCreated'   => new IXR_Date($this->options->timezone + $post->created),
                'content'       => $content,
                'postid'        => $post->cid,
                );
        return $struct;
    }

    /**
     * 获取当前作者前postsNum个post
     *
     * @param int $blogId
     * @param string $userName
     * @param string $password
     * @param int $postsNum
     * @access public
     * @return void
     */
    public function bloggerGetRecentPosts($blogId, $userName, $password, $postsNum)
    {
        if(!$this->checkAccess($userName, $password))
        {
            return $this->error;
        }
        //todo:限制数量
        $posts = $this->widget('Widget_Contents_Post_Admin', "pageSize=$postsNum", 'status=all');
        
        $postStructs = array();
        while($posts->next())
        {
            $content = '<title>' . $posts->title . '</title>';
            $content .= '<category>' . $posts->categaries['0']['name'];
            $content .= stripslashes($posts->text);

            $struct = array(
                'userid'        => $posts->authorId,
                'dateCreated'   => new IXR_Date($this->options->timezone + $posts->created),
                'content'       => $content,
                'postid'        => $posts->cid,
            );
            $postStructs[] = $struct;
        }
        if(NULL == $postStructs)
        {
            return new IXR_Error('404', '没有任何文章');
        }
        return $postStructs;
    }

    /**
     * bloggerGetTemplate
     *
     * @param int $blogId
     * @param string $userName
     * @param string $password
     * @param mixed $template
     * @access public
     * @return void
     */
    public function bloggerGetTemplate($blogId, $userName, $password, $template)
    {
        if(!$this->checkAccess($userName, $password))
        {
            return $this->error;
        }
        /** todo:暂时先返回true*/
        return true;
    }

    /**
     * bloggerSetTemplate
     *
     * @param int $blogId
     * @param string $userName
     * @param string $password
     * @param mixed $content
     * @param mixed $template
     * @access public
     * @return void
     */
    public function bloggerSetTemplate($blogId, $userName, $password, $content, $template)
    {
        if(!$this->checkAccess($userName, $password))
        {
            return $this->error;
        }
        /** todo:暂时先返回true*/
        return true;
    }

    public function pingbackPing($source, $target)
    {
        /** 检查源地址是否存在*/
        $http = Typecho_Http_Client::get();
        if($response = $http->send($source))
        {
            if(200 == $http->getResponseStatus())
            {
                if(!$http->getResponseHeader('x-pingback'))
                {
                    preg_match_all("/<link[^>]*rel=[\"']([^\"']*)[\"'][^>]*href=[\"']([^\"']*)[\"'][^>]*>/i", $response, $out);
                    if(!isset($out[1]['pingback']))
                    {
                        return new IXR_Error(50, _t('源地址不支持PingBack'));
                    }
                }
            }
            else
            {
                 return new IXR_Error(16, _t('源地址服务器错误'));
            }
        }
        else
        {
            return new IXR_Error(16, _t('源地址服务器错误'));
        }

        /** 检查目标地址是否正确*/
        if(($pos = strpos($target, $this->options->siteUrl . 'index.php/')) === 0)
        {
            $pathInfo = substr($target, $pos + 1);
            /** 这样可以得到cid或者slug*/
            if($route = Typecho_Router::match($pathInfo))
            {
                //todo:为什么是type啊？
                if(NULL != $this->request->type) {
                    $select = $this->select()->where('table.contents.cid = ?',$this->request->type)->limit(1);
                }else {
                    /** 文章不存在*/
                    return new IXR_Error(33, _t('这个目标地址不存在'));
                }
            }
            else
            {
                return new IXR_Error(33, _t('这个目标地址不存在'));
            }

            /** 提交查询 */
            $post = $this->db->fetchRow($select, array($this, 'filter'));
            if($post)
            {
                /** 检查是否可以ping*/
                if($post['allowPing'] && ($post['type'] == 'post' || $post['type'] == 'page'))
                {
                    /** 现在可以ping了，但是还得检查下这个pingback是否已经存在了*/
                    $pingNum = $this->db->fetchObject($this->db->select(array('COUNT(coid)' => 'num'))->from('table.comments')->where('table.comments.cid = ? AND table.comments.url = ? AND table.comments.type <> ?', $post['cid'], $source, 'comment'))->num;
                    if($pingNum <= 0)
                    {
                        /** 现在开始插入以及邮件提示了 $response就是第一行请求时返回的数组*/
                        preg_match("/\<title\>([^<]*?)\<\/title\\>/is", $response, $matchTitle);
                        $finalTitle = $matchTitle[1];
                        /** 干掉html tag，只留下<a>*/
                        $text = Typecho_Common::stripTags($response, '<a href="">');
                        /** 此处将$target quote,留着后面用*/
                        $pregLink = preg_quote($target);
                        /** 找出含有target链接的最长的一行作为$finalText*/
                        $finalText = '';
                        $lines = explode("\n", $text);
                        foreach($lines as $line)
                        {
                            $line = trim($line);
 						    if(NULL != $line)
 						    {
 						        if(preg_match("|<a[^>]*href=[\"']{$pregLink}[\"'][^>]*>(.*?)</a>|",$line))
 						        {
 						            if(strlen($line) > strlen($finalText))
 						            {
                                        /** <a>也要干掉，*/
 						                $finalText = Typecho_Common::stripTags($line);
 						            }
 						        }
 						    }
                        }
                        /** 截取一段字*/
                        if(NULL == trim($finalText))
                        {
                            return new IXR_Error('17', _t('源地址中不包括目标地址'));
                        }
                        $finalText = '[...]' . Typecho_Common::subStr($finalText, 0, 200) . '[...]';
                        /** 组织$input，准备插入*/
                        $input = array();
                        $input['cid'] = $post['cid'];
                        $input['created'] = time() - $this->options->timezone;
                        $input['author'] = $finalTitle;
                        $input['ownerId'] = $post['authorId'];
                        $input['url'] = $source;
                        $input['ip'] = $_SERVER["REMOTE_ADDR"];
                        $input['agent'] = $_SERVER["HTTP_USER_AGENT"];
                        $input['text'] = $finalText;
                        $input['type'] = 'pingback';
                        if(0 != $this->options->commentsRequireModeration)
                        {
                            $input['status'] = 'waiting';
                        }
                        else
                        {
                            $input['status'] = 'approved';
                        }

                        /** 执行插入*/
                        return $insertId = $this->widget('Widget_Abstract_Comments')->insert($input);

                        /** todo:发送邮件提示*/
                    }
                    else
                    {
                        return new IXR_Error(48, _t('PingBack已经存在'));
                    }
                }
                else
                {
                    return IXR_Error(49, _t('目标地址禁止Ping'));
                }
            }
            else
            {
                return new IXR_Error(33, _t('这个目标地址不存在'));
            }

        }
        else
        {
            return new IXR_Error(33, _t('这个目标地址错误.'));
        }
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
            /** 直接把初始化放到这里 */
            new Ixr_Server(array(
                /** WordPress API */
                'wp.getPage'            => array($this,'wpGetPage'),
                'wp.getPages'            => array($this,'wpGetPages'),
                'wp.newPage'            => array($this,'wpNewPage'),
                'wp.deletePage'            => array($this,'wpDeletePage'),
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
                'pingback.extensions.getPingbacks' => array($this,'pingbackExtensionsGetPingbacks'),
            ));
        }
    }
}
