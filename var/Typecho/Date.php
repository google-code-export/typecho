<?php

/** Typecho_I18n */
require_once 'Typecho/I18n.php';

/**
 * 日期处理
 * 
 * @author qining
 * @category typecho
 * @package Date
 */
class Typecho_Date
{
    /**
     * GMT时间戳
     * 
     * @access private
     * @var integer
     */
    private $_gmtTime;
    
    /**
     * 时区偏移
     * 
     * @access private
     * @var integer
     */
    private $_timezone;
    
    /**
     * 服务器时区偏移
     * 
     * @access private
     * @var integer
     */
    private $_serverTimezone;
    
    /**
     * 偏移后的时间
     * 
     * @access private
     * @var integer
     */
    private $_time;

    /**
     * 初始化参数
     * 
     * @access public
     * @param integer $gmtTime GMT时间戳
     * @param integer $timezone 时区偏移
     * @return void
     */
    public function __construct($gmtTime, $timezone)
    {
        $this->_gmtTime = $gmtTime;
        $this->_timezone = $timezone;
        $this->_serverTimezone = idate('Z');
        $this->_time = $gmtTime + ($this->_timezone - $this->_serverTimezone);
    }
    
    /**
     * 获取格式化时间
     * 
     * @access public
     * @param string $format 时间格式
     * @return string
     */
    public function format($format)
    {
        return date($format, $this->_time);
    }
    
    /**
     * 获取国际化偏移时间
     * 
     * @access public
     * @return string
     */
    public function word()
    {
        $now = self::gmtTime() + ($this->_timezone - $this->_serverTimezone);
        $from = $this->_time;
        return Typecho_I18n::dateWord($from, $now);
    }
    
    /**
     * 获取单项数据
     * 
     * @access public
     * @param string $name 名称
     * @return integer
     */
    public function __get($name)
    {
        switch ($name) {
            case 'year':
                return date('Y', $this->_time);
            case 'month':
                return date('m', $this->_time);
            case 'day':
                return date('d', $this->_time);
            default:
                return;
        }
    }
    
    /**
     * 获取GMT时间
     * 
     * @access public
     * @return integer
     */
    public static function gmtTime()
    {
        return @gmmktime();
    }
}
