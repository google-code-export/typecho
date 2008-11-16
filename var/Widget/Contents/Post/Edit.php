<?php
/**
 * 编辑文章
 * 
 * @category typecho
 * @package default
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
class Widget_Contents_Post_Edit extends Widget_Abstract_Contents implements Widget_Interface_Action_Widget
{
    /**
     * 构造函数
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        Typecho_API::factory('Widget_Users_Current')->pass('contributor');
    
        /** 获取文章内容 */
        if (Typecho_Request::getParameter('cid')) {
            $this->db->fetchRow($this->select()->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'post', 'draft')
            ->where('table.contents.`cid` = ?', Typecho_Request::getParameter('cid'))
            ->limit(1), array($this, 'push'));
            Typecho_API::factory('Widget_Menu')->title = _t('编辑文章');
        }
    }
    
    /**
     * 获取创建GMT时间戳
     * 
     * @access public
     * @return integer
     */
    public function getCreated()
    {
        if (!($date = Typecho_Request::getParameter('date'))) {
            $date = date('Y-m-d');
        }
        
        if (!($time = Typecho_Request::getParameter('time'))) {
            $time = date('g:i A');
        }
        
        return strtotime($date . ' ' . $time) - $this->options->timezone;
    }
    
    /**
     * 设置内容标签
     * 
     * @access public
     * @param integer $cid
     * @param string $tags
     * @param string $type 参与计数的类型
     * @return string
     */
    public function setTags($cid, $tags, $type = 'post')
    {
        $tags = str_replace(array(' ', '，', ' '), ',', $tags);
        $tags = array_unique(array_map('trim', explode(',', $tags)));

        /** 取出已有tag */
        $existTags = Typecho_API::arrayFlatten($this->db->fetchAll(
        $this->db->sql()->select('table.metas', 'table.metas.`mid`')
        ->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $cid)
        ->where('table.metas.`type` = ?', 'tag')
        ->group('table.metas.`mid`')), 'mid');
        
        /** 删除已有tag */
        if ($existTags) {
            foreach ($existTags as $tag) {
                $this->db->query($this->db->sql()->delete('table.relationships')
                ->where('`cid` = ?', $cid)
                ->where('`mid` = ?', $tag));
                
                $num = $this->db->fetchObject($this->db->sql()
                ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->join('table.contents', 'table.contents.`cid` = table.relationships.`cid`')
                ->where('table.contents.`type` = ?', $type )
                ->where('table.relationships.`mid` = ?', $tag))->num;
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $num)
                ->where('`mid` = ?', $tag));
            }
        }
        
        /** 取出插入tag */
        $insertTags = $this->getTags($tags);
        
        /** 插入tag */
        if ($insertTags) {
            foreach ($insertTags as $tag) {
                $this->db->query($this->db->sql()->insert('table.relationships')
                ->rows(array(
                    'mid'  =>   $tag,
                    'cid'  =>   $cid
                )));
                
                $num = $this->db->fetchObject($this->db->sql()
                ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->join('table.contents', 'table.contents.`cid` = table.relationships.`cid`')
                ->where('table.contents.`type` = ?', $type )
                ->where('table.relationships.`mid` = ?', $tag))->num;
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $num)
                ->where('`mid` = ?', $tag));
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
        
            $row = $this->db->fetchRow($this->db->sql()->select('table.metas', '`mid`')
            ->where('`name` = ?', $tag)->limit(1));
            
            if ($row) {
                $result[] = $row['mid'];
            } else {
                $result[] = 
                $this->db->query($this->db->sql()->insert('table.metas')
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
     * @param integer $cid
     * @param array $categories
     * @param string $type 参与计数的类型
     * @return integer
     */
    public function setCategories($cid, array $categories, $type = 'post')
    {
        $categories = array_unique(array_map('trim', $categories));

        /** 取出已有category */
        $existCategories = Typecho_API::arrayFlatten($this->db->fetchAll(
        $this->db->sql()->select('table.metas', 'table.metas.`mid`')
        ->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $cid)
        ->where('table.metas.`type` = ?', 'category')
        ->group('table.metas.`mid`')), 'mid');
        
        /** 删除已有category */
        if ($existCategories) {
            foreach ($existCategories as $category) {
                $this->db->query($this->db->sql()->delete('table.relationships')
                ->where('`cid` = ?', $cid)
                ->where('`mid` = ?', $category));
                
                $num = $this->db->fetchObject($this->db->sql()
                ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->join('table.contents', 'table.contents.`cid` = table.relationships.`cid`')
                ->where('table.contents.`type` = ?', $type )
                ->where('table.relationships.`mid` = ?', $category))->num;
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $num)
                ->where('`mid` = ?', $category));
            }
        }
        
        /** 插入category */
        if ($categories) {
            foreach ($categories as $category) {
                $this->db->query($this->db->sql()->insert('table.relationships')
                ->rows(array(
                    'mid'  =>   $category,
                    'cid'  =>   $cid
                )));
                
                $num = $this->db->fetchObject($this->db->sql()
                ->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->join('table.contents', 'table.contents.`cid` = table.relationships.`cid`')
                ->where('table.contents.`type` = ?', $type )
                ->where('table.relationships.`mid` = ?', $category))->num;
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $num)
                ->where('`mid` = ?', $category));
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
        $contents = Typecho_Request::getParametersFrom('password', 'created', 'text', 'template',
        'allowComment', 'allowPing', 'allowFeed', 'slug', 'category', 'tags');
        $contents['type'] = (1 == Typecho_Request::getParameter('draft') || !Typecho_API::factory('Widget_Users_Current')->pass('editor', true)) ? 'draft' : 'post';
        $contents['title'] = (NULL == Typecho_Request::getParameter('title')) ? 
        _t('未命名文档') : Typecho_Request::getParameter('title');
        $contents['created'] = $this->getCreated();
    
        $insertId = $this->insert($contents);
        
        if ($insertId > 0) {
            /** 插入分类 */
            $this->setCategories($insertId, !empty($contents['category']) && is_array($contents['category']) ? 
            $contents['category'] : array(), 'post');
            
            /** 插入标签 */
            $this->setTags($insertId, empty($contents['tags']) ? NULL : $contents['tags'], 'post');
        }
        
        $this->db->fetchRow($this->select()->group('table.contents.`cid`')
        ->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'post', 'draft')
        ->where('table.contents.`cid` = ?', $insertId)->limit(1), array($this, 'push'));
        
        /** 文章提示信息 */
        if ('post' == $contents['type']) {
            Typecho_API::factory('Widget_Notice')->set($insertId > 0 ? 
            _t("文章 '<a href=\"%s\" target=\"_blank\">%s</a>' 已经被创建", $this->permalink, $this->title)
            : _t('文章提交失败'), NULL, $insertId > 0 ? 'success' : 'error');
        } else {
            Typecho_API::factory('Widget_Notice')->set($insertId > 0 ? 
            _t("草稿 '%s' 已经被保存", $this->title) :
            _t('草稿保存失败'), NULL, $insertId > 0 ? 'success' : 'error');
        }

        /** 跳转页面 */
        if (1 == Typecho_Request::getParameter('continue')) {
            Typecho_API::redirect(Typecho_API::pathToUrl('edit.php?cid=' . $this->cid, $this->options->adminUrl));
        } else {
            Typecho_API::redirect(Typecho_API::pathToUrl('post-list.php', $this->options->adminUrl));
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
        $validator = new Typecho_Validate();
        $validator->addRule('cid', 'required', _t('文章不存在'));
        $validator->run(Typecho_Request::getParametersFrom('cid'));
        
        $select = $this->select()->group('table.contents.`cid`')
        ->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'post', 'draft')
        ->where('table.contents.`cid` = ?', Typecho_Request::getParameter('cid'))
        ->limit(1);
        
        $exists = $this->db->fetchRow($select);
        
        if (!$exists) {
            throw new Typecho_Widget_Exception(_t('文章不存在'), Typecho_Exception::NOTFOUND);
        }
    
        $contents = Typecho_Request::getParametersFrom('password', 'created', 'text', 'template',
        'allowComment', 'allowPing', 'allowFeed', 'slug', 'category', 'tags');
        $contents['type'] = (1 == Typecho_Request::getParameter('draft') || !Typecho_API::factory('Widget_Users_Current')->pass('editor', true)) ? 'draft' : 'post';
        $contents['title'] = (NULL == Typecho_Request::getParameter('title')) ? 
        _t('未命名文档') : Typecho_Request::getParameter('title');
        $contents['created'] = $this->getCreated();
    
        $updateRows = $this->update($contents, $this->db->sql()->where('`cid` = ?', Typecho_Request::getParameter('cid')));
        $this->db->fetchRow($select, array($this, 'push'));

        if ($updateRows > 0) {
            /** 插入分类 */
            $this->setCategories($this->cid, !empty($contents['category']) && is_array($contents['category']) ? 
            $contents['category'] : array(), 'post');
            
            /** 插入标签 */
            $this->setTags($this->cid, empty($contents['tags']) ? NULL : $contents['tags'], 'post');
        }

        /** 文章提示信息 */
        if ('post' == $this->type) {
            Typecho_API::factory('Widget_Notice')->set($updateRows > 0 ? 
            _t("文章 '<a href=\"%s\" target=\"_blank\">%s</a>' 已经被更新", $this->permalink, $this->title)
            : _t('文章提交失败'), NULL, $updateRows > 0 ? 'success' : 'error');
        } else {
            Typecho_API::factory('Widget_Notice')->set($updateRows > 0 ? 
            _t("草稿 '%s' 已经被保存", $this->title) :
            _t('草稿保存失败'), NULL, $updateRows > 0 ? 'success' : 'error');
        }

        /** 跳转页面 */
        if (1 == Typecho_Request::getParameter('continue')) {
            Typecho_API::redirect(Typecho_API::pathToUrl('edit.php?cid=' . $this->cid, $this->options->adminUrl));
        } else {
            Typecho_API::redirect(Typecho_API::pathToUrl('post-list.php', $this->options->adminUrl));
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
        $cid = Typecho_Request::getParameter('cid');
        $deleteCount = 0;

        if ($cid) {
            /** 格式化文章主键 */
            $posts = is_array($cid) ? $cid : array($cid);
            foreach ($posts as $post) {
                if ($this->delete($this->db->sql()->where('`cid` = ?', $post))) {
                    /** 删除分类 */
                    $this->setCategories($post, array(), 'post');
                    
                    /** 删除标签 */
                    $this->setTags($post, NULL, 'post');
                    
                    /** 删除评论 */
                    $this->db->query($this->db->sql()->delete('table.comments')
                    ->where('`cid` = ?', $post));
                    
                    $deleteCount ++;
                }
            }
        }
        
        /** 设置提示信息 */
        Typecho_API::factory('Widget_Notice')->set($deleteCount > 0 ? _t('文章已经被删除') : _t('没有文章被删除'), NULL,
        $deleteCount > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        Typecho_API::goBack();
    }
    
    /**
     * 绑定动作
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        Typecho_Request::bindParameter(array('do' => 'insert'), array($this, 'insertPost'));
        Typecho_Request::bindParameter(array('do' => 'update'), array($this, 'updatePost'));
        Typecho_Request::bindParameter(array('do' => 'delete'), array($this, 'deletePost'));
        Typecho_API::redirect($this->options->adminUrl);
    }
}
