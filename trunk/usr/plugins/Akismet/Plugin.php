<?php
/**
 * Akismet 反垃圾评论插件 for Typecho
 * 
 * @package Akismet
 * @author qining
 * @version 1.0
 * @link http://typecho.org
 */
class Akismet_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public function activate()
    {
        if (false == Typecho_Http_Client::get('Curl', 'Socket')) {
            throw new Typecho_Plugin_Exception(_t('对不起, 您的主机不支持 php-curl 扩展而且没有打开 allow_url_fopen 功能, 无法正常使用此功能'));
        }
    
        Typecho_Plugin::factory('Widget_Feedback')->comment = array('Akismet_Plugin', 'filter');
        Typecho_Plugin::factory('Widget_Feedback')->trackback = array('Akismet_Plugin', 'filter');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public function deactivate(){}
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public function config(Typecho_Widget_Helper_Form $form)
    {
        $key = new Typecho_Widget_Helper_Form_Element_Text('key', NULL, NULL, _t('服务密钥'), _t('此密钥需要向服务提供商注册<br />
        它是一个用于表明您合法用户身份的字符串'));
        $form->addInput($key->addRule('required', _t('您必须填写一个服务密钥'))
        ->addRule(array('Akismet_Plugin', 'validate'), _t('您使用的服务密钥错误')));
        
        $url = new Typecho_Widget_Helper_Form_Element_Text('url', NULL, 'http://rest.akismet.com',
        _t('服务地址'), _t('这是反垃圾评论服务提供商的服务器地址<br />
        我们推荐您使用 <a href="http://akismet.com">Akismet</a> 或者 <a href="http://antispam.typepad.com">Typepad</a> 的反垃圾服务'));
        $form->addInput($url->addRule('url', _t('您使用的地址格式错误')));
    }
    
    /**
     * 验证api的key值
     * 
     * @access public
     * @param string $key 服务密钥
     * @return boolean
     */
    public function validate($key)
    {
        $options = Typecho_Widget::widget('Widget_Options');
        $url = Typecho_Request::getParameter('url');
        
        $data = array(
            'key'   =>  $key,
            'blog'  =>  $options->siteUrl
        );
        
        $client = Typecho_Http_Client::get('Curl', 'Socket');
        if (false != $client) {
            $client->setData($data)
            ->setHeader('User-Agent', $options->generator . ' | Akismet/1.1')
            ->send(Typecho_Common::url('/1.1/verify-key', $url));
            
            if ('valid' == $client->getResponseBody()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 评论过滤器
     * 
     * @access public
     * @param array $comment 评论结构
     * @param Typecho_Widget $post 被评论的文章
     * @param array $result 返回的结果上下文
     * @return void
     */
    public static function filter($comment, $post, $result)
    {
        $comment = empty($result) ? $comment : $result;
    
        $options = Typecho_Widget::widget('Widget_Options');
        $url = $options->plugin('Akismet')->url;
        $key = $options->plugin('Akismet')->key;
        
        $allowedServerVars = array(
            'SCRIPT_URI',
            'HTTP_HOST',
            'HTTP_USER_AGENT',
            'HTTP_ACCEPT',
            'HTTP_ACCEPT_LANGUAGE',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_ACCEPT_CHARSET',
            'HTTP_KEEP_ALIVE',
            'HTTP_CONNECTION',
            'HTTP_CACHE_CONTROL',
            'HTTP_PRAGMA',
            'HTTP_DATE',
            'HTTP_EXPECT',
            'HTTP_MAX_FORWARDS',
            'HTTP_RANGE',
            'CONTENT_TYPE',
            'CONTENT_LENGTH',
            'SERVER_SIGNATURE',
            'SERVER_SOFTWARE',
            'SERVER_NAME',
            'SERVER_ADDR',
            'SERVER_PORT',
            'REMOTE_PORT',
            'GATEWAY_INTERFACE',
            'SERVER_PROTOCOL',
            'REQUEST_METHOD',
            'QUERY_STRING',
            'REQUEST_URI',
            'SCRIPT_NAME',
            'REQUEST_TIME'
        );
        
        $data = array(
            'blog'                  =>  $options->siteUrl,
            'user_ip'               =>  $comment['ip'],
            'user_agent'            =>  $comment['agent'],
            'referrer'              =>  Typecho_Request::getReferer(),
            'permalink'             =>  $post->permalink,
            'comment_type'          =>  $comment['mode'],
            'comment_author'        =>  $comment['author'],
            'comment_author_email'  =>  $comment['mail'],
            'comment_author_url'    =>  $comment['url'],
            'comment_content'       =>  $comment['text']
        );
        
        foreach ($allowedServerVars as $val) {
            if (array_key_exists($val, $_SERVER)) {
                $data[$val] = $_SERVER[$val];
            }
        }

        $client = Typecho_Http_Client::get('Curl', 'Socket');
        if (false != $client) {
            $params = parse_url($url);
            $url = $params['scheme'] . '://' . $key . '.' . $params['host'] . (isset($params['path']) ? $params['path'] : NULL);

            $client->setHeader('User-Agent', $options->generator . ' | Akismet/1.1')
            ->setData($data)
            ->send(Typecho_Common::url('/1.1/comment-check', $url));

            if ('true' == $client->getResponseBody()) {
                $comment['status'] = 'spam';
            }
        }
        
        return $comment;
    }
}
