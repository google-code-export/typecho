<?php
/**
 * 反馈提交
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 评论基类 */
require_once 'Abstract/Comments.php';

/**
 * 反馈提交组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class FeedbackWidget extends CommentsWidget
{
    /**
     * 内容组件
     * 
     * @access private
     * @var TypechoWidget
     */
    private $content;

    /**
     * 评论处理函数
     * 
     * @access private
     * @return void
     */
    private function comment()
    {
        $comment = array(
            'created'   =>  $this->options->gmtTime,
            'agent'     =>  $_SERVER["HTTP_USER_AGENT"],
            'ip'        =>  TypechoRequest::getClientIp(),
            'type'      =>  'comment',
            'status'    =>  'approved'
        );
    
        /** 判断父节点 */
        if($parentId = TypechoRequest::getParameter('parent'))
        {
            if(($parent = $this->db->fetchRow($this->db->sql()->select('table.comments', '`coid`')
            ->where('`coid` = ?', $parentId))) && $this->content->cid == $parent['cid'])
            {
                $comment['parent'] = $parentId;
            }
            else
            {
                throw new TypechoWidgetException(_t('父级评论不存在'));
            }
        }
        
        //检验格式
        $validator = new TypechoValidation();
        $validator->addRule('author', 'required', _t('必须填写用户名'));

        if($this->options->commentsRequireMail)
        {
            $validator->addRule('mail', 'required', _t('必须填写电子邮箱地址'));
        }

        $validator->addRule('mail', 'email', _t('邮箱地址不合法'));

        if($this->options->commentsRequireUrl)
        {
            $validator->addRule('url', 'required', _t('必须填写个人主页'));
        }

        $validator->addRule('url', 'url', _t('个人主页地址不合法'));
        $validator->addRule('text', 'required', _t('必须填写评论内容'));

        $validator->run(TypechoRequest::getParametersFrom('author', 'mail', 'url', 'text'));

        $comment['author'] = strip_tags(TypechoRequest::getParameter('author'));
        $comment['mail'] = strip_tags(TypechoRequest::getParameter('mail'));
        $comment['url'] = strip_tags(TypechoRequest::getParameter('url'));
        $comment['text'] = TypechoRequest::getParameter('text');

        TypechoRequest::setCookie('author', $comment['author']);
        TypechoRequest::setCookie('mail', $comment['mail']);
        TypechoRequest::setCookie('url', $comment['url']);
        TypechoRequest::setCookie('text', $comment['text']);
        
        /** 生成过滤器 */
        TypechoPlugin::instance(__FILE__)->filter(__METHOD__, $comment);
        
        /** 添加评论 */
        $commentId = $this->insertComment($comment, $this->content->cid);
        
        TypechoRequest::deleteCookie('text');
        $this->goBack('#comment-' . $commentId);
    }
    
    private function trackback()
    {
        $trackback = array(
            'created'   =>  $this->options->gmtTime,
            'agent'     =>  $_SERVER["HTTP_USER_AGENT"],
            'ip'        =>  TypechoRequest::getClientIp(),
            'type'      =>  'trackback',
            'status'    =>  'approved'
        );
        
        $trackback['author'] = strip_tags(TypechoRequest::getParameter('blog_name'));
        $trackback['url'] = strip_tags(TypechoRequest::getParameter('url'));
        $trackback['text'] = TypechoRequest::getParameter('excerpt');
        
        /** 生成过滤器 */
        TypechoPlugin::instance(__FILE__)->filter(__METHOD__, $trackback);
        
        /** 添加引用 */
        $trackbackId = $this->insertComment($trackback, $this->content->cid);
    }

    /**
     * 入口函数,提交评论
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $permalink = TypechoRoute::getParameter('permalink');
        $callback = TypechoRoute::getParameter('type');
        
        if(false !== TypechoRoute::match(TypechoConfig::get('Route'), $permalink) && 
        ('post' == TypechoRoute::$current || 'page' == TypechoRoute::$current) &&
        Typecho::widget('Archive')->have() && 
        in_array($callback, array('comment', 'trackback')))
        {
            $this->content = Typecho::widget('Archive', 1, false);
            
            /** 判断来源 */
            if('comment' == $callback && (empty($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] != $this->content->permalink))
            {
                throw new TypechoWidgetException(_t('来源页不合法'));
            }
            
            /** 如果文章允许反馈 */
            if(!$this->content->allow($callback))
            {
                throw new TypechoWidgetException(_t('对不起,此内容的反馈被关闭.'), TypechoException::FORBIDDEN);
            }
            
            /** 调用函数 */
            $this->$callback();
        }
        else
        {
            throw new TypechoWidgetException(_t('被评论的文章不存在'), TypechoException::NOTFOUND);
        }
    }
}
