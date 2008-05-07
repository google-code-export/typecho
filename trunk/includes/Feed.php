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

/** UnivarselFeedWriter */
require_once 'Feed/FeedWriter.php';

/** UnivarselFeedItem */
require_once 'Feed/FeedItem.php';

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
    
    /** 定义Feed类型 */
    private static $_type;
    
    /**
     * 创建rss1 Feed对象
     * 
     * @access public
     * @return FeedWriter
     */
    public static function rss1()
    {
        self::$_type = self::RSS1;
        return new FeedWriter(self::RSS1);
    }
    
    /**
     * 创建rss2 Feed对象
     * 
     * @access public
     * @return FeedWriter
     */
    public static function rss2()
    {
        self::$_type = self::RSS2;
        return new FeedWriter(self::RSS2);
    }
    
    /**
     * 创建atom Feed对象
     * 
     * @access public
     * @return FeedWriter
     */
    public static function atom()
    {
        self::$_type = self::ATOM;
        return new FeedWriter(self::ATOM);
    }
    
    /**
     * 创建原子对象
     * 
     * @access public
     * @return FeedItem
     */
    public static function item()
    {
        return new FeedWriter(self::$_type);
    }
}
