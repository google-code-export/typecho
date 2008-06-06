<?php
/**
 * 聚合代理库
 * 
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 聚合代理组件
 * 
 * @category typecho
 * @package FeedProxyWidget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class FeedProxyWidget extends TypechoWidget
{
    /**
     * 解析聚合文件
     * 
     * @access public
     * @return unknown
     */
    public function parseFeed()
    {
        /** 获取feed数据 */
        $response = @file_get_contents(TypechoRequest::getParameter('url'));

        /** 处理异常 */
        if(empty($response))
        {
            die(_t('网络通讯异常'));
        }
    
        /** 开始解析 */
        try
        {
            $feed = TypechoFeed::parser($response);
        }
        catch(XML_Feed_Parser_Exception $e)
        {
            die(_t('文件解析错误'));
        }

        foreach($feed as $item)
        {
            $value = array(
                'link'  =>  $item->link,
                'title' =>  $item->title,
                'date'  =>  TypechoI18n::dateWord($item->pubDate, 
                Typecho::widget('Options')->gmtTime + Typecho::widget('Options')->timezone)
            );
            
            $this->push($value);
        }
    }

    /**
     * 聚合解析入口
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        Typecho::widget('Access')->pass('subscriber');
        TypechoRequest::bindParameter('url', array($this, 'parseFeed'));
        $this->parse('<li><a href="{link}">{title}</a><small> - {date}</small></li>');
    }
}
