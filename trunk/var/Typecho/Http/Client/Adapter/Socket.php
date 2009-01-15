<?php
/**
 * Socket适配器
 * 
 * @author qining
 * @category typecho
 * @package Http
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** Typecho_Http_Client_Adapter */
require_once 'Typecho/Http/Client/Adapter.php';

/**
 * Socket适配器
 * 
 * @author qining
 * @category typecho
 * @package Http
 */
class Typecho_Http_Client_Adapter_Socket extends Typecho_Http_Client_Adapter
{
    /**
     * 判断适配器是否可用
     * 
     * @access public
     * @return boolean
     */
    public static function isAvailable()
    {
        return ini_get('allow_url_fopen');
    }
    
    /**
     * 发送请求
     * 
     * @access public
     * @param string $url 请求地址
     * @return string
     */
    public function httpSend($url)
    {
        $eol = Typecho_Http_Client::EOL;
        $request = $this->method . ' ' . $this->path . ' ' . $this->rfc . $eol;
        $request .= 'Host: ' . $this->host . $eol;
        $request .= 'Accept: */*' . $eol;
        $request .= 'Cache-Control: no-cache' . $eol;
        $request .= 'Connection: Close' . $eol;
        
        /** 设置header信息 */
        if (!empty($this->headers)) {
            foreach ($this->headers as $key => $val) {
                $request .= $key . ': ' . $val . $eol;
            }
        }
        
        /** 设置cookie信息 */
        if (!empty($this->cookies)) {
            foreach ($this->cookies as $val) {
                $request .= 'Set-Cookie: ' . $val . $eol;
            }
        }
        
        /** 发送POST信息 */
        if (Typecho_Http_Client::METHOD_POST == $this->method) {
            if (empty($this->files)) {
                $content = is_array($this->data) ? http_build_query($this->data) : $this->data;
                $request .= 'Content-Length: ' . strlen($content) . $eol;
                
                if (!isset($this->headers['content-type'])) {
                    $request .= 'Content-Type: application/x-www-form-urlencoded' . $eol;
                }
                
                $request .= $eol;
                $request .= $content;
            } else {
                $boundary = '---------------------------' . substr(md5(uniqid()), 0, 16);
                $content .= $eol . $boundary;
                
                if (!empty($this->data)) {
                    foreach ($this->data as $key => $val) {
                        $content .= $eol . 'Content-Disposition: form-data; name="' . $key . '"' . $eol . $eol;
                        $content .= $val . $eol;
                        $content .= $boundary;
                    }
                }
                
                foreach ($this->files as $key => $file) {
                    $content .= $eol . 'Content-Disposition: form-data; name="' . $key . '"; filename="' . $file . '"' . $eol;
                    $content .= 'Content-Type: ' . mime_content_type($file) . $eol . $eol;
                    $content .= file_get_contents($file) . $eol;
                    $content .= $boundary;
                }
                
                $content .= '--' . $eol;
                $request .= 'Content-Length: ' . strlen($content) . $eol;
                $request .= 'Content-Type: multipart/form-data; boundary=' . $boundary;
                $request .= $eol;
                $request .= $content;
            }
        } else {
            $request .= $eol;
        }
        
        /** 打开连接 */
        $socket = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        if (false === $socket) {
            throw new Typecho_Http_Client_Exception($errno . ':' . $errstr, 500);
        }
        
        /** 发送数据 */
        fwrite($socket, $request);
        stream_set_timeout($socket, $this->timeout);
        stream_set_blocking($socket, 0);
        $info = stream_get_meta_data($socket);
        $response = '';
        
        while (!feof($socket)  && !$info['timed_out']) {
            $response .= fgets($socket, 4096);
        }
        
        fclose($socket);
        return $response;
    }
    
    /**
     * 获取回执身体
     * 
     * @access public
     * @return string
     */
    public function getResponseBody()
    {
        /** 支持chunked编码 */
        if ('chunked' == $this->getResponseHeader('Transfer-Encoding')) {
            $parts = explode("\r\n", $this->reponseBody, 2);
            $counter = hexdec($parts[0]);
            $this->reponseBody = substr($parts[1], 0, $counter);
        }
        
        return $this->reponseBody;
    }
}
