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
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        /** 必须为贡献者以上权限 */
        $this->user->pass('contributor');
    
        /** 获取文章内容 */
        if ($this->request->cid || 'update' == $this->request->do) {
            $post = $this->db->fetchRow($this->select()
            ->where('table.contents.type = ? OR table.contents.type = ? OR table.contents.type = ?', 'post', 'draft', 'waiting')
            ->where('table.contents.cid = ?', $this->request->cid)
            ->limit(1), array($this, 'push'));
            
            if (!$post) {
                $this->response->throwExceptionResponseByCode(_t('文章不存在'), 404);
            } else if ($post && 'update' == $this->request->do && !$this->postIsWriteable()) {
                $this->response->throwExceptionResponseByCode(_t('没有编辑权限'), 403);
            }
        }
    }
    
    /**
     * 获取网页标题
     * 
     * @access public
     * @return string
     */
    public function getMenuTitle()
    {
        return _t('编辑 "%s"', $this->title);
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
        $insertTags = $this->getTags($tags);
        
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
     * 根据tag获取ID
     * 
     * @access public
     * @param array $tags
     * @return array
     */
    public function getTags(array $tags)
    {
        $result = array();
        foreach ($tags as $tag) {
            if (empty($tag)) {
                continue;
            }
        
            $row = $this->db->fetchRow($this->db->select('mid')
            ->from('table.metas')
            ->where('name = ?', $tag)->limit(1));
            
            if ($row) {
                $result[] = $row['mid'];
            } else {
                $result[] = 
                $this->db->query($this->db->insert('table.metas')
                ->rows(array(
                    'name'  =>  $tag,
                    'slug'  =>  $tag,
                    'type'  =>  'tag',
                    'count' =>  0,
                    'sort'  =>  0,
                )));
            }
        }
        
        return $result;
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
        $contents = $this->request->from('password', 'created', 'text', 'template',
        'allowComment', 'allowPing', 'allowFeed', 'slug', 'category', 'tags');
        $contents['type'] = ('draft' == $this->request->type) ? 'draft' :
        (($this->user->pass('editor', true) && 'post' == $this->request->type) ? 'post' : 'waiting');
        $contents['title'] = isset($this->request->title) ? _t('未命名文档') : $this->request->title;
        $contents['created'] = isset($this->request->created) ? $this->request->created - $this->options->timezone : $this->options->gmtTime;
    
        $insertId = $this->insert($contents);
        
        if ($insertId > 0) {
            /** 插入分类 */
            $this->setCategories($insertId, !empty($contents['category']) && is_array($contents['category']) ? 
            $contents['category'] : array(), 'post' == $contents['type']);
            
            /** 插入标签 */
            $this->setTags($insertId, empty($contents['tags']) ? NULL : $contents['tags'], 'post' == $contents['type']);
        }
        
        $this->db->fetchRow($this->select()->where('table.contents.cid = ?', $insertId)->limit(1), array($this, 'push'));
        
        /** 文章提示信息 */
        if ('post' == $contents['type']) {
            $this->widget('Widget_Notice')->set($insertId > 0 ? 
            _t('文章 "<a href="%s">%s</a>" 已经被创建', $this->permalink, $this->title)
            : _t('文章提交失败'), NULL, $insertId > 0 ? 'success' : 'error');
        } else if ('draft' == $contents['type']) {
            $this->widget('Widget_Notice')->set($insertId > 0 ? 
            _t('草稿 "%s" 已经被保存', $this->title) :
            _t('草稿保存失败'), NULL, $insertId > 0 ? 'success' : 'error');
        } else if ('waiting' == $contents['type']) {
            $this->widget('Widget_Notice')->set($insertId > 0 ? 
            _t('文章 "%s" 等待审核', $this->title) :
            _t('文章提交失败'), NULL, $insertId > 0 ? 'notice' : 'error');
        }

        /** 跳转页面 */
        if ('draft' == $contents['type']) {
            $this->response->redirect(Typecho_Common::url('write-page.php?cid=' . $this->cid, $this->options->adminUrl));
        } else {
            $this->response->redirect(Typecho_Common::url('manage-posts.php', $this->options->adminUrl));
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
        $contents = $this->request->from('password', 'created', 'text', 'template',
        'allowComment', 'allowPing', 'allowFeed', 'slug', 'category', 'tags');
        $contents['type'] = ('draft' == $this->request->type) ? 'draft' :
        (($this->user->pass('editor', true) && 'post' == $this->request->type) ? 'post' : 'waiting');
        $contents['title'] = isset($this->request->title) ? _t('未命名文档') : $this->request->title;
        $contents['created'] = isset($this->request->created) ? $this->request->created - $this->options->timezone : $this->options->gmtTime;
    
        $updateRows = $this->update($contents, $this->db->sql()->where('cid = ?', $this->request->cid));

        if ($updateRows > 0) {
            /** 取出内容 */
            $this->db->fetchRow($this->select()->where('cid = ?', $this->request->cid)->limit(1), array($this, 'push'));
        
            /** 插入分类 */
            $this->setCategories($this->cid, !empty($contents['category']) && is_array($contents['category']) ? 
            $contents['category'] : array(), 'post' == $contents['type']);
            
            /** 插入标签 */
            $this->setTags($this->cid, empty($contents['tags']) ? NULL : $contents['tags'], 'post' == $contents['type']);
        }

        /** 文章提示信息 */
        if ('post' == $this->type) {
            $this->widget('Widget_Notice')->set($updateRows > 0 ? 
            _t('文章 "<a href="%s">%s</a>" 已经被更新', $this->permalink, $this->title)
            : _t('文章提交失败'), NULL, $updateRows > 0 ? 'success' : 'error');
        } else if ('draft' == $contents['type']) {
            $this->widget('Widget_Notice')->set($updateRows > 0 ? 
            _t('草稿 "%s" 已经被保存', $this->title) :
            _t('草稿保存失败'), NULL, $updateRows > 0 ? 'success' : 'error');
        } else if ('waiting' == $contents['type']) {
            $this->widget('Widget_Notice')->set($updateRows > 0 ? 
            _t('文章 "%s" 等待审核', $this->title) :
            _t('文章提交失败'), NULL, $updateRows > 0 ? 'notice' : 'error');
        }

        /** 跳转页面 */
        if ('draft' == $contents['type']) {
            $this->response->redirect(Typecho_Common::url('write-page.php?cid=' . $this->cid, $this->options->adminUrl));
        } else {
            $this->response->redirect(Typecho_Common::url('manage-posts.php', $this->options->adminUrl));
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
        $cid = $this->request->cid;
        $deleteCount = 0;

        if ($cid) {
            /** 格式化文章主键 */
            $posts = is_array($cid) ? $cid : array($cid);
            foreach ($posts as $post) {
            
                $condition = $this->db->sql()->where('cid = ?', $post);
                
                if ($this->postIsWriteable($condition) && $this->delete($condition)) {
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
