<?php
/**
 * 聚合代理库
 * 
 * @author qining
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入聚合库支持 **/
require_once __TYPECHO_LIB_DIR__ . '/Feed.php';

/**
 * 聚合代理组件
 * 
 * @author qining
 * @category typecho
 * @package FeedProxyWidget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class FeedProxyWidget extends TypechoWidget
{
    /**
     * 聚合解析入口
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        /** 获取feed数据 */
        $response = Typecho::httpRequest('http://localhost/typecho/index.php/feed/rss');
        
        /** 处理异常 */
        if(empty($response))
        {
            die('error');
        }
    
        /** 开始解析 */
        try
        {
            $feed = TypechoFeed::parser($response['body']);
        }
        catch(XML_Feed_Parser_Exception $e)
        {
            die('error');
        }

        foreach($feed as $item)
        {
            echo $item->date;
        }
    }
}
