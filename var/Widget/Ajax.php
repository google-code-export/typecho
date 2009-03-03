<?php
/**
 * 异步调用组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 异步调用组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 */
class Widget_Ajax extends Widget_Abstract_Options implements Widget_Interface_Do
{
    /**
     * 编码
     * 
     * @access public
     * @param array $matches
     * @return string
     */
    public static function encodeCallback($matches)
    {
        return '<code' . $matches[1] . '>' . str_replace(' ', '&nbsp;', htmlspecialchars(trim($matches[2]))) . '</code>';
    }
    
    /**
     * 解析
     * 
     * @access public
     * @param array $matches
     * @return string
     */
    public static function decodeCallback($matches)
    {
        return '<code' . $matches[1] . ">\n" .
        trim(htmlspecialchars_decode(str_replace('<br />', "\n", $matches[2])))
         . "\n</code>";
    }

    /**
     * 针对rewrite验证的请求返回
     * 
     * @access public
     * @return void
     */
    public function remoteCallback()
    {
        if ($this->options->generator == $this->request->getAgent()) {
            echo 'OK';
        }
    }
    
    /**
     * 获取最新版本
     * 
     * @access public
     * @return void
     */
    public function checkVersion()
    {
        $this->user->pass('editor');
        $client = Typecho_Http_Client::get();
        if ($client) {
            $client->setHeader('User-Agent', $this->options->generator)
            ->send('http://code.google.com/feeds/p/typecho/downloads/basic');
            
            /** 匹配内容体 */
            $response = $client->getResponseBody();
            preg_match_all("/<link[^>]*href=\"([^>]*)\"\s*\/>\s*<title>([^>]*)<\/title>/is", $response, $matches);
            $result = array('available' => 0);
            
            list($soft, $version) = explode(' ', $this->options->generator);
            $current = explode('/', $version);
            
            if ($matches) {
                foreach ($matches[0] as $key => $val) {
                    $title = trim($matches[2][$key]);
                    if (preg_match("/([0-9\.]+)\(([0-9\.]+)\)\-release/is", $title, $out)) {
                        if (version_compare($out[1], $current[0], '>=')
                        && version_compare($out[2], $current[1], '>')) {
                            $result = array('available' => 1, 'latest' => $out[1],
                            'current' => $current[0], 'link' => $matches[1][$key]);
                            break;
                        }
                    }
                }
            }
            
            $this->response->setCookie('__typecho_check_version', $result);
            $this->response->throwJson($result);
            return;
        }
        
        throw new Typecho_Widget_Exception(_t('禁止访问'), 403);
    }
    
    /**
     * 远程请求代理
     * 
     * @access public
     * @return void
     */
    public function feed()
    {
        $this->user->pass('subscriber');
        $client = Typecho_Http_Client::get();
        if ($client) {
            $client->setHeader('User-Agent', $this->options->generator)
            ->send('http://typecho.net/feed/');
            
            /** 匹配内容体 */
            $response = $client->getResponseBody();
            preg_match_all("/<item>\s*<title><\!\[CDATA\[([^>]*)\]\]><\/title>\s*<link>([^>]*)<\/link>\s*<pubDate>([^>]*)<\/pubDate>/is", $response, $matches);
            
            $data = array();
            
            if ($matches) {
                foreach ($matches[0] as $key => $val) {
                    $data[] = array(
                        'title'  =>  $matches[1][$key],
                        'link'   =>  $matches[2][$key],
                        'date'   =>  Typecho_I18n::dateWord(strtotime($matches[3][$key]),
                        $this->options->gmtTime + $this->options->timezone),
                    );
                    
                    if ($key > 3) {
                        break;
                    }
                }
            }
            
            $this->response->throwJson($data);
            return;
        }
        
        throw new Typecho_Widget_Exception(_t('禁止访问'), 403);
    }
    
