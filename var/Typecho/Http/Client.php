<?php
/**
 * Http客户端
 * 
 * @author qining
 * @category typecho
 * @package Http
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * Http客户端
 * 
 * @author qining
 * @category typecho
 * @package Http
 */
class Typecho_Http_Client
{
    /** POST方法 */
    const METHOD_POST = 'POST';
    
    /** GET方法 */
    const METHOD_GET = 'GET';

    /** PUT方法 */
    const METHOD_PUT = 'PUT';
    
    /** 定义行结束符 */
    const EOL = "\r\n";
    
    /**
     * 获取可用的连接
     * 
     * @access public
     * @return Typecho_Http_Client_Adapter
     */
    public static function get()
    {
        $adapters = func_get_args();
        
        foreach ($adapters as $adapter) {
            require_once 'Typecho/Http/Client/Adapter/' . $adapter . '.php';
            $adapterName = 'Typecho_Http_Client_Adapter_' . $adapter;
            if (call_user_func(array($adapterName, 'isAvailable'))) {
                return new $adapterName();
            }
        }
        
        return false;
    }
}
