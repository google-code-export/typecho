<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 内容分页抽象类
 *
 * @package Widget
 */
abstract class Typecho_Widget_Helper_PageNavigator
{
    /**
     * 记录总数
     *
     * @access protected
     * @var integer
     */
    protected $_total;

    /**
     * 页面总数
     *
     * @access protected
     * @var integer
     */
    protected $_totalPage;

    /**
     * 当前页面
     *
     * @access protected
     * @var integer
     */
    protected $_currentPage;

    /**
     * 每页内容数
     *
     * @access protected
     * @var integer
     */
    protected $_pageSize;

    /**
     * 页面链接模板
     *
     * @access protected
     * @var string
     */
    protected $_pageTemplate;

    /**
     * 构造函数,初始化页面基本信息
     *
     * @access public
     * @param integer $total 记录总数
     * @param integer $page 当前页面
     * @param integer $pageSize 每页记录数
     * @param string $pageTemplate 页面链接模板
     * @return void
     */
    public function __construct($total, $currentPage, $pageSize, $pageTemplate)
    {
        $this->_total = $total;
        $this->_totalPage = ceil($total / $pageSize);
        $this->_currentPage = $currentPage;
        $this->_pageSize = $pageSize;
        $this->_pageTemplate = $pageTemplate;
    }
    
    /**
     * 输出方法
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        /** 载入异常支持 */
        require_once 'Typecho/Widget/Exception.php';
        throw new Typecho_Widget_Exception(get_class($this) . ':' . __METHOD__, Typecho_Exception::RUNTIME);
    }
}
