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
 * @package CommentsPost
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
        $comment['created'] = $this->registry('Options')->gmtTime;
        $comment['agent'] = $_SERVER["HTTP_USER_AGENT"];
        $comment['ip'] = typechoGetClientIp();
        $comment['type'] = 'comment';
        
        //初始化验证规则
        $rules = array();
        
        //判断用户
        if($this->registry('Access')->hasLogin())
        {
            $comment['author'] = $this->registry('Access')->user('screenName');
            $comment['mail'] = $this->registry('Access')->user('mail');
            $comment['url'] = $this->registry('Access')->user('url');
            
            setCookie('author', $comment['author'], 0, typechoGetSiteRoot());
            setCookie('mail', $comment['mail'], 0, typechoGetSiteRoot());
            setCookie('url', $comment['url'], 0, typechoGetSiteRoot());
        }
        else
        {
            //判断作者
            if(empty($_POST['author']))
            {
                throw new TypechoWidgetException(_t('必须填写用户名'));
            }
            else
            {
                $comment['author'] = $_POST['author'];
                setCookie('author', $comment['author'], 0, typechoGetSiteRoot());
            }
            
            //判断电子邮箱
            if(empty($_POST['mail']))
            {
                if($this->registry('Options')->commentsRequireMail)
                {
                    throw new TypechoWidgetException(_t('必须填写电子邮箱地址'));
                }
            }
            else
            {
                $comment['mail'] = $_POST['mail'];
                setCookie('mail', $comment['mail'], 0, typechoGetSiteRoot());
                $rules['mail'] = array('email' => _t('邮箱地址不合法'));
            }
            
            //判断个人主页
            if(empty($_POST['url']))
            {
                if($this->registry('Options')->commentsRequireUrl)
                {
                    throw new TypechoWidgetException(_t('必须填写电子邮箱地址'));
                }
            }
            else
            {
                $comment['url'] = $_POST['url'];
                setCookie('url', $comment['url'], 0, typechoGetSiteRoot());
                $rules['url'] = array('url' => _t('个人主页地址不合法'));
            }
        }
        
        //判断内容
        if(empty($_POST['text']))
        {
            throw new TypechoWidgetException(_t('必须填写评论内容'));
        }
        else
        {
            $comment['text'] = $_POST['text'];
            setCookie('text', $comment['text'], 0, typechoGetSiteRoot());
        }
        
        //判断父节点
        if(!empty($_POST['parent']))
        {
            if($parent = $this->db->fetchRow($this->db->sql()->select('table.comments', 'coid')
            ->where('coid = ?', intval($_POST['parent']))))
            {
                if($cid == $parent['cid'])
                {
                    $comment['parent'] = intval($_POST['parent']);
                }
            }

            throw new TypechoWidgetException(_t('父级评论不存在'));
        }
        
        //添加钩子
        $hookName = TypechoWidgetHook::name(__FILE__);
        $commentRows = TypechoWidgetHook::call($hookName, $comment);
        
        if($commentRows)
        {
            array_unshift($commentRows, $comment);
            $comment = call_user_func_array('array_merge', $commentRows);
        }
        
        //检验格式
        if($rules)
        {
            $validator = new TypechoValidation();
            $error = $validator->run($comment, $rules);
            if($error)
            {
                throw new TypechoWidgetException($error);
            }
        }

        //添加评论
        $commentId = $this->query($this->db
        ->sql
        ->insert('table.comments')
        ->rows($comment));
        
        setCookie('text', '', 0, typechoGetSiteRoot());
        $this->goBack('#comment-' . $commentId);
    }
    
    public function render()
    {
        //判断来源
        if(empty($_SERVER['HTTP_REFERER']) || 0 === strpos($_SERVER['HTTP_REFERER'], $this->registry('Options')->index))
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
