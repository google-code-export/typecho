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
    public function checkAccess($name, $password, $level = 'contributor')
    {
        if ($this->user->login($name, $password, true)) {
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
        if (!$this->checkAccess($userName, $password)) {
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
        list($excerpt, $more) = $this->getPostExtended($page->content);

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
                'excerpt'       => NULL,
                'text_more'     => $more,
                'mt_allow_comments' => $page->allowComment,
                'mt_allow_pings' => $page->allowPing,                          
                'wp_slug'        => $page->slug,
                'wp_password'   => $page->password,
                'wp_author'     => $page->author->name,
                'wp_page_parent_id' => 0,
                'wp_page_parent_title' => NULL,
                'wp_page_order' => $page->order,     //meta是描述字段, 在page时表示顺序
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
            list($excerpt, $more) = $this->getPostExtended($pages->content);
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
                'excerpt'       => NULL,
                'text_more'     => $more,
                'mt_allow_comments' => $pages->allowComment,
                'mt_allow_pings' => $pages->allowPing,                          
                'wp_slug'        => $pages->slug,
                'wp_password'   => $pages->password,
                'wp_author'     => $pages->author->name,
                'wp_page_parent_id' => 0,
                'wp_page_parent_title' => NULL,
                'wp_page_order' => $pages->order,     //meta是描述字段, 在page时表示顺序
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
        if (!$this->checkAccess($userName, $password, 'editor')) {
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
        if (!$this->checkAccess($userName, $password, 'editor')) {
            return $this->error;
        }
        
        /** 删除页面 */
        try {
            /** 此组件会进行复杂的权限检测 */
            $page = $this->widget('Widget_Contents_Page_Edit', NULL, "do=delete&cid={$pageId}", false);
        } catch (Typecho_Widget_Exception $e) {
            /** 截获可能会抛出的异常(参见 Widget_Contents_Page_Edit 的 execute 方法) */
            return new IXR_Error($e->getCode(), $e->getMessage());
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
        if (!$this->checkAccess($userName, $password, 'editor')) {
            return ($this->error);
        }
        $pages = $this->widget('Widget_Contents_Page_Admin', NULL, 'status=all');
        /**初始化*/
        $pageStructs = array();
        
        while ($pages->next()) {
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
        if (!$this->checkAccess($userName, $password, 'editor')) {
            return ($this->error);
        }

        /** 构建查询*/
        $select = $this->db->select('table.users.uid', 'table.users.name', 'table.users.screenName')->from('table.users');
        $authors = $this->db->fetchAll($select);

        $authorStructs = array();
        foreach ($authors as $author) {
            $authorStructs[] = array(
                'user_id'       => $author['uid'],
                'user_login'    => $author['name'],
                'display_name'  => $author['screenName']
            );
        }
        
        return $authorStructs;
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
        if (!$this->checkAccess($userName, $password)) {
            return ($this->error);
        }

        /** 开始接受数据 */
        $option['name'] = $category['name'];
        $option['slug'] = Typecho_Common::slugName(empty($category['slug']) ? $category['name'] : $category['slug']);
        $option['type'] = 'category';
        $option['description'] = isset($category['description']) ? $category['description'] : $category['name'];

        /** 初始化meta widget，然后插入*/
        $meta = $this->widget('Widget_Abstract_Metas');
        if (!$meta->insert($option)) {
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
        if (!$this->checkAccess($userName, $password)) {
            return ($this->error);
        }

        $meta = $this->widget('Widget_Abstract_Metas');

        /** 构造出查询语句并且查询*/
        $key = Typecho_Common::filterSearchQuery($category);
        $key = '%' . $key . '%';
        $select = $meta->select()->where('table.metas.type = ? AND (table.metas.name LIKE ? OR slug LIKE ?)', 'category', $key, $key);
        
        /** 不要category push到contents的容器中 */
        $categories = $this->db->fetchAll($select);
        
        /** 初始化categorise数组*/
        $categoryStructs = array();
        foreach ($categories as $category) {
            $categoryStructs[] = array(
                    'category_id'   => $category['mid'],
                    'category_name' => $category['name'],
                    );
        }
        
        return $categoryStructs;
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
        if (!$this->checkAccess($userName, $password)) {
            return $this->error;
        }

        /** 取得content内容 */
        $input = array();
        $input['title'] = trim($content['title']) == NULL ? _t('未命名文档') : $content['title'];
        $input['slug'] = isset($content['slug']) ? $content['slug'] : NULL;
        
        $input['text'] = isset($content['mt_text_more']) && $content['mt_text_more'] ? 
        $content['description'] . "\n<!--more-->\n" . $content['mt_text_more'] : $content['description'];
        $input['text'] = Typecho_Common::beautifyFormat(Typecho_Common::removeParagraph($input['text']));
        $input['text'] = $this->pluginHandle()->fromOfflineEditor($input['text']);
        $input['password'] = isset($content["wp_password"]) ? $content["wp_password"] : NULL;

        $input['tags'] = isset($content['mt_keywords']) ? $content['mt_keywords'] : NULL;
        $input['type'] = isset($content['post_type']) ? $content['post_type'] : 'post';
        $input['draft'] = !$publish;
        $input['category'] = array();
        
        if (isset($content['postId'])) {
            $input['cid'] = $content['postId'];
        }
        
        if (isset($content['dateCreated'])) {
            /** 解决客户端与服务器端时间偏移 */
            $input['created'] = $content['dateCreated']->getTimestamp() - $this->options->timezone + $this->options->serverTimezone;
        }
        
        if (!empty($content['categories']) && is_array($content['categories'])) {
            foreach ($content['categories'] as $category) {
                $input['category'][] = $this->db->fetchObject($this->db->select('mid')
                ->from('table.metas')->where('type = ? AND name = ?', 'category', $category)
                ->limit(1))->mid;
            }
        }
        
        $input['allowComment'] = (isset($content['mt_allow_comments']) && (1 == $content['mt_allow_comments']
        || 'open' == $content['mt_allow_comments'])) ? 1 : ((isset($content['mt_allow_comments']) && (0 == $content['mt_allow_comments']
        || 'closed' == $content['mt_allow_comments'])) ? 0 : $this->options->defaultAllowComment);
        
        $input['allowPing'] = (isset($content['mt_allow_pings']) && (1 == $content['mt_allow_pings']
        || 'open' == $content['mt_allow_pings'])) ? 1 : ((isset($content['mt_allow_pings']) && (0 == $content['mt_allow_pings']
        || 'closed' == $content['mt_allow_pings'])) ? 0 : $this->options->defaultAllowPing);

        $input['allowFeed'] = $this->options->defaultAllowFeed;
        
        
        /** 调用已有组件 */
        try {
            if (isset($input['cid'])) {
                /** 编辑 */
                $input['do'] = 'update';
                if (empty($input['slug'])) {
                    unset($input['slug']);
                }
                $this->widget('Widget_Contents_Post_Edit', NULL, $input, false)->updatePost();
                return $this->widget('Widget_Notice')->getHighlightId();
            } else {
                /** 插入 */
                $input['do'] = 'insert';
                $this->widget('Widget_Contents_Post_Edit', NULL, $input, false)->insertPost();
                return $this->widget('Widget_Notice')->getHighlightId();
            }
        } catch (Typecho_Widget_Exception $e) {
            return new IXR_Error($e->getCode(), $e->getMessage());
        }
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
        $content['postId'] = $postId;
        return $this->mwNewPost(1, $userName, $password, $content, $publish);
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
        if (!$this->checkAccess($userName, $password)) {
            return $this->error;
        }

        try {
            $post = $this->widget('Widget_Contents_Post_Edit', NULL, "cid={$postId}");
        } catch (Typecho_Widget_Exception $e) {
            return new IXR_Error($e->getCode(), $e->getMessage());
        }

        /** 对文章内容做截取处理，以获得description和text_more*/
        list($excerpt, $more) = $this->getPostExtended($post->content);
        /** 只需要分类的name*/
        $categories = Typecho_Common::arrayFlatten($post->categories, 'name');
        $tags = Typecho_Common::arrayFlatten($post->tags, 'name');

        $postStruct = array(
                'dateCreated'   => new IXR_Date($this->options->timezone + $post->created),
                'userid'        => $post->authorId,
                'postid'       => $post->cid,
                'description'   => $excerpt,
                'title'         => $post->title,
                'link'          => $post->permalink,
                'permalink'     => $post->permalink,
                'categories'    => $categories,
                'mt_excerpt'    => NULL,
                'mt_text_more'  => $more,
                'mt_allow_comments' => $post->allowComment,
                'mt_allow_pings' => $post->allowPing,
                'mt_keywords'	=> $tags,
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
        if (!$this->checkAccess($userName, $password)) {
            return $this->error;
        }

        $posts = $this->widget('Widget_Contents_Post_Admin', "pageSize={$postsNum}", 'status=all');

        $postStructs = array();
        /** 如果这个post存在则输出，否则输出错误 */
        while ($posts->next()) {
            /** 对文章内容做截取处理，以获得description和text_more*/
            list($excerpt, $more) = $this->getPostExtended($posts->content);
            
            /** 只需要分类的name*/
            /** 可以用flatten函数处理 */
            $categories = Typecho_Common::arrayFlatten($posts->categories, 'name');
            $tags = Typecho_Common::arrayFlatten($posts->tags, 'name');
             
            $postStructs[] = array(
                    'dateCreated'   => new IXR_Date($this->options->timezone + $posts->created),
                    'userid'        => $posts->authorId,
                    'postid'       => $posts->cid,
                    'description'   => $excerpt,
                    'title'         => $posts->title,
                    'link'          => $posts->permalink,
                    'permalink'     => $posts->permalink,
                    'categories'    => $categories,
                    'mt_excerpt'    => NULL,
                    'mt_text_more'  => $more,
                    'mt_allow_comments' => $posts->allowComment,
                    'mt_allow_pings' => $posts->allowPing,
                    'mt_keywords'	=> $tags,
                    'wp_slug'       => $posts->slug,
                    'wp_password'   => $posts->password,
                    'wp_author'     => $posts->author->name,
                    'wp_author_id'  => $posts->authorId,
                    'wp_author_display_name' => $posts->author->screenName,
                    );
        }

        return $postStructs;
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
        if (!$this->checkAccess($userName, $password)) {
            return ($this->error);
        }

        $categories = $this->widget('Widget_Metas_Category_List');

        /** 初始化category数组*/
        $categoryStructs = array();
        while ($categories->next()) {
            $categoryStructs[] = array(
                    'categoryId'    => $categories->mid,
                    'parentId'      => 0,
                    'categoryName'  => $categories->name,
                    'description'   => $categories->name,
                    'htmlUrl'       => $categories->permalink,
                    'rssUrl'        => $categories->feedRssUrl,
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
        if (!$this->checkAccess($userName, $password)) {
            return $this->error;
        }
    
        $uploadHandle = unserialize($this->options->uploadHandle);
        $deleteHandle = unserialize($this->options->deleteHandle);
        $attachmentHandle = unserialize($this->options->attachmentHandle);

        $result = call_user_func($uploadHandle, $data);
        
        if (false === $result) {
            return IXR_Error(500, _t('上传失败'));
        } else {
        
            $result['uploadHandle'] = $uploadHandle;
            $result['deleteHandle'] = $deleteHandle;
            $result['attachmentHandle'] = $attachmentHandle;
        
            $this->insert(array(
                'title'     =>  $result['name'],
                'slug'      =>  $result['name'],
                'type'      =>  'attachment',
                'text'      =>  serialize($result),
                'allowComment'      =>  1,
                'allowPing'         =>  0,
                'allowFeed'         =>  1
            ));

            return array(
                'file' => $result['name'],
                'url'  => call_user_func($attachmentHandle, $result['path'])
            );
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
        if (!$this->checkAccess($userName, $password)) {
            return ($this->error);
        }

        /** 读取数据*/
        $posts = $this->widget('Widget_Contents_Post_Admin', "pageSize=$postsNum", 'status=all');
        
        /**初始化*/
        $postTitleStructs = array();
        while ($posts->next()) {
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
        if (!$this->checkAccess($userName, $password)) {
            return ($this->error);
        }
        
        $categories = $this->widget('Widget_Metas_Category_List');

        /** 初始化categorise数组*/
        $categoryStructs = array();
        while ($categories->next()) {
            $categoryStructs[] = array(
                'categoryId'   => $categories->mid,
                'categoryName' => $categories->name,
            );
        }
        return $categoryStructs;
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
        if (!$this->checkAccess($userName, $password)) {
            return $this->error;
        }

        try {
            $post = $this->widget('Widget_Contents_Post_Edit', NULL, "cid={$postId}");
        } catch (Typecho_Widget_Exception $e) {
            return new IXR_Error($e->getCode(), $e->getMessage());
        }
        
        /** 格式化categories*/
        $categories = array();
        foreach ($post->categories as $category) {
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
        if (!$this->checkAccess($userName, $password, 'editor')) {
            return $this->error;
        }
        
        try {
            $post = $this->widget('Widget_Contents_Post_Edit', NULL, "cid={$postId}");
        } catch (Typecho_Widget_Exception $e) {
            return new IXR_Error($e->getCode(), $e->getMessage());
        }
        
        $post->setCategories($postId, Typecho_Common::arrayFlatten($categories, 'categoryId'),
        'publish' == $post->status);
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
        if (!$this->checkAccess($userName, $password, 'editor')) {
            return $this->error;
        }

        /** 过滤id为$postId的post */
        $select = $this->select()->where('table.contents.cid = ? AND table.contents.type = ?', $postId, 'post')->limit(1);

        /** 提交查询 */
        $post = $this->$db->fetchRow($select, array($this, 'filter'));
        if ($this->authorId != $this->user->uid && !$this->checkAccess($userName, $password, 'administrator')) {
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
        if (!$this->checkAccess($userName, $password)) {
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
        if (!$this->checkAccess($userName, $password)) {
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
        if (!$this->checkAccess($userName, $password)) {
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
     * bloggerDeletePost 
     * 删除文章
     * @param mixed $blogId 
     * @param mixed $userName 
     * @param mixed $password 
     * @param mixed $publish 
     * @access public
     * @return bool
     */
    public function bloggerDeletePost($blogId, $postId, $userName, $password, $publish)
    {
        if (!$this->checkAccess($userName, $password)) {
            return $this->error;
        }
        try {
            $this->widget('Widget_Contents_Post_Edit', NULL, "cid={$postId}")->deletePost();
        } catch (Typecho_Widget_Exception $e) {
            return new IXR_Error($e->getCode(), $e->getMessage());
        }
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
        if (!$this->checkAccess($userName, $password)) {
            return $this->error;
        }
        //todo:限制数量
        $posts = $this->widget('Widget_Contents_Post_Admin', "pageSize=$postsNum", 'status=all');
        
        $postStructs = array();
        while ($posts->next()) {
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
        if (NULL == $postStructs) {
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
        if (!$this->checkAccess($userName, $password)) {
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
        if (!$this->checkAccess($userName, $password)) {
            return $this->error;
        }
        /** todo:暂时先返回true*/
        return true;
    }

    /**
     * pingbackPing 
     * 
     * @param string $source 
     * @param string $target 
     * @access public
     * @return void
     */
    public function pingbackPing($source, $target)
    {
        /** 检查源地址是否存在*/
        if (!($http = Typecho_Http_Client::get())) {
            return new IXR_Error(16, _t('源地址服务器错误'));
        }
        
        try {
        
            $http->setTimeout(5)->send($source);
            $response = $http->getResponseBody();
            
            if (200 == $http->getResponseStatus()) {
            
                if (!$http->getResponseHeader('x-pingback')) {
                    preg_match_all("/<link[^>]*rel=[\"']([^\"']*)[\"'][^>]*href=[\"']([^\"']*)[\"'][^>]*>/i", $response, $out);
                    if (!isset($out[1]['pingback'])) {
                        return new IXR_Error(50, _t('源地址不支持PingBack'));
                    }
                }
                
            } else {
                return new IXR_Error(16, _t('源地址服务器错误'));
            }
            
        } catch (Exception $e) {
            return new IXR_Error(16, _t('源地址服务器错误'));
        }

        /** 检查目标地址是否正确*/
        $pathInfo = Typecho_Common::url(substr($target, strlen($this->options->index)), '/');
        Typecho_Router::match($pathInfo, $params);
        $post = $this->widget('Widget_Archive', NULL, $params);
        
        /** 这样可以得到cid或者slug*/
        if (!$post->have() || !$post->is('single')) {
            return new IXR_Error(33, _t('这个目标地址不存在'));
        }
        
        if ($post) {
            /** 检查是否可以ping*/
            if ($post->allowPing) {
            
                /** 现在可以ping了，但是还得检查下这个pingback是否已经存在了*/
                $pingNum = $this->db->fetchObject($this->db->select(array('COUNT(coid)' => 'num'))
                ->from('table.comments')->where('table.comments.cid = ? AND table.comments.url = ? AND table.comments.type <> ?',
                $post->cid, $source, 'comment'))->num;
                
                if ($pingNum <= 0) {
                
                    /** 现在开始插入以及邮件提示了 $response就是第一行请求时返回的数组*/
                    preg_match("/\<title\>([^<]*?)\<\/title\\>/is", $response, $matchTitle);
                    $finalTitle = Typecho_Common::removeXSS(trim(strip_tags($matchTitle[1])));
                    
                    /** 干掉html tag，只留下<a>*/
                    $text = Typecho_Common::stripTags($response, '<a href="">');
                    
                    /** 此处将$target quote,留着后面用*/
                    $pregLink = preg_quote($target);
                    
                    /** 找出含有target链接的最长的一行作为$finalText*/
                    $finalText = '';
                    $lines = explode("\n", $text);
                    
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (NULL != $line) {
                            if (preg_match("|<a[^>]*href=[\"']{$pregLink}[\"'][^>]*>(.*?)</a>|",$line)) {
                                if (strlen($line) > strlen($finalText)) {
                                    /** <a>也要干掉，*/
                                    $finalText = Typecho_Common::stripTags($line);
                                }
                            }
                        }
                    }
                    
                    /** 截取一段字*/
                    if (NULL == trim($finalText)) {
                        return new IXR_Error('17', _t('源地址中不包括目标地址'));
                    }
                    
                    $finalText = '[...]' . Typecho_Common::subStr($finalText, 0, 200, '') . '[...]';
                    
                    $pingback = array(
                        'cid'       =>  $post->cid,
                        'created'   =>  $this->options->gmtTime,
                        'agent'     =>  $this->request->getAgent(),
                        'ip'        =>  $this->request->getIp(),
                        'author'    =>  $finalTitle,
                        'url'       =>  Typecho_Common::safeUrl($source),
                        'text'      =>  $finalText,
                        'ownerId'   =>  $post->author->uid,
                        'type'      =>  'pingback',
                        'status'    =>  $this->options->commentsRequireModeration ? 'waiting' : 'approved'
                    );
                    
                    /** 加入plugin */
                    $pingback = $this->pluginHandle()->pingback($pingback, $post);

                    /** 执行插入*/
                    $insertId = $this->widget('Widget_Abstract_Comments')->insert($pingback);

                    /** 评论完成接口 */
                    $this->pluginHandle()->finishPingback($this);

                    return $insertId;

                    /** todo:发送邮件提示*/
                } else {
                    return new IXR_Error(48, _t('PingBack已经存在'));
                }
            } else {
                return IXR_Error(49, _t('目标地址禁止Ping'));
            }
        } else {
            return new IXR_Error(33, _t('这个目标地址不存在'));
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
        if (isset($this->request->rsd)) {
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
        } else if (isset($this->request->wlw)) {
            echo
<<<EOF
<?xml version="1.0" encoding="{$this->options->charset}"?>
<manifest xmlns="http://schemas.microsoft.com/wlw/manifest/weblog">
    <options>
        <supportsKeywords>Yes</supportsKeywords>
        <supportsFileUpload>Yes</supportsFileUpload>
        <supportsExtendedEntries>Yes</supportsExtendedEntries>
        <supportsCustomDate>Yes</supportsCustomDate>
        <supportsCategories>Yes</supportsCategories>

        <supportsCategoriesInline>Yes</supportsCategoriesInline>
        <supportsMultipleCategories>Yes</supportsMultipleCategories>
        <supportsHierarchicalCategories>No</supportsHierarchicalCategories>
        <supportsNewCategories>Yes</supportsNewCategories>
        <supportsNewCategoriesInline>Yes</supportsNewCategoriesInline>
        <supportsCommentPolicy>Yes</supportsCommentPolicy>

        <supportsPingPolicy>Yes</supportsPingPolicy>
        <supportsAuthor>Yes</supportsAuthor>
        <supportsSlug>Yes</supportsSlug>
        <supportsPassword>Yes</supportsPassword>
        <supportsExcerpt>Yes</supportsExcerpt>
        <supportsTrackbacks>Yes</supportsTrackbacks>

        <supportsPostAsDraft>Yes</supportsPostAsDraft>

        <supportsPages>Yes</supportsPages>
        <supportsPageParent>No</supportsPageParent>
        <supportsPageOrder>Yes</supportsPageOrder>
        <requiresXHTML>True</requiresXHTML>
        <supportsAutoUpdate>No</supportsAutoUpdate>

    </options>
</manifest>
EOF;
        } else {
            /** 直接把初始化放到这里 */
            new IXR_Server(array(
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
