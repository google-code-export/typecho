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
class Widget_Feedback extends Widget_Abstract_Comments implements Widget_Interface_Do
{
    /**
     * 内容对象
     * 
     * @access private
     * @var Widget_Archive
     */
    private $_content;

    /**
     * 评论处理函数
     * 
     * @access private
     * @return void
     */
    private function comment()
    {
        $comment = array(
            'cid'       =>  $this->_content->cid,
            'created'   =>  $this->options->gmtTime,
            'agent'     =>  $_SERVER["HTTP_USER_AGENT"],
            'ip'        =>  $this->request->getClientIp(),
            'type'      =>  'comment',
            'status'    =>  !$this->_content->postIsWriteable() && $this->options->commentsRequireModeration ? 'waiting' : 'approved'
        );
    
        /** 判断父节点 */
        if ($parentId = $this->request->parent) {
            if (($parent = $this->db->fetchRow($this->db->select('table.comments', '`coid`')
            ->where('`coid` = ?', $parentId))) && $this->content->cid == $parent['cid']) {
                $comment['parent'] = $parentId;
            } else {
                throw new Typecho_Widget_Exception(_t('父级评论不存在'));
            }
        }
        
        //检验格式
        $validator = new Typecho_Validate();
        $validator->addRule('author', 'required', _t('必须填写用户名'));
        $validator->addRule('author', array($this, 'requireUserLogin'), _t('您所使用的用户名已经被注册,请登录后再次提交'));

        if ($this->options->commentsRequireMail && !$this->user->hasLogin()) {
            $validator->addRule('mail', 'required', _t('必须填写电子邮箱地址'));
        }

        $validator->addRule('mail', 'email', _t('邮箱地址不合法'));

        if ($this->options->commentsRequireUrl && !$user->hasLogin()) {
            $validator->addRule('url', 'required', _t('必须填写个人主页'));
        }

        $validator->addRule('text', 'required', _t('必须填写评论内容'));

        $comment['author'] = Typecho_Common::removeXSS(trim(strip_tags($this->request->getParameter('author', $this->user->screenName))));
        $comment['mail'] = Typecho_Common::removeXSS(trim(strip_tags($this->request->getParameter('mail', $this->user->mail))));
        $comment['url'] = Typecho_Common::removeXSS(trim(strip_tags($this->request->getParameter('url', $this->user->url))));
        
        /** 修正用户提交的url */
        if (!empty($comment['url'])) {
            $urlParams = parse_url($comment['url']);
            if (!isset($urlParams['scheme'])) {
                $comment['url'] = 'http://' . $comment['url'];
            }
        }
        
        $comment['text'] = Typecho_Common::removeXSS(Typecho_Common::stripTags($this->request->text, $this->options->commentsHTMLTagAllowed));

        /** 对一般匿名访问者,将用户数据保存一个月 */
        if (!$this->user->hasLogin()) {
            $expire = $this->options->gmtTime + $this->options->timezone + 30*24*3600;
            $this->response->setCookie('author', $comment['author'], $expire);
            $this->response->setCookie('mail', $comment['mail'], $expire);
            $this->response->setCookie('url', $comment['url'], $expire);
        }
        
        try {
            $validator->run($comment);
        } catch (Typecho_Validate_Exception $e) {
            /** 记录文字 */
            $this->response->setCookie('text', $comment['text']);
            $this->response->throwExceptionResponseByCode($e->getMessages());
        }
        
        /** 生成过滤器 */
        $comment = $this->plugin()->comment($comment);
        
        /** 添加评论 */
        $commentId = $this->insert($comment);
        $this->response->deleteCookie('text');
        
        $this->response->goBack('comments-' . $commentId);
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
            'cid'       =>  $this->_content->cid,
            'created'   =>  $this->options->gmtTime,
            'agent'     =>  $_SERVER["HTTP_USER_AGENT"],
            'ip'        =>  $this->request->getClientIp(),
            'type'      =>  'trackback',
            'status'    =>  !$this->_content->postIsWriteable() && $this->options->commentsRequireModeration ? 'waiting' : 'approved'
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
        
        try {
            $validator->setBreak();
            $validator->run($trackback);
        } catch (Typecho_Validate_Exception $e) {
            $message = array('success' => 1, 'message' => $e->getMessage());
            $this->response->throwXml($message);
        }
        
        /** 生成过滤器 */
        $trackback = $this->plugin()->trackback($trackback);
        
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
        if ($this->user->hasLogin() && $this->user->screenName != $userName) {
            /** 当前用户名与提交者不匹配 */
            return false;
        } else if (!$this->user->hasLogin() && $this->db->fetchRow($this->db->select('uid')
        ->from('table.users')->where('screenName = ? OR name = ?', $userName, $userName)->limit(1))) {
            /** 此用户名已经被注册 */
            return false;
        }
        
        return true;
    }

    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        /** 回调方法 */
        $callback = $this->request->type;
    
        /** 判断内容是否存在 */
        if (false !== Typecho_Router::match($this->request->permalink) && 
        ('post' == Typecho_Router::$current || 'page' == Typecho_Router::$current) &&
        $this->widget('Widget_Archive')->to($this->_content)->have() && 
        in_array($callback, array('comment', 'trackback'))) {
            /** 判断来源 */
            if ('comment' == $callback && (empty($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] != $this->_content->permalink)) {
                $this->response->throwExceptionResponseByCode(_t('来源页不合法'), 403);
            }
            
            /** 如果文章允许反馈 */
            if (!$this->_content->allow('comment')) {
                $this->response->throwExceptionResponseByCode(_t('对不起,此内容的反馈被禁止.'), 403);
            }
            
            /** 调用函数 */
            $this->$callback();
        } else {
            $this->response->throwExceptionResponseByCode(_t('被评论的文章不存在'), 404);
        }
    }
}
