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

/**
 * 反馈提交组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Feedback extends Widget_Abstract_Comments implements Typecho_Widget_Interface_Action
{
    /**
     * 内容组件
     * 
     * @access private
     * @var Typecho_Widget
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
            'cid'       =>  $this->content->cid,
            'created'   =>  $this->options->gmtTime,
            'agent'     =>  $_SERVER["HTTP_USER_AGENT"],
            'ip'        =>  Typecho_Request::getClientIp(),
            'type'      =>  'comment',
            'status'    =>  'approved'
        );
    
        /** 判断父节点 */
        if($parentId = Typecho_Request::getParameter('parent'))
        {
            if(($parent = $this->db->fetchRow($this->db->sql()->select('table.comments', '`coid`')
            ->where('`coid` = ?', $parentId))) && $this->content->cid == $parent['cid'])
            {
                $comment['parent'] = $parentId;
            }
            else
            {
                throw new Typecho_Widget_Exception(_t('父级评论不存在'));
            }
        }
        
        //检验格式
        $validator = new Typecho_Validate();
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

        $validator->run(Typecho_Request::getParametersFrom('author', 'mail', 'url', 'text'));

        $comment['author'] = strip_tags(Typecho_Request::getParameter('author'));
        $comment['mail'] = strip_tags(Typecho_Request::getParameter('mail'));
        $comment['url'] = strip_tags(Typecho_Request::getParameter('url'));
        $comment['text'] = Typecho_Request::getParameter('text');

        Typecho_Request::setCookie('author', $comment['author']);
        Typecho_Request::setCookie('mail', $comment['mail']);
        Typecho_Request::setCookie('url', $comment['url']);
        Typecho_Request::setCookie('text', $comment['text']);
        
        /** 生成过滤器 */
        Typecho_Plugin::instance(__FILE__)->filter(__METHOD__, $comment);
        
        /** 添加评论 */
        $commentId = $this->insert($comment);
        
        Typecho_Request::deleteCookie('text');
        $this->goBack('#comment-' . $commentId);
    }
    
    /**
     * 引用处理函数
     * 
     * @access private
     * @return void
     */
    private function trackback()
    {
        $trackback = array(
            'cid'       =>  $this->content->cid,
            'created'   =>  $this->options->gmtTime,
            'agent'     =>  $_SERVER["HTTP_USER_AGENT"],
            'ip'        =>  Typecho_Request::getClientIp(),
            'type'      =>  'trackback',
            'status'    =>  'approved'
        );
        
        $trackback['author'] = strip_tags(Typecho_Request::getParameter('blog_name'));
        $trackback['url'] = strip_tags(Typecho_Request::getParameter('url'));
        $trackback['text'] = Typecho_Request::getParameter('excerpt');
        
        /** 生成过滤器 */
        Typecho_Plugin::instance(__FILE__)->filter(__METHOD__, $trackback);
        
        /** 添加引用 */
        $trackbackId = $this->insert($trackback);
    }

    /**
     * 入口函数,提交评论
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $permalink = Typecho_Route::getParameter('permalink');
        $callback = Typecho_Route::getParameter('type');
        
        if(false !== Typecho_Route::match(Typecho_Config::get('Route'), $permalink) && 
        ('post' == Typecho_Route::$current || 'page' == Typecho_Route::$current) &&
        Typecho_API::factory('Widget_Archive', 1)->have() && 
        in_array($callback, array('comment', 'trackback')))
        {
            $this->content = Typecho_API::factory('Widget_Archive', 1);
            
            /** 判断来源 */
            if('comment' == $callback && (empty($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] != $this->content->permalink))
            {
                throw new Typecho_Widget_Exception(_t('来源页不合法'));
            }
            
            /** 如果文章允许反馈 */
            if(!$this->content->allow($callback))
            {
                throw new Typecho_Widget_Exception(_t('对不起,此内容的反馈被关闭.'), Typecho_Exception::FORBIDDEN);
            }
            
            /** 调用函数 */
            $this->$callback();
        }
        else
        {
            throw new Typecho_Widget_Exception(_t('被评论的文章不存在'), Typecho_Exception::NOTFOUND);
        }
    }
}
