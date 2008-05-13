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
require_once 'ContentsPost.php';

/**
 * 内容处理类
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class DoEditPostWidget extends ContentsPostWidget
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
        $contents['cid'] = $insertId;
        
        /** 文章提示信息 */
        if('post' == $contents['type'])
        {
            Typecho::widget('Notice')->set($insertId > 0 ? 
            _t('文章<a href="%s" target="_blank">%s</a>已经被创建',
            TypechoRoute::parse('post', $contents, Typecho::widget('Options')->index),
            $contents['title']) : _t('文章提交失败'), NULL, $insertId > 0 ? 'success' : 'error');
        }
        else
        {
            Typecho::widget('Notice')->set($insertId > 0 ? 
            _t('草稿%s已经被保存', $contents['title']) :
            _t('草稿保存失败'), NULL, $insertId > 0 ? 'success' : 'error');
        }

        /** 跳转页面 */
        if(1 == TypechoRequest::getParameter('continue'))
        {
            Typecho::redirect(Typecho::pathToUrl('edit.php?cid=' . $insertId, Typecho::widget('Options')->adminUrl));
        }
        else
        {
            Typecho::redirect(Typecho::pathToUrl('post-list.php', Typecho::widget('Options')->adminUrl));
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
        Typecho::widget('Access')->pass('contributor');
        TypechoRequest::bindParameter(array('do' => 'insert'), array($this, 'insertPost'));
        TypechoRequest::bindParameter(array('do' => 'delete'), array($this, 'deletePost'));
        Typecho::redirect(Typecho::widget('Options')->adminUrl);
    }
}
