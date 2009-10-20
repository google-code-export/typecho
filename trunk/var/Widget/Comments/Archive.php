<?php
/**
 * 评论归档
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 评论归档组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Comments_Archive extends Widget_Abstract_Comments
{
    /**
     * 分页计算对象
     * 
     * @access private
     * @var Typecho_Db_Query
     */
    private $_countSql;
    
    /**
     * 当前页
     * 
     * @access private
     * @var integer
     */
    private $_currentPage;
    
    /**
     * 所有文章个数
     * 
     * @access private
     * @var integer
     */
    private $_total = false;
    
    /**
     * 评论类型
     * 
     * @access private
     * @var integer
     */
    private $_commentType = 'comment';
    
    /**
     * 分页数目
     * 
     * @access private
     * @var integer
     */
    private $_pageSize = 0;

    /**
     * 构造函数,初始化组件
     * 
     * @access public
     * @param mixed $request request对象
     * @param mixed $response response对象
     * @param mixed $params 参数列表
     * @return void
     */
    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);
        $this->parameter->setDefault('parentId=0&desc=0&pageSize=0&focusLast=0&type&commentPage=0');
    }
    
    /**
     * 重载内容获取
     * 
     * @access protected
     * @return void
     */
    protected function ___parentContent()
    {
        return $this->parameter->parentContent;
    }
    
    /**
     * 通过类型获取评论
     * 
     * @access protected
     * @param string $type 评论类型
     * @return void
     */
    protected function getCommentsByType($type = NULL)
    {
        if (!$this->parameter->parentId) {
            return;
        }
    
        $select = $this->select()->where('table.comments.status = ?', 'approved')
        ->where('table.comments.cid = ?', $this->parameter->parentId);
        
        if (!empty($type)) {
            $select->where('table.comments.type = ?', $type);
        }
        
        $this->_countSql = clone $select;
        
        if ($this->parameter->pageSize > 0) {
            $this->_total = empty($type) ? $this->parentContent['commentsNum'] : $this->size($this->_countSql);
            
            if ($this->parameter->focusLast && !$this->parameter->commentPage) {
                $this->_currentPage = ceil($this->_total / $this->parameter->pageSize);
            } else {
                $this->_currentPage = $this->parameter->commentPage ? $this->parameter->commentPage : 1;
            }
            
            $select->page($this->_currentPage, $this->parameter->pageSize);
        }

        $select->order('table.comments.created', $this->parameter->desc ? Typecho_Db::SORT_DESC : Typecho_Db::SORT_ASC);
        $this->db->fetchAll($select, array($this, 'push'));
    }
    
    /**
     * 输出文章评论数
     *
     * @access public
     * @param string $string 评论数格式化数据
     * @return void
     */
    public function num()
    {
        if (false === $this->_total) {
            $this->_total = empty($this->parameter->type)
            ? $this->parentContent['commentsNum'] : $this->size($this->_countSql);
        }
        
        $args = func_get_args();
        if (!$args) {
            $args[] = '%d';
        }
        
        $num = intval($this->_total);
        
        echo sprintf(isset($args[$num]) ? $args[$num] : array_pop($args), $num);
    }

    /**
     * 执行函数
     * 
     * @access public
     * @return void
     */
    public function execute()
    {
        $this->_commentType = empty($this->parameter->type) ? 'comment' : $this->parameter->type;
        $this->getCommentsByType($this->parameter->type);
    }
    
    /**
     * 输出分页
     * 
     * @access public
     * @param string $prev 上一页文字
     * @param string $next 下一页文字
     * @param int $splitPage 分割范围
     * @param string $splitWord 分割字符
     * @return void
     */
    public function pageNav($prev = '&laquo;', $next = '&raquo;', $splitPage = 3, $splitWord = '...')
    {
        if ($this->parameter->pageSize > 0) {
            $pageRow = $this->parentContent;
            $pageRow['commentType'] = $this->_commentType;
            $pageRow['permalink'] = $pageRow['pathinfo'];
            
            $query = Typecho_Router::url($this->_commentType . '_page',
                $pageRow, $this->options->index);

            /** 使用盒状分页 */
            $nav = new Typecho_Widget_Helper_PageNavigator_Box($this->_total, $this->_currentPage, $this->parameter->pageSize, $query);
            $nav->setPageHolder($this->_commentType . 'Page');
            $nav->render($prev, $next, $splitPage, $splitWord);
        }
    }
}
