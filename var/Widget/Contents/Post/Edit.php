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
     * 获取当前时间
     * 
     * @access protected
     * @return Typecho_Date
     */
    protected function ___date()
    {
        return new Typecho_Date($this->options->gmtTime);
    }
    
    /**
     * 根据提交值获取created字段值
     * 
     * @access protected
     * @return integer
     */
    protected function getCreated()
    {
        $created = $this->options->gmtTime;
        if (isset($this->request->created)) {
            $created = $this->request->created;
        } else if (isset($this->request->date)) {
            $created = strtotime($this->request->date) - $this->options->timezone + $this->options->serverTimezone;
        } else if (isset($this->request->year) && isset($this->request->month) && isset($this->request->day)) {
            $second = intval($this->request->get('sec', date('s')));
            $min = intval($this->request->get('min', date('i')));
            $hour = intval($this->request->get('hour', date('H')));
            
            $year = intval($this->request->year);
            $month = intval($this->request->month);
            $day = intval($this->request->day);
            
            $created = mktime($hour, $min, $second, $month, $day, $year) - $this->options->timezone + $this->options->serverTimezone;
        }
        
        return $created;
    }
    
    /**
     * 同步附件
     * 
     * @access protected
     * @param integer $cid 内容id
     * @return void
     */
    protected function attach($cid)
    {
        if ($this->request->attachment && is_array($this->request->attachment)) {
            $attachments = $this->request->filter('int')->attachment;
            
            foreach ($attachments as $attachment) {
                $this->db->query($this->db->update('table.contents')->rows(array('parent' => $cid, 'status' => 'publish',
                'order' => $key + 1))->where('cid = ? AND type = ?', $attachment, 'attachment'));
            }
        }
    }
    
    /**
     * 取消附件关联
     * 
     * @access protected
     * @param integer $cid 内容id
     * @return void
     */
    protected function unAttach($cid)
    {
        $this->db->query($this->db->update('table.contents')->rows(array('parent' => 0, 'status' => 'publish'))
                ->where('parent = ? AND type = ?', $cid, 'attachment'));
    }
    
    /**
     * 获取页面偏移的URL Query
     * 
     * @access protected
     * @param integer $created 创建时间
     * @param string $status 状态
     * @return string
     */
    protected function getPageOffsetQuery($created, $status)
    {
        return 'page=' . $this->getPageOffset('created', $created, 'post', $status,
        'on' == $this->request->__typecho_all_posts ? 0 : $this->user->uid);
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
            $this->db->fetchRow($this->select()
            ->where('table.contents.type = ?', 'post')
            ->where('table.contents.cid = ?', $this->request->filter('int')->cid)
            ->limit(1), array($this, 'push'));
            
            if (!$this->have()) {
                throw new Typecho_Widget_Exception(_t('文章不存在'), 404);
            } else if ($this->have() && !$this->allow('edit')) {
                throw new Typecho_Widget_Exception(_t('没有编辑权限'), 403);
            }
        }
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
            echo date($format, $this->options->gmtTime + $this->options->timezone - $this->options->serverTimezone);
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
    public function setTags($cid, $tags, $beforeCount = true, $afterCount = true)
    {
        $tags = str_replace('，', ',', $tags);
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
                
                if ($beforeCount) {
                    $this->db->query($this->db->update('table.metas')
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
                
                if ($afterCount) {
                    $this->db->query($this->db->update('table.metas')
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
    public function setCategories($cid, array $categories, $beforeCount = true, $afterCount = true)
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
                
                if ($beforeCount) {
                    $this->db->query($this->db->update('table.metas')
                    ->expression('count', 'count - 1')
                    ->where('mid = ?', $category));
                }
            }
        }
        
        /** 插入category */
        if ($categories) {
            foreach ($categories as $category) {
                /** 如果分类不存在 */
                if (!$this->db->fetchRow($this->db->select('mid')
                ->from('table.metas')
                ->where('mid = ?', $category)
                ->limit(1))) {
                    continue;
                }
            
                $this->db->query($this->db->insert('table.relationships')
                ->rows(array(
                    'mid'  =>   $category,
                    'cid'  =>   $cid
                )));
                
                if ($afterCount) {
                    $this->db->query($this->db->update('table.metas')
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
        $contents = $this->request->from('password', 'allowComment',
        'allowPing', 'allowFeed', 'slug', 'category', 'tags', 'status', 'text');
        $contents['type'] = 'post';
        $contents['status'] = $this->request->draft ? 'draft' :
        (($this->user->pass('editor', true) && !$this->request->draft) ? 'publish' : 'waiting');
        
        $contents['title'] = $this->request->get('title', _t('未命名文档'));
        $contents['created'] = $this->getCreated();

        /** 提交数据的过滤 */
        $contents = $this->pluginHandle()->insert($contents);
        $insertId = $this->insert($contents);
        
        if ($insertId > 0) {
            /** 插入分类 */
            $this->setCategories($insertId, !empty($contents['category']) && is_array($contents['category']) ? 
            $contents['category'] : array($this->options->defaultCategory), false, 'publish' == $contents['status']);
            
            /** 插入标签 */
            $this->setTags($insertId, empty($contents['tags']) ? NULL : $contents['tags'],
            false, 'publish' == $contents['status']);
            
            /** 同步附件 */
            $this->attach($insertId);
        }
        
        $this->db->fetchRow($this->select()->where('table.contents.cid = ?', $insertId)->limit(1), array($this, 'push'));
        
        /** 发送ping */
        $trackback = array_unique(preg_split("/(\r|\n|\r\n)/", trim($this->request->trackback)));
        $this->widget('Widget_Service')->sendPing($this->cid, $trackback);
        
        if ($this->request->isAjax()) {
            if ($insertId > 0) {
                $created = new Typecho_Date($this->options->gmtTime);
                $this->response->throwJson(array(
                    'success'  =>  1,
                    'message'  =>  _t('文章保存于 %s', $created->format('H:i A')),
                    'cid'      =>  $insertId
                ));
            } else {
                $this->response->throwJson(array(
                    'success'  =>  0,
                    'message'  =>  _t('文章保存失败')
                ));
            }
        } else {
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
            
            /** 获取页面偏移 */
            $pageQuery = $this->getPageOffsetQuery($this->created, $this->status);

            /** 跳转页面 */
            if ('draft' == $contents['status']) {
                $this->response->redirect(Typecho_Common::url('write-post.php?cid=' . $insertId, $this->options->adminUrl));
            } else {
                $this->response->redirect(Typecho_Common::url('manage-posts.php?status=' . $contents['status'] .
                '&' . $pageQuery, $this->options->adminUrl));
            }
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
        $contents = $this->request->from('password', 'allowComment',
        'allowPing', 'allowFeed', 'slug', 'category', 'tags', 'text');
        $contents['type'] = 'post';
        $contents['status'] = $this->request->draft ? 'draft' :
        (($this->user->pass('editor', true) && !$this->request->draft) ? 'publish' : 'waiting');
        
        $contents['title'] = $this->request->get('title', _t('未命名文档'));
        $contents['created'] = $this->getCreated();

        /** 提交数据的过滤 */
        $contents = $this->pluginHandle()->update($contents);
        $updateRows = $this->update($contents, $this->db->sql()->where('cid = ?', $this->cid));

        if ($updateRows > 0) {
            /** 插入分类 */
            $this->setCategories($this->cid, !empty($contents['category']) && is_array($contents['category']) ? 
            $contents['category'] : array($this->options->defaultCategory), 'publish' == $this->status, 'publish' == $contents['status']);
            
            /** 插入标签 */
            $this->setTags($this->cid, empty($contents['tags']) ? NULL : $contents['tags'],
            'publish' == $this->status, 'publish' == $contents['status']);
            
            /** 取出已修改的文章 */
            $this->db->fetchRow($this->select()->where('table.contents.cid = ?', $this->cid)->limit(1), array($this, 'push'));
            
            /** 同步附件 */
            $this->attach($this->cid);
        }
        
        /** 发送ping */
        $trackback = array_unique(preg_split("/(\r|\n|\r\n)/", trim($this->request->trackback)));
        $this->widget('Widget_Service')->sendPing($this->cid, $trackback);
        
        if ($this->request->isAjax()) {
            if ($updateRows > 0) {
                $created = new Typecho_Date($this->options->gmtTime);
                $this->response->throwJson(array(
                    'success'  =>  1,
                    'message'  =>  _t('文章保存于 %s', $created->format('H:i A')),
                    'cid'      =>  $this->cid
                ));
            } else {
                $this->response->throwJson(array(
                    'success'  =>  0,
                    'message'  =>  _t('文章保存失败')
                ));
            }
        } else {
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
            
            /** 获取页面偏移 */
            $pageQuery = $this->getPageOffsetQuery($this->created, $this->status);

            /** 跳转页面 */
            if ('draft' == $contents['status']) {
                $this->response->redirect(Typecho_Common::url('write-post.php?cid=' . $this->cid, $this->options->adminUrl));
            } else {
                $this->response->redirect(Typecho_Common::url('manage-posts.php?status=' . $contents['status'] .
                '&' . $pageQuery, $this->options->adminUrl));
            }
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
                    
                    /** 解除附件关联 */
                    $this->unAttach($post);
                    
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
        $this->on($this->request->is('do=insert'))->insertPost();
        $this->on($this->request->is('do=update'))->updatePost();
        $this->on($this->request->is('do=delete'))->deletePost();
        $this->response->redirect($this->options->adminUrl);
    }
}
