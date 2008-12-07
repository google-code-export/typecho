<?php
/**
 * CURL适配器
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
 * CURL适配器
 * 
 * @author qining
 * @category typecho
 * @package Http
 */
class Typecho_Http_Client_Adapter_Curl extends Typecho_Http_Client_Adapter
{
    /**
     * 判断适配器是否可用
     * 
     * @access public
     * @return boolean
     */
    public static function isAvailable()
    {
        return function_exists('curl_version');
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
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, $this->port);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        
        /** 设置HTTP版本 */
        switch ($this->rfc) {
            case 'HTTP/1.0':
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
                break;
            case 'HTTP/1.1':
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                break;
            default:
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_NONE);
                break;
        }

        /** 设置header信息 */
        if (!empty($this->headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }

        /** POST模式 */
        if (Typecho_Http_Client::METHOD_POST == $this->method) {
            curl_setopt($ch, CURLOPT_POST, true);
            
            if (!empty($this->data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->data));
            }
            
            if (!empty($this->files)) {
                foreach ($this->files as $key => &$file) {
                    $file = '@' . $file;
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->files);
            }
        }
        
        /** 设置cookie */
        if (!empty($this->cookies)) {
            foreach ($this->cookies as $cookie) {
                curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            }
        }
        
        $response = curl_exec($ch);
        if (false === $response) {
            /** Typecho_Http_Client_Exception */
            require_once 'Typecho/Http/Client/Exception.php';
            throw new Typecho_Http_Client_Exception(curl_error($ch), 500);
        }
        
        curl_close($ch);
        return $response;
    }
}
