<?php
/**
 * 编辑文章
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 编辑文章组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Contents_Post_Edit extends Widget_Abstract_Contents implements Widget_Interface_Do
{
    /**
     * 将tags取出
     * 
     * @access protected
     * @return array
     */
    protected function ___tags()
    {
        if ($this->have()) {
            return $this->db->fetchAll($this->db
            ->select()->from('table.metas')
            ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
            ->where('table.relationships.cid = ?', $this->cid)
            ->where('table.metas.type = ?', 'tag'), array($this->widget('Widget_Abstract_Metas'), 'filter'));
        }
        
        return array();
    }
    
    /**
     * 获取当前所有自定义模板
     * 
     * @access protected
     * @return array
     */
    protected function ___templates()
    {
        $files = glob(__TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_THEME_DIR__ . '/' . $this->options->theme . '/*.php');
        $result = array();
        
        foreach ($files as $file) {
            $info = Typecho_Plugin::parseInfo($file);
            $file = basename($file);
            
            if ('index.php' != $file && 'custom' == $info['title']) {
                $result[] = array(
                    'name'  =>  $info['description'],
                    'value' =>  $file
                );
            }
        }
        
        return $result;
    }

    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        /** 必须为贡献者以上权限 */
        $this->user->pass('contributor');
    
        /** 获取文章内容 */
        if ((isset($this->request->cid) && 'delete' != $this->request->do
         && 'insert' != $this->request->do) || 'update' == $this->request->do) {
            $post = $this->db->fetchRow($this->select()
            ->where('table.contents.type = ?', 'post')
            ->where('table.contents.cid = ?', $this->request->filter('int')->cid)
            ->limit(1), array($this, 'push'));
            
            if (!$this->have()) {
                throw new Typecho_Widget_Exception(_t('文章不存在'), 404);
            } else if ($post && 'update' == $this->request->do && !$this->allow('edit')) {
                throw new Typecho_Widget_Exception(_t('没有编辑权限'), 403);
            }
        }
    }
    
    /**
     * 重载获取内容的方法
     * 
     * @access public
     * @return void
     */
    public function content()
    {
        echo htmlspecialchars(trim($this->text));
    }
    
    /**
     * 输出文章发布日期
     *
     * @access public
     * @param string $format 日期格式
     * @return void
     */
    public function date($format = NULL)
    {
        if (isset($this->created)) {
            parent::date($format);
        } else {
            echo date($format, $this->options->gmtTime + $this->options->timezone);
        }
    }
    
    /**
     * 获取文章权限
     *
     * @access public
     * @param string $permission 权限
     * @return unknown
     */
    public function allow()
    {
        $permissions = func_get_args();
        $allow = true;

        foreach ($permissions as $permission) {
            $permission = strtolower($permission);

            if ('edit' == $permission) {
                $allow &= ($this->user->pass('editor', true) || $this->authorId == $this->user->uid);
            } else {
                $permission = 'allow' . ucfirst(strtolower($permission));
                $optionPermission = 'default' . ucfirst($permission);
                $allow &= (isset($this->{$permission}) ? $this->{$permission} : $this->options->{$optionPermission});
            }
        }

        return $allow;
    }
    
    /**
     * 获取网页标题
     * 
     * @access public
     * @return string
     */
    public function getMenuTitle()
    {
        return _t('编辑 %s', $this->title);
    }
    
    /**
     * 设置内容标签
     * 
     * @access public
     * @param integer $cid
     * @param string $tags
     * @param boolean $count 是否参与计数
     * @return string
     */
    public function setTags($cid, $tags, $count = true)
    {
        $tags = str_replace(array(' ', '，', ' '), ',', $tags);
        $tags = array_unique(array_map('trim', explode(',', $tags)));

        /** 取出已有tag */
        $existTags = Typecho_Common::arrayFlatten($this->db->fetchAll(
        $this->db->select('table.metas.mid')
        ->from('table.metas')
        ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
        ->where('table.relationships.cid = ?', $cid)
        ->where('table.metas.type = ?', 'tag')), 'mid');
        
        /** 删除已有tag */
        if ($existTags) {
            foreach ($existTags as $tag) {
                $this->db->query($this->db->delete('table.relationships')
                ->where('cid = ?', $cid)
                ->where('mid = ?', $tag));
                
                if ($count) {
                    $this->db->query($this->db->update('table.metas')
                    ->setKeywords('')       //让系统忽略count关键字
                    ->expression('count', 'count - 1')
                    ->where('mid = ?', $tag));
                }
            }
        }
        
        /** 取出插入tag */
        $insertTags = $this->widget('Widget_Abstract_Metas')->scanTags($tags);
        
        /** 插入tag */
        if ($insertTags) {
            foreach ($insertTags as $tag) {
                $this->db->query($this->db->insert('table.relationships')
                ->rows(array(
                    'mid'  =>   $tag,
                    'cid'  =>   $cid
                )));
                
                if ($count) {
                    $this->db->query($this->db->update('table.metas')
                    ->setKeywords('')       //让系统忽略count关键字
                    ->expression('count', 'count + 1')
                    ->where('mid = ?', $tag));
                }
            }
        }
    }
    
    /**
     * 设置分类
     * 
     * @access public
     * @param integer $cid 内容id
     * @param array $categories 分类id的集合数组
     * @param boolean $count 是否参与计数
     * @return integer
     */
    public function setCategories($cid, array $categories, $count = true)
    {
        $categories = array_unique(array_map('trim', $categories));

        /** 取出已有category */
        $existCategories = Typecho_Common::arrayFlatten($this->db->fetchAll(
        $this->db->select('table.metas.mid')
        ->from('table.metas')
        ->join('table.relationships', 'table.relationships.mid = table.metas.mid')
        ->where('table.relationships.cid = ?', $cid)
        ->where('table.metas.type = ?', 'category')), 'mid');
        
        /** 删除已有category */
        if ($existCategories) {
            foreach ($existCategories as $category) {
                $this->db->query($this->db->delete('table.relationships')
                ->where('cid = ?', $cid)
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
        if ($categories) {
            foreach ($categories as $category) {
                $this->db->query($this->db->insert('table.relationships')
                ->rows(array(
                    'mid'  =>   $category,
                    'cid'  =>   $cid
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
    
    /**
     * 新增文章
     * 
     * @access public
     * @return void
     */
    public function insertPost()
    {
        $contents = $this->request->from('password', 'text','allowComment',
        'allowPing', 'allowFeed', 'slug', 'category', 'tags', 'status');
        $contents['type'] = 'post';
        $contents['status'] = $this->request->draft ? 'draft' :
        (($this->user->pass('editor', true) && !$this->request->draft) ? 'publish' : 'waiting');
        
        $contents['title'] = $this->request->nil(_t('未命名文档'))->title;
        $contents['text'] = trim($contents['text']);
        $contents['created'] = isset($this->request->created) ? ($this->request->created - $this->options->timezone)
        : (isset($this->request->date) ? strtotime($this->request->date) - $this->options->timezone : $this->options->gmtTime);

        /** 提交数据的过滤 */
        $contents = $this->plugin()->insert($contents);
        $insertId = $this->insert($contents);
        
        if ($insertId > 0) {
            /** 插入分类 */
            $this->setCategories($insertId, !empty($contents['category']) && is_array($contents['category']) ? 
            $contents['category'] : array(), 'publish' == $contents['status']);
            
            /** 插入标签 */
            $this->setTags($insertId, empty($contents['tags']) ? NULL : $contents['tags'], 'publish' == $contents['status']);
        }
        
        $this->db->fetchRow($this->select()->where('table.contents.cid = ?', $insertId)->limit(1), array($this, 'push'));
        
        /** 文章提示信息 */
        if ('publish' == $contents['status']) {
            $this->widget('Widget_Notice')->set($insertId > 0 ? 
            _t('文章 "<a href="%s">%s</a>" 已经被创建', $this->permalink, $this->title)
            : _t('文章提交失败'), NULL, $insertId > 0 ? 'success' : 'error');
        } else if ('draft' == $contents['status']) {
            $this->widget('Widget_Notice')->set($insertId > 0 ? 
            _t('草稿 "%s" 已经被保存', $this->title) :
            _t('草稿保存失败'), NULL, $insertId > 0 ? 'success' : 'error');
        } else if ('waiting' == $contents['status']) {
            $this->widget('Widget_Notice')->set($insertId > 0 ? 
            _t('文章 "%s" 等待审核', $this->title) :
            _t('文章提交失败'), NULL, $insertId > 0 ? 'notice' : 'error');
        }
        
        /** 设置高亮 */
        $this->widget('Widget_Notice')->highlight($this->theId);

        /** 跳转页面 */
        if ('draft' == $contents['status']) {
            $this->response->redirect(Typecho_Common::url('write-post.php?cid=' . $insertId, $this->options->adminUrl));
        } else {
            $this->response->redirect(Typecho_Common::url('manage-posts.php?status=' . $contents['status'], $this->options->adminUrl));
        }
    }
    
    /**
     * 更新文章
     * 
     * @access public
     * @return void
     */
    public function updatePost()
    {
        $contents = $this->request->from('password', 'text', 'allowComment',
        'allowPing', 'allowFeed', 'slug', 'category', 'tags');
        $contents['type'] = 'post';
        $contents['status'] = $this->request->draft ? 'draft' :
        (($this->user->pass('editor', true) && !$this->request->draft) ? 'publish' : 'waiting');
        
        $contents['title'] = $this->request->nil(_t('未命名文档'))->title;
        $contents['text'] = trim($contents['text']);
        $contents['created'] = isset($this->request->created) ? ($this->request->created - $this->options->timezone)
        : (isset($this->request->date) ? strtotime($this->request->date) - $this->options->timezone : $this->options->gmtTime);

        /** 提交数据的过滤 */
        $contents = $this->plugin()->update($contents);
        $updateRows = $this->update($contents, $this->db->sql()->where('cid = ?', $this->cid));

        if ($updateRows > 0) {
            /** 取出内容 */
            $this->db->fetchRow($this->select()->where('cid = ?', $this->cid)->limit(1), array($this, 'push'));
        
            /** 插入分类 */
            $this->setCategories($this->cid, !empty($contents['category']) && is_array($contents['category']) ? 
            $contents['category'] : array(), 'publish' == $contents['status']);
            
            /** 插入标签 */
            $this->setTags($this->cid, empty($contents['tags']) ? NULL : $contents['tags'], 'publish' == $contents['status']);
        }

        /** 文章提示信息 */
        if ('publish' == $contents['status']) {
            $this->widget('Widget_Notice')->set($updateRows > 0 ? 
            _t('文章 "<a href="%s">%s</a>" 已经被更新', $this->permalink, $this->title)
            : _t('文章提交失败'), NULL, $updateRows > 0 ? 'success' : 'error');
        } else if ('draft' == $contents['status']) {
            $this->widget('Widget_Notice')->set($updateRows > 0 ? 
            _t('草稿 "%s" 已经被保存', $this->title) :
            _t('草稿保存失败'), NULL, $updateRows > 0 ? 'success' : 'error');
        } else if ('waiting' == $contents['status']) {
            $this->widget('Widget_Notice')->set($updateRows > 0 ? 
            _t('文章 "%s" 等待审核', $this->title) :
            _t('文章提交失败'), NULL, $updateRows > 0 ? 'notice' : 'error');
        }

        /** 设置高亮 */
        $this->widget('Widget_Notice')->highlight($this->theId);

        /** 跳转页面 */
        if ('draft' == $contents['status']) {
            $this->response->redirect(Typecho_Common::url('write-post.php?cid=' . $this->cid, $this->options->adminUrl));
        } else {
            $this->response->redirect(Typecho_Common::url('manage-posts.php?status=' . $contents['status'], $this->options->adminUrl));
        }
    }
    
    /**
     * 删除文章
     * 
     * @access public
     * @return void
     */
    public function deletePost()
    {
        $cid = $this->request->filter('int')->cid;
        $deleteCount = 0;

        if ($cid) {
            /** 格式化文章主键 */
            $posts = is_array($cid) ? $cid : array($cid);
            foreach ($posts as $post) {
            
                $condition = $this->db->sql()->where('cid = ?', $post);
                
                if ($this->isWriteable($condition) && $this->delete($condition)) {
                    /** 删除分类 */
                    $this->setCategories($post, array(), 'post');
                    
                    /** 删除标签 */
                    $this->setTags($post, NULL, 'post');
                    
                    /** 删除评论 */
                    $this->db->query($this->db->delete('table.comments')
                    ->where('cid = ?', $post));
                    
                    $deleteCount ++;
                }
                
                unset($condition);
            }
        }
        
        /** 设置提示信息 */
        $this->widget('Widget_Notice')->set($deleteCount > 0 ? _t('文章已经被删除') : _t('没有文章被删除'), NULL,
        $deleteCount > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->response->goBack();
    }
    
    /**
     * 绑定动作
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $this->onRequest('do', 'insert')->insertPost();
        $this->onRequest('do', 'update')->updatePost();
        $this->onRequest('do', 'delete')->deletePost();
        $this->response->redirect($this->options->adminUrl);
    }
}
