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
            'status'    =>  !$this->content->postIsWriteable() && $this->options->commentsRequireModeration ? 'waiting' : 'approved'
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
        $user = Typecho_API::factory('Widget_Users_Current');
        $validator->addRule('author', 'required', _t('必须填写用户名'));
        $validator->addRule('author', array($this, 'requireUserLogin'), _t('您所使用的用户名已经被注册,请登录后再次提交'));

        if($this->options->commentsRequireMail && !$user->hasLogin())
        {
            $validator->addRule('mail', 'required', _t('必须填写电子邮箱地址'));
        }

        $validator->addRule('mail', 'email', _t('邮箱地址不合法'));

        if($this->options->commentsRequireUrl && !$user->hasLogin())
        {
            $validator->addRule('url', 'required', _t('必须填写个人主页'));
        }

        $validator->addRule('url', 'url', _t('个人主页地址不合法'));
        $validator->addRule('text', 'required', _t('必须填写评论内容'));

        $comment['author'] = strip_tags(Typecho_Request::getParameter('author', $user->screenName));
        $comment['mail'] = strip_tags(Typecho_Request::getParameter('mail', $user->mail));
        $comment['url'] = strip_tags(Typecho_Request::getParameter('url', $user->url));
        $comment['text'] = Typecho_API::stripTags(Typecho_Request::getParameter('text'), $this->options->commentsHTMLTagAllowed);

        /** 对一般匿名访问者,将用户数据保存一个月 */
        if(!$user->hasLogin())
        {
            $expire = $this->options->gmtTime + $this->options->timezone + 30*24*3600;
            Typecho_Request::setCookie('author', $comment['author'], $expire);
            Typecho_Request::setCookie('mail', $comment['mail'], $expire);
            Typecho_Request::setCookie('url', $comment['url'], $expire);
        }
        
        try
        {
            $validator->run($comment);
        }
        catch(Typecho_Validate_Exception $e)
        {
            /** 记录文字 */
            Typecho_Request::setCookie('text', $comment['text']);
            throw new Typecho_Widget_Exception($e->getMessages());
        }
        
        /** 生成过滤器 */
        $comment = _p(__FILE__, 'Filter')->comment($comment);
        
        /** 添加评论 */
        $commentId = $this->insert($comment);
        Typecho_Request::deleteCookie('text');
        
        Typecho_API::goBack('#comments-' . $commentId);
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
            'status'    =>  !$this->content->postIsWriteable() && $this->options->commentsRequireModeration ? 'waiting' : 'approved'
        );
        
        $trackback['author'] = strip_tags(Typecho_Request::getParameter('blog_name'));
        $trackback['url'] = strip_tags(Typecho_Request::getParameter('url'));
        $trackback['text'] = Typecho_API::stripTags(Typecho_Request::getParameter('excerpt'), $this->options->commentsHTMLTagAllowed);
        
        //检验格式
        $validator = new Typecho_Validate();
        $validator->addRule('url', 'required', 'We require all Trackbacks to provide an url.')
        ->addRule('url', 'url', 'Your url is not valid.')
        ->addRule('text', 'required', 'We require all Trackbacks to provide an excerption.')
        ->addRule('blog_name', 'required', 'We require all Trackbacks to provide an blog name.');
        
        try
        {
            $validator->setBreak();
            $validator->run($trackback);
        }
        catch(Typecho_Validate_Exception $e)
        {
            $message = array('success' => 1, 'message' => $e->getMessage());
            Typecho_API::throwAjaxResponse($message);
        }
        
        /** 生成过滤器 */
        _p(__FILE__, 'Filter')->trackback($trackback);
        
        /** 添加引用 */
        $trackbackId = $this->insert($trackback);
    }
    
    /**
     * 对已注册用户的保护性检测
     * 
     * @access public
     * @param string $userName 用户名
     * @return void
     */
    public function requireUserLogin($userName)
    {
        $user = Typecho_API::factory('Widget_Users_Current');
        
        if($user->hasLogin() && $user->screenName != $userName)
        {
            /** 当前用户名与提交者不匹配 */
            return false;
        }
        else if(!$user->hasLogin() && $this->db->fetchRow($this->db->sql()->select('table.users', '`uid`')
        ->where('`screenName` = ? OR `name` = ?', $userName, $userName)->limit(1)))
        {
            /** 此用户名已经被注册 */
            return false;
        }
        
        return true;
    }

    /**
     * 入口函数,提交评论
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $permalink = Typecho_Request::getParameter('permalink');
        $callback = Typecho_Request::getParameter('type');

        /** 判断内容是否存在 */
        if(false !== Typecho_Router::match($permalink) && 
        ('post' == Typecho_Router::$current || 'page' == Typecho_Router::$current) &&
        Typecho_API::factory('Widget_Archive', 1)->have() && 
        in_array($callback, array('comment', 'trackback')))
        {
            $this->content = Typecho_API::factory('Widget_Archive', 1);
            
            /** 判断来源 */
            if('comment' == $callback && (empty($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] != $this->content->permalink))
            {
                throw new Typecho_Widget_Exception(_t('来源页不合法'));
            }
            
            /** 判断评论间隔 */
            if(!$this->content->postIsWriteable() && $this->options->commentsUniqueIpInterval > 0)
            {
                $recent = $this->db->fetchObject($this->db->sql()->select('table.comments', '`created`')
                ->where('table.comments.`ip` = ?', Typecho_Request::getClientIp())
                ->order('table.comments.`created`', Typecho_Db::SORT_DESC)->limit(1));

                if($recent)
                {
                    if($this->options->gmtTime - $recent->created < $this->options->commentsUniqueIpInterval)
                    {
                        throw new Typecho_Widget_Exception(_t('对不起,您的发言速度太快.'), Typecho_Exception::FORBIDDEN);
                    }
                }
            }
            
            /** 如果文章允许反馈 */
            if(!$this->content->allow('comment'))
            {
                throw new Typecho_Widget_Exception(_t('对不起,此内容的评论被关闭.'), Typecho_Exception::FORBIDDEN);
            }
            
            if(NULL != $this->content->password && 
            $this->content->password != Typecho_Request::getParameter('protect_password', Typecho_Request::getCookie('protect_password')))
            {
                throw new Typecho_Widget_Exception(_t('此文章被密码保护.'), Typecho_Exception::FORBIDDEN);
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
