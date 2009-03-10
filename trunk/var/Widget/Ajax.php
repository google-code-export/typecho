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
        echo Typecho_Common::encodeCode(Typecho_Common::cutParagraph($this->request->content));
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
        echo Typecho_Common::removeParagraph(Typecho_Common::decodeCode($this->request->content));
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
        $this->onRequest('do', 'cutParagraph')->cutParagraph();
        $this->onRequest('do', 'removeParagraph')->removeParagraph();
        $this->onRequest('do', 'autoSave')->autoSave();
    }
}
