<?php
/**
 * 文章提交管理
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入验证库支持 **/
require_once __TYPECHO_LIB_DIR__ . '/Validation.php';

/** 载入提交基类支持 **/
require_once __TYPECHO_WIDGET_DIR__ . '/Abstract/Contents.php';

/**
 * 内容处理类
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class DoEditPostWidget extends ContentsWidget
{
    /**
     * 提交文章
     * 
     * @access public
     * @return void
     */
    public function insertPost()
    {
        $contents = TypechoRequest::getParametersFrom('password', 'created', 'text', 'template',
        'allowComment', 'allowPing', 'allowFeed', 'slug', 'category', 'tags');
        $contents['type'] = (1 == TypechoRequest::getParameter('draft')) ? 'draft' : 'post';
        $contents['title'] = (NULL == TypechoRequest::getParameter('title')) ? 
        _t('未命名文档') : TypechoRequest::getParameter('title');
    
        $insertId = $this->insertContent($contents);
        
        if($insertId > 0)
        {
            /** 插入分类 */
            Typecho::widget('Abstract.Metas')->setCategories($insertId, !empty($contents['category']) && is_array($contents['category']) ? 
            $contents['category'] : array());
            
            /** 插入标签 */
            Typecho::widget('Abstract.Metas')->setTags($insertId, empty($contents['tags']) ? NULL : $contents['tags']);
        }
        
        $this->db->fetchRow($this->selectSql->group('table.contents.`cid`')
        ->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'post', 'draft')
        ->where('table.contents.`cid` = ?', $insertId)->limit(1), array($this, 'push'));
        
        /** 文章提示信息 */
        if('post' == $contents['type'])
        {
            Typecho::widget('Notice')->set($insertId > 0 ? 
            _t("文章 '<a href=\"%s\" target=\"_blank\">%s</a>' 已经被创建", $this->permalink, $this->title)
            : _t('文章提交失败'), NULL, $insertId > 0 ? 'success' : 'error');
        }
        else
        {
            Typecho::widget('Notice')->set($insertId > 0 ? 
            _t("草稿 '%s' 已经被保存", $this->title) :
            _t('草稿保存失败'), NULL, $insertId > 0 ? 'success' : 'error');
        }

        /** 跳转页面 */
        if(1 == TypechoRequest::getParameter('continue'))
        {
            Typecho::redirect(Typecho::pathToUrl('edit.php?cid=' . $this->cid, $this->options->adminUrl));
        }
        else
        {
            Typecho::redirect(Typecho::pathToUrl('post-list.php', $this->options->adminUrl));
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
        $validator = new TypechoValidation();
        $validator->addRule('cid', 'required', _t('文章不存在'));
        $validator->run(TypechoRequest::getParametersFrom('cid'));
        
        $post = $this->db->fetchRow($this->selectSql->group('table.contents.`cid`')
        ->where('table.contents.`type` = ? OR table.contents.`type` = ?', 'post', 'draft')
        ->where('table.contents.`cid` = ?', TypechoRequest::getParameter('cid'))
        ->limit(1), array($this, 'push'));
        
        if(!$post)
        {
            throw new TypechoWidgetException(_t('文章不存在'), TypechoException::NOTFOUND);
        }
    
        $contents = TypechoRequest::getParametersFrom('password', 'created', 'text', 'template',
        'allowComment', 'allowPing', 'allowFeed', 'slug', 'category', 'tags');
        $contents['type'] = (1 == TypechoRequest::getParameter('draft')) ? 'draft' : 'post';
        $contents['title'] = (NULL == TypechoRequest::getParameter('title')) ? 
        _t('未命名文档') : TypechoRequest::getParameter('title');
    
        $updateRows = $this->updateContent($contents, TypechoRequest::getParameter('cid'));

        if($updateRows > 0)
        {
            /** 插入分类 */
            Typecho::widget('Abstract.Metas')->setCategories($this->cid, !empty($contents['category']) && is_array($contents['category']) ? 
            $contents['category'] : array());
            
            /** 插入标签 */
            Typecho::widget('Abstract.Metas')->setTags($this->cid, empty($contents['tags']) ? NULL : $contents['tags']);
        }

        /** 文章提示信息 */
        if('post' == $this->type)
        {
            Typecho::widget('Notice')->set($updateRows > 0 ? 
            _t("文章 '<a href=\"%s\" target=\"_blank\">%s</a>' 已经被更新", $this->permalink, $this->title)
            : _t('文章提交失败'), NULL, $updateRows > 0 ? 'success' : 'error');
        }
        else
        {
            Typecho::widget('Notice')->set($updateRows > 0 ? 
            _t("草稿 '%s' 已经被保存", $this->title) :
            _t('草稿保存失败'), NULL, $updateRows > 0 ? 'success' : 'error');
        }

        /** 跳转页面 */
        if(1 == TypechoRequest::getParameter('continue'))
        {
            Typecho::redirect(Typecho::pathToUrl('edit.php?cid=' . $this->cid, $this->options->adminUrl));
        }
        else
        {
            Typecho::redirect(Typecho::pathToUrl('post-list.php', $this->options->adminUrl));
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
        $cid = TypechoRequest::getParameter('cid');
        $deleteCount = 0;
        
        if($cid)
        {
            /** 格式化文章主键 */
            $posts = is_array($cid) ? $cid : array($cid);
            foreach($posts as $post)
            {
                if($this->deleteContent($post))
                {
                    /** 删除分类 */
                    Typecho::widget('Abstract.Metas')->setCategories($post, array());
                    
                    /** 删除标签 */
                    Typecho::widget('Abstract.Metas')->setTags($post, NULL);
                    
                    /** 删除评论 */
                    $this->db->query($this->db->sql()->delete('table.comments')
                    ->where('`cid` = ?', $post));
                    
                    $deleteCount ++;
                }
            }
        }
        
        /** 设置提示信息 */
        Typecho::widget('Notice')->set($deleteCount > 0 ? _t('文章已经被删除') : _t('没有文章被删除'), NULL,
        $deleteCount > 0 ? 'success' : 'notice');
        
        /** 返回原网页 */
        $this->goBack();
    }

    /**
     * 入口函数,绑定请求事件
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $this->access->pass('contributor');
        TypechoRequest::bindParameter(array('do' => 'insert'), array($this, 'insertPost'));
        TypechoRequest::bindParameter(array('do' => 'update'), array($this, 'updatePost'));
        TypechoRequest::bindParameter(array('do' => 'delete'), array($this, 'deletePost'));
        Typecho::redirect($this->options->adminUrl);
    }
}
