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
            'agent'     =>  $this->request->getAgent(),
            'ip'        =>  $this->request->getIp(),
            'ownerId'   =>  $this->_content->author->uid,
            'type'      =>  'comment',
            'status'    =>  !$this->_content->allow('edit') && $this->options->commentsRequireModeration ? 'waiting' : 'approved'
        );
    
        /** 判断父节点 */
        if ($parentId = $this->request->filter('int')->parent) {
            if (($parent = $this->db->fetchRow($this->db->select('coid')->from('table.comments')
            ->where('coid = ?', $parentId))) && $this->content->cid == $parent['cid']) {
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
        
        $comment['text'] = $this->request->filter(array($this, 'filterText'))->text;

        /** 对一般匿名访问者,将用户数据保存一个月 */
        if (!$this->user->hasLogin()) {
            /** Anti-XSS */
            $comment['author'] = $this->request->filter('strip_tags', 'trim', 'xss')->author;
            $comment['mail'] = $this->request->filter('strip_tags', 'trim', 'xss')->mail;
            $comment['url'] = $this->request->filter('url')->url;
            
            /** 修正用户提交的url */
            if (!empty($comment['url'])) {
                $urlParams = parse_url($comment['url']);
                if (!isset($urlParams['scheme'])) {
                    $comment['url'] = 'http://' . $comment['url'];
                }
            }
        
            $expire = $this->options->gmtTime + $this->options->timezone + 30*24*3600;
            $this->response->setCookie('__typecho_remember_author', $comment['author'], $expire);
            $this->response->setCookie('__typecho_remember_mail', $comment['mail'], $expire);
            $this->response->setCookie('__typecho_remember_url', $comment['url'], $expire);
        } else {
            $comment['author'] = $this->user->screenName;
            $comment['mail'] = $this->user->mail;
            $comment['url'] = $this->user->url;
        
            /** 记录登录用户的id */
            $comment['authorId'] = $this->user->uid;
        }
        
        if ($error = $validator->run($comment)) {
            /** 记录文字 */
            $this->response->setCookie('__typecho_remember_text', $comment['text']);
            throw new Typecho_Widget_Exception(implode("\n", $error));
        }
        
        /** 生成过滤器 */
        $comment = $this->plugin()->comment($comment, $this->_content);
        
        /** 添加评论 */
        $commentId = $this->insert($comment);
        $this->response->deleteCookie('text');
        $this->db->fetchRow($this->select()->where('coid = ?', $commentId)
        ->limit(1), array($this, 'push'));

        /** 评论完成接口 */
        $this->plugin()->finishComment($this);
        
        $this->response->goBack($this->theId);
    }
    
    /**
     * 引用处理函数
     * 
     * @access private
     * @return void
     */
    private function trackback()
    {
        /** 如果不是POST方法 */
        if (!$this->request->isPost()) {
            $this->response->redirect($this->_content->permalink);
        }
    
        /** 如果库中已经存在当前ip为spam的trackback则直接拒绝 */
        if ($this->size($this->select()
        ->where('status = ? AND ip = ?', 'spam', $this->request->getIp())) > 0) {
            /** 使用404告诉机器人 */
            throw new Typecho_Widget_Exception(_t('找不到内容'), 404);
        }
    
        $trackback = array(
            'cid'       =>  $this->_content->cid,
            'created'   =>  $this->options->gmtTime,
            'agent'     =>  $this->request->getAgent(),
            'ip'        =>  $this->request->getIp(),
            'ownerId'   =>  $this->_content->author->uid,
            'type'      =>  'trackback',
            'status'    =>  $this->options->commentsRequireModeration ? 'waiting' : 'approved'
        );
        
        $trackback['author'] = $this->request->filter('strip_tags', 'trim', 'xss')->blog_name;
        $trackback['url'] = $this->request->filter('url')->url;
        $trackback['text'] = $this->request->filter(array($this, 'filterText'))->excerpt;
        
        //检验格式
        $validator = new Typecho_Validate();
        $validator->addRule('url', 'required', 'We require all Trackbacks to provide an url.')
        ->addRule('url', 'url', 'Your url is not valid.')
        ->addRule('text', 'required', 'We require all Trackbacks to provide an excerption.')
        ->addRule('author', 'required', 'We require all Trackbacks to provide an blog name.');
        
        $validator->setBreak();
        if ($error = $validator->run($trackback)) {
            $message = array('success' => 1, 'message' => current($error));
            $this->response->throwXml($message);
        }
        
        /** 截取长度 */
        $trackback['text'] = Typecho_Common::subStr($trackback['text'], 0, 100, '[...]');
        
        /** 如果库中已经存在重复url则直接拒绝 */
        if ($this->size($this->select()
        ->where('cid = ? AND url = ? AND type <> ?', $this->_content->cid, $trackback['url'], 'comment')) > 0) {
            /** 使用403告诉机器人 */
            throw new Typecho_Widget_Exception(_t('禁止重复提交'), 403);
        }
        
        /** 生成过滤器 */
        $trackback = $this->plugin()->trackback($trackback, $this->_content);
        
        /** 添加引用 */
        $trackbackId = $this->insert($trackback);
        
        /** 评论完成接口 */
        $this->plugin()->finishTrackback($this);
        
        /** 返回正确 */
        $this->response->throwXml(array('success' => 0, 'message' => 'Trackback has registered.'));
    }
    
    /**
     * 过滤评论内容
     * 
     * @access public
     * @param string $text 评论内容
     * @return string
     */
    public function filterText($text)
    {
        $text = str_replace("\r", '', trim($text));
        $text = preg_replace("/\n{2,}/", "\n\n", $text);
    
        return Typecho_Common::removeXSS(Typecho_Common::stripTags(
        $text, $this->options->commentsHTMLTagAllowed));
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
        $result = Typecho_Router::match($this->request->permalink, $params);
    
        /** 判断内容是否存在 */
        if (false !== $result &&
        $this->widget('Widget_Archive', NULL, $params)->to($this->_content)->have() && 
        $this->_content->is('single') && 
        in_array($callback, array('comment', 'trackback'))) {
            /** 判断来源 */
            // ~ fix Issue 38
            if ('comment' == $callback && 0 !== strpos($_SERVER['HTTP_REFERER'], $this->_content->permalink)) {
                //增加对自定义首页的判断
                if ($this->options->customHomePage != $this->_content->cid) {
                    throw new Typecho_Widget_Exception(_t('来源页不合法'), 403);
                }
            }
            
            /** 如果文章不允许反馈 */
            if ('comment' == $callback && !$this->_content->allow('comment')) {
                throw new Typecho_Widget_Exception(_t('对不起,此内容的反馈被禁止.'), 403);
            }
            
            /** 如果文章不允许引用 */
            if ('trackback' == $callback && !$this->_content->allow('ping')) {
                throw new Typecho_Widget_Exception(_t('对不起,此内容的引用被禁止.'), 403);
            }
            
            /** 调用函数 */
            $this->$callback();
        } else {
            throw new Typecho_Widget_Exception(_t('找不到内容'), 404);
        }
    }
}
