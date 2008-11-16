<?php
/**
 * 格式化聚合XML数据,整合自Univarsel Feed Writer
 * 
 * @author Anis uddin Ahmad <anisniit@gmail.com>
 * @category typecho
 * @package UnivarselFeedWriter
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id: Feed.php 219 2008-05-27 09:06:15Z magike.net $
 */

/**
 * UnivarselFeedWriter
 * 
 * @author qining
 * @category typecho
 * @package UnivarselFeedWriter
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Typecho_Feed
{
    /** 定义RSS0.91类型 */
    const RSS09 = 'RSS 0.9';
    
    /** 定义RSS0.91类型 */
    const RSS091 = 'RSS 0.91';
    
    /** 定义RSS0.92类型 */
    const RSS092 = 'RSS 0.92';

    /** 定义RSS 1.0类型 */
    const RSS1 = 'RSS 1.0';
    
    /** 定义RSS 2.0类型 */
    const RSS2 = 'RSS 2.0';
    
    /** 定义ATOM 0.3类型 */
    const ATOM03 = 'ATOM 0.3';
    
    /** 定义ATOM 1.0类型 */
    const ATOM1 = 'ATOM 1.0';
    
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
     * @return Typecho_Feed_Writer
     */
    public static function generator($type = self::RSS2)
    {
        /** UnivarselFeedWriter */
        require_once 'Typecho/Feed/Writer.php';
        
        return new Typecho_Feed_Writer($type);
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
        if (self::RSS1 == $type || self::RSS2 == $type) {
            return self::DATE_RSS;
        } else {
            return self::DATE_ATOM;
        }
    }
}
