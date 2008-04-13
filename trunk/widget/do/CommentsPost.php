<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入验证库支持 **/
require_once __TYPECHO_LIB_DIR__ . '/Validation.php';

/** 载入提交基类支持 **/
require_once 'Post.php';

/**
 * 评论提交
 * 
 * @package Widget
 * @todo 增加邮件和个人主页格式判断
 */
class CommentsPost extends Post
{    
    /**
     * 插入评论
     * 
     * @access public
     * @param integer $cid
     * @return void
     */
    public function insertComment($cid)
    {
        $comment = array();
        $comment['cid'] = $cid;
        $comment['created'] = widget('Options')->gmtTime;
        $comment['agent'] = $_SERVER["HTTP_USER_AGENT"];
        $comment['ip'] = typechoGetClientIp();
        $comment['type'] = 'comment';
        
        //判断父节点
        if($parentId = TypechoRequest::getParameter('parent'))
        {
            $parentId = intval($parentId);
            if($parent = $this->db->fetchRow($this->db->sql()->select('table.comments', 'coid')
            ->where('coid = ?', $parentId)))
            {
                if($cid == $parent['cid'])
                {
                    $comment['parent'] = $parentId;
                }
            }

            throw new TypechoWidgetException(_t('父级评论不存在'));
        }
        
        //检验格式
        $validator = new TypechoValidation();
        $validator->addRule('author', 'required', _t('必须填写用户名'));
        
        if(widget('Options')->commentsRequireMail)
        {
            $validator->addRule('mail', 'required', _t('必须填写电子邮箱地址'));
        }
        
        $validator->addRule('mail', 'email', _t('邮箱地址不合法'));
        
        if(widget('Options')->commentsRequireURL)
        {
            $validator->addRule('url', 'required', _t('必须填写个人主页'));
        }
        
        $validator->addRule('url', 'url', _t('个人主页地址不合法'));
        $validator->addRule('text', 'required', _t('必须填写评论内容'));
        
        $message = $validator->run(TyepchoRequest::getParametersFrom('author', 'mail', 'url', 'text'));
        
        $comment['author'] = TypechoRequest::getParameter('author');
        $comment['mail'] = TypechoRequest::getParameter('mail');
        $comment['url'] = TypechoRequest::getParameter('url');
        $comment['text'] = TypechoRequest::getParameter('text');
        
        TypechoRequest::setCookie('author', $comment['author']);
        TypechoRequest::setCookie('mail', $comment['mail']);
        TypechoRequest::setCookie('url', $comment['url']);
        TypechoRequest::setCookie('text', $comment['text']);
        
        //添加过滤器
        $filterName = TypechoPlugin::name(__FILE__);
        TypechoPlugin::callFilter($filterName, $comment);

        //添加评论
        $commentId = $this->query($this->db
        ->sql
        ->insert('table.comments')
        ->rows($comment));
        
        TypechoRequest::deleteCookie('text');
        $this->goBack('#comment-' . $commentId);
    }
    
    /**
     * 评论提交入口
     * 
     * @access public
     * @return void
     * @throws TypechoWidgetException
     */
    public function render()
    {
        //判断来源
        if(empty($_SERVER['HTTP_REFERER']) || 0 === strpos($_SERVER['HTTP_REFERER'], widget('Options')->index))
        {
            throw new TypechoWidgetException(_t('来源页不合法'));
        }
    
        //判断字段
        if(!empty($_SERVER['QUERY_STRING']) && 2 == count($query = explode('.', $_SERVER['QUERY_STRING'])))
        {
            list($cid, $created) = $query;
            
            if($post = $this->db->fetchRow($this->db->sql()->select('table.contents', 'created')
            ->where('cid = ?', intval($cid))
            ->limit(1)))
            {
                if($created == $post['created'])
                {
                    $this->insertComment(intval($cid));
                    return;
                }
            }
        }
        
        throw new TypechoWidgetException(_t('被评论的文章不存在'));
    }
}
