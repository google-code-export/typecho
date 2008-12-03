<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 异常处理组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_ExceptionHandle extends Typecho_Widget
{
    /**
     * 全局选项
     * 
     * @access protected
     * @var Widget_Options
     */
    protected $options;
    
    /**
     * 数据库对象
     * 
     * @access protected
     * @var Typecho_Db
     */
    protected $db;

    /**
     * 重载构造函数
     * 
     * @access public
     * @param Exception $excepiton 抛出的异常
     * @return void
     */
    public function __construct(Exception $excepiton)
    {
        parent::__construct(array(
            'code'      =>  $excepiton->getCode(),
            'message'   =>  $excepiton->getMessage(),
            'trace'     =>  $excepiton->getTrace()
        ));
    }
    
    /**
     * 准备一些常用组件
     * 
     * @access public
     * @return void
     */
    public function prepare()
    {
        /** 如果数据库可用 */
        //~ 503和500都是内部程序错误
        if (503 != $this->parameter->code) {
            /** 初始化数据库 */
            $this->db = Typecho_Db::get();
        
            /** 初始化常用组件 */
            $this->options = $this->widget('Widget_Options');
        }
    }
    
    /**
     * 初始化函数
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        
    }
}
