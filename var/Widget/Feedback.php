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
class Widget_Feedback extends Widget_Abstract_Comments implements Widget_Interface_Action
{
    /**
     * 评论处理函数
     * 
     * @access private
     * @return void
     */
    private function comment()
    {
        $comment = array(
            'cid'       =>  $this->widget('Widget_Archive')->cid,
            'created'   =>  $this->options->gmtTime,
            'agent'     =>  $_SERVER["HTTP_USER_AGENT"],
            'ip'        =>  $this->request->getClientIp(),
            'type'      =>  'comment',
            'status'    =>  !$this->widget('Widget_Archive')->postIsWriteable() && $this->options->commentsRequireModeration ? 'waiting' : 'approved'
        );
    
        /** 判断父节点 */
        if($parentId = $this->request->parent)
        {
            if(($parent = $this->db->fetchRow($this->db->select('table.comments', '`coid`')
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

        $validator->addRule('text', 'required', _t('必须填写评论内容'));

        $comment['author'] = Typecho_Common::removeXSS(trim(strip_tags($this->request->getParameter('author', $this->user->screenName))));
        $comment['mail'] = Typecho_Common::removeXSS(trim(strip_tags($this->request->getParameter('mail', $this->user->mail))));
        $comment['url'] = Typecho_Common::removeXSS(trim(strip_tags($this->request->getParameter('url', $this->user->url))));
        
        /** 修正用户提交的url */
        if(!empty($comment['url']))
        {
            $urlParams = parse_url($comment['url']);
            if(!isset($urlParams['scheme']))
            {
                $comment['url'] = 'http://' . $comment['url'];
            }
        }
        
        $comment['text'] = Typecho_Common::removeXSS(Typecho_Common::stripTags($this->request->text, $this->options->commentsHTMLTagAllowed));

        /** 对一般匿名访问者,将用户数据保存一个月 */
        if(!$user->hasLogin())
        {
            $expire = $this->options->gmtTime + $this->options->timezone + 30*24*3600;
            $this->request->setCookie('author', $comment['author'], $expire);
            $this->request->setCookie('mail', $comment['mail'], $expire);
            $this->request->setCookie('url', $comment['url'], $expire);
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
        $comment = $this->plugin('Filter')->comment($comment);
        
        /** 添加评论 */
        $commentId = $this->insert($comment);
        $this->request->deleteCookie('text');
        
        Typecho_Common::goBack('#comments-' . $commentId);
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
            'cid'       =>  $this->widget('Widget_Archive')->cid,
            'created'   =>  $this->options->gmtTime,
            'agent'     =>  $_SERVER["HTTP_USER_AGENT"],
            'ip'        =>  $this->request->getClientIp(),
            'type'      =>  'trackback',
            'status'    =>  !$this->widget('Widget_Archive')->postIsWriteable() && $this->options->commentsRequireModeration ? 'waiting' : 'approved'
        );
        
        $trackback['author'] = Typecho_Common::removeXSS(trim(strip_tags($this->request->blog_name)));
        $trackback['url'] = Typecho_Common::removeXSS(trim(strip_tags($this->request->url)));
        $trackback['text'] = Typecho_Common::removeXSS(Typecho_Common::stripTags($this->request->excerpt, $this->options->commentsHTMLTagAllowed));
        
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
            $this->response()->throwXml($message);
        }
        
        /** 生成过滤器 */
        $trackback = $this->plugin('Filter')->trackback($trackback);
        
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
        if($this->user->hasLogin() && $this->user->screenName != $userName)
        {
            /** 当前用户名与提交者不匹配 */
            return false;
        }
        else if(!$this->user->hasLogin() && $this->db->fetchRow($this->db->select('table.users', '`uid`')
        ->where('`screenName` = ? OR `name` = ?', $userName, $userName)->limit(1)))
        {
            /** 此用户名已经被注册 */
            return false;
        }
        
        return true;
    }

    /**
     * 初始化函数
     * 
     * @access public
     * @param Typecho_Widget_Request $request 请求对象
     * @param Typecho_Widget_Response $response 回执对象
     * @return void
     */
    public function init(Typecho_Widget_Request $request, Typecho_Widget_Response $response)
    {
        /** 判断内容是否存在 */
        if(false !== Typecho_Router::match($request->permalink) && 
        ('post' == Typecho_Router::$current || 'page' == Typecho_Router::$current) &&
        $this->widget('Widget_Archive')->have() && 
        in_array($callback, array('comment', 'trackback')))
        {
            /** 判断来源 */
            if('comment' == $callback && (empty($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] != $this->widget('Widget_Archive')->permalink))
            {
                throw new Typecho_Widget_Exception(_t('来源页不合法'));
            }
            
            /** 判断评论间隔 */
            if(!$this->widget('Widget_Archive')->postIsWriteable() && $this->widget('Widget_Archive')->commentsUniqueIpInterval > 0)
            {
                $recent = $this->db->fetchObject($this->db->select('table.comments', '`created`')
                ->where('table.comments.`ip` = ?', $request->getClientIp())
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
            if(!$this->widget('Widget_Archive')->allow('comment'))
            {
                throw new Typecho_Widget_Exception(_t('对不起,此内容的评论被关闭.'), Typecho_Exception::FORBIDDEN);
            }
            
            if(NULL != $this->widget('Widget_Archive')->password && 
            $this->widget('Widget_Archive')->password != $request->protect_password)
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