    /**
     * 发送pingback实现
     * 
     * @access public
     * @return void
     */
    public function sendPingbackHandle()
    {
        /** 验证权限 */
        $this->user->pass('contributor');
        
        /** 忽略超时 */
        ignore_user_abort(true);
        
        /** 获取post */
        $post = $this->widget('Widget_Contents_Post_Edit', NULL, "cid={$this->request->cid}");
        
        /** 发送pingback */
        foreach ($this->request->links as $url) {
            $xmlrpc = new IXR_Client($url);
            $xmlrpc->pingback->ping($post->permalink, $url);
        }
    }
    
    /**
     * 发送pingback
     * <code>
     * $this->sendPingbacks(array('http://www.example.com/archives/1' => 'http://www.mydomain.com/archives/365'));
     * </code>
     * 
     * @access public
     * @param integer $cid 内容id
     * @param integer $text 内容文本
     * @return void
     */
    public function sendPingback($cid, $text)
    {
        $this->user->pass('contributor');
        if (preg_match("|<a[^>]*href=[\"'](.*?)[\"'][^>]*>(.*?)</a>|", $line, $matches)) {
            $data = array();
            $data['do'] = 'pingback';
            $data['cid'] = $cid;
            
            foreach ($matches[1] as $url) {
                $data['links'][] = $url;
            }
            
            if ($client = Typecho_Http_Client::get()) {
                $client->setCookie('__typecho_uid', $this->user->uid, 0, $this->options->siteUrl)
                ->setCookie('__typecho_authCode', $this->user->authCode, 0, $this->options->siteUrl)
                ->setHeader('User-Agent', $this->options->generator)
                ->setTimeout(1)
                ->setData($data)
                ->send(Typecho_Common::url('Ajax.do', $this->options->index));
            }
        }
    }
    
    /**
     * 自定义编辑器大小
     * 
     * @access public
     * @return void
     */
    public function editorResize()
    {
        $this->user->pass('contributor');
        if ($this->db->fetchObject($this->db->select(array('COUNT(*)' => 'num'))
        ->from('table.options')->where('name = ? AND user = ?', 'editorSize', $this->user->uid))->num > 0) {
            $this->widget('Widget_Abstract_Options')
            ->update(array('value' => $this->request->size), $this->db->sql()->where('name = ? AND user = ?', 'editorSize', $this->user->uid));
        } else {
            $this->widget('Widget_Abstract_Options')->insert(array(
                'name'  =>  'editorSize',
                'value' =>  $this->request->size,
                'user'  =>  $this->user->uid
            ));
        }
    }
    
    /**
     * 将文本转化为html
     * 
     * @access public
     * @return void
     */
    public function cutParagraph()
    {
        $this->user->pass('contributor');
        echo preg_replace_callback("/<code([^>]*)>(.*?)<\/code>/is",
        array('Widget_Ajax', 'encodeCallback'), Typecho_Common::cutParagraph($this->request->content));
    }
    
    /**
     * 将html转化为文本
     * 
     * @access public
     * @return void
     */
    public function removeParagraph()
    {
        $this->user->pass('contributor');
        echo html_entity_decode(Typecho_Common::removeParagraph(
        preg_replace_callback("/<code([^>]*)>(.*?)<\/code>/is", array('Widget_Ajax', 'decodeCallback'), $this->request->content)
        ), ENT_QUOTES, $this->options->charset);
    }
    
    public function autoSave()
    {
        
    }
    
    /**
     * 异步请求入口
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $this->onRequest('do', 'remoteCallback')->remoteCallback();
        $this->onRequest('do', 'feed')->feed();
        $this->onRequest('do', 'checkVersion')->checkVersion();
        $this->onRequest('do', 'editorResize')->editorResize();
        $this->onRequest('do', 'pingback')->sendPingbackHandle();
        $this->onRequest('do', 'cutParagraph')->cutParagraph();
        $this->onRequest('do', 'removeParagraph')->removeParagraph();
        $this->onRequest('do', 'autoSave')->autoSave();
    }
}
