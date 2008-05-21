<?php
/**
 * 描述记录输出
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入分类支持 */
require_once 'Categories.php';

/**
 * 描述记录输出组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class TagsWidget extends CategoriesWidget
{
    /**
     * 分页数目
     *
     * @access protected
     * @var integer
     */
    protected $_pageSize;

    /**
     * 当前页
     *
     * @access protected
     * @var integer
     */
    protected $_currentPage;
    
    /**
     * 用于计算总数的sql对象
     * 
     * @access private
     * @var TypechoDbQuery
     */
    public $countSql;
    
    /**
     * 输出内容分页
     *
     * @access public
     * @param string $fileName 文件名
     * @return void
     */
    public function pageNav($fileName)
    {
        $args = func_get_args();
        $query = $fileName . '?page={page}';

        $num = $this->db->fetchObject($this->countSql->select('table.metas', 'COUNT(table.metas.`mid`) AS `num`'))->num;
        $nav = new TypechoWidgetNavigator($num,
                                          $this->_currentPage,
                                          $this->_pageSize,
                                          Typecho::pathToUrl($query, $this->options->adminUrl));

        $nav->makeBoxNavigator(_t('上一页'), _t('下一页'));
    }

    /**
     * 入口函数
     * 
     * @access public
     * @param string $type 类型
     * @param string $pageSize 分页参数,为0时表示不分页
     * @return unknown
     */
    public function render($pageSize = 0)
    {
        $select = $this->db->sql()->select('table.metas')->where('`type` = ?', 'tag')
        ->order('`mid`', TypechoDb::SORT_ASC);
        $this->countSql = clone $select;
        
        if($pageSize > 0)
        {
            $this->_pageSize = $pageSize;
            $this->_currentPage = (NULL === TypechoRequest::getParameter('page')) ? 1 : TypechoRequest::getParameter('page');
            $select->page($this->_currentPage, $this->_pageSize);
        }
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
