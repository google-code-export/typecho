<?php
/**
 * 格式化聚合XML数据,整合自Univarsel Feed Writer
 * 
 * @author Anis uddin Ahmad <anisniit@gmail.com>
 * @category typecho
 * @package UnivarselFeedWriter
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */


/** 载入api支持 */
require_once 'Typecho.php';

/** UnivarselFeedWriter */
require_once 'Feed/FeedWriter.php';

/** UnivarselFeedItem */
require_once 'Feed/FeedItem.php';

/**
 * We are extending TypechoException
 */
require_once 'Exception.php';

/** Feed Parser */
require_once 'Feed/Parser.php';

/**
 * UnivarselFeedWriter
 * 
 * @author qining
 * @category typecho
 * @package UnivarselFeedWriter
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class TypechoFeed
{
    /** 定义RSS1类型 */
    const RSS1 = 'RSS 1.0';
    
    /** 定义RSS2类型 */
    const RSS2 = 'RSS 2.0';
    
    /** 定义ATOM类型 */
    const ATOM = 'ATOM';
    
    /** 定义RSS时间格式 */
    const DATE_RSS = 'r';
    
    /** 定义ATOM时间格式 */
    const DATE_ATOM = 'c';
    
    /** 定义行结束符 */
    const EOL = "\n";

    /**
     * 创建Feed对象
     * 
     * @access public
     * @return FeedWriter
     */
    public static function generator($type = self::RSS2)
    {
        return new FeedWriter($type);
    }
    
    /**
     * 创建Parser对象
     * 
     * @access public
     * @param string $xml xml字符串
     * @return XML_Feed_Parser
     */
    public static function parser($xml)
    {
        return new XML_Feed_Parser($xml);
    }
    
    /**
     * 获取Feed时间格式
     * 
     * @access public
     * @param string $type 聚合类型
     * @return string
     */
    public static function dateFormat($type = self::RSS2)
    {
        if(self::RSS1 == $type || self::RSS2 == $type)
        {
            return self::DATE_RSS;
        }
        else
        {
            return self::DATE_ATOM;
        }
    }
}
