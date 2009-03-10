<?php
/**
 * 通用异步服务
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 通用异步服务组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 */
class Widget_Service extends Widget_Abstract_Options implements Widget_Interface_Do
{
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
        $post = $this->widget('Widget_Archive', "type=post", "cid={$this->request->cid}");
        
        if ($post->have() && preg_match_all("|<a[^>]*href=[\"'](.*?)[\"'][^>]*>(.*?)</a>|", $post->text, $matches)) {
            /** 发送pingback */
            foreach ($matches[1] as $url) {
                $spider = Typecho_Http_Client::get();
                
                if ($spider) {
                    $spider->setTimeout(5)
                    ->send($url);
                    
                    if (!($xmlrpcUrl = $spider->getResponseHeader('x-pingback'))) {
                        if (preg_match("/<link[^>]*rel=[\"']pingback[\"'][^>]*href=[\"']([^\"']+)[\"'][^>]*>/i",
                        $spider->getResponseBody(), $out)) {
                            $xmlrpcUrl = $out[1];
                        }
                    }
                    
                    try {
                        $xmlrpc = new IXR_Client($xmlrpcUrl);
                        $xmlrpc->pingback->ping($post->permalink, $url);
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }
        }
    }
    
    /**
     * 发送pingback
     * <code>
     * $this->sendPingbacks(365);
     * </code>
     * 
     * @access public
     * @param integer $cid 内容id
     * @return void
     */
    public function sendPingback($cid)
    {
        $this->user->pass('contributor');

        if ($client = Typecho_Http_Client::get()) {        
            try {
            
                $client->setCookie('__typecho_uid', $this->request->getCookie('__typecho_uid'), 0, $this->options->siteUrl)
                ->setCookie('__typecho_authCode', $this->request->getCookie('__typecho_authCode'), 0, $this->options->siteUrl)
                ->setHeader('User-Agent', $this->options->generator)
                ->setTimeout(3)
                ->setData(array('do' => 'pingback', 'cid' => $cid))
                ->send(Typecho_Common::url('Service.do', $this->options->index));
                
            } catch (Typecho_Http_Client_Exception $e) {
                return;
            }
        }
    }
    
    /**
     * 异步请求入口
     * 
     * @access public
     * @return void
     */
    public function action()
    {
        $this->onRequest('do', 'pingback')->sendPingbackHandle();
    }
}
