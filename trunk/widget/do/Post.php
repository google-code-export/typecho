<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 数据提交基础类库
 * 
 * @package Widget
 */
class Post extends TypechoWidget
{
    /**
     * 数据库对象
     * 
     * @access protected
     * @var TypechoDb
     */
    protected $db;
    
    /**
     * 构造函数,初始化数据库
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->db = TypechoDb::get();
    }

    /**
     * 返回来路
     * 
     * @access protected
     * @param string $anchor 锚点地址
     * @return void
     * @throws TypechoWidgetException
     */
    protected function goBack($anchor = NULL)
    {
        //判断来源
        if(empty($_SERVER['HTTP_REFERER']) || 0 === strpos($_SERVER['HTTP_REFERER'], widget('Options')->index))
        {
            throw new TypechoWidgetException(_t('无法返回原网页'));
        }
        
        typechoRedirect($_SERVER['HTTP_REFERER'] . $anchor, false);
    }
    
    /**
     * 直接跳向地址
     * 
     * @access protected
     * @param string $url 跳入的地址
     * @return void
     */
    protected function goForward($url)
    {
        typechoRedirect(widget('Options')->siteURL . $url, false);
    }
    
    /**
     * 执行异步任务
     * 
     * @access protected
     * @param string $jobName
     * @return void
     */
    protected function doJob($jobName)
    {
        $args = func_get_args();
        array_shift($args);
        
        typechoHttpSender(TypechoRoute::parse('job', array('job' => $jobName), widget('Options')->index),
        widget('Options')->generator,
        NULL,
        array('args' => $args),
        NULL,
        2,
        NULL,
        $_SERVER['SERVER_ADDR']);
    }
    
    /**
     * 获取递增字段值
     * 
     * @access protected
     * @param unknown $table
     * @return unknown
     */
    protected function getAutoIncrement($table)
    {
        $table = __TYPECHO_DB_PREFIX__ . $table;
        $row = $this->fetchRow("SHOW TABLE STATUS LIKE '{$table}'");
        if($row)
        {
            return empty($row['Auto_increment']) ? 1 : $row['Auto_increment'];
        }
        else
        {
            return 1;
        }
    }
}
