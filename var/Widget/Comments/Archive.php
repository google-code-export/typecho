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
     * 子父级评论关系
     *
     * @access private
     * @var array
     */
    private $_threadedComments;

    /**
     * 递归深度
     *
     * @access private
     * @var integer
     */
    private $_levels = 0;
    
    /**
     * 多级评论回调函数
     * 
     * @access private
     * @var mixed
     */
    private $_customThreadedCommentsCallback = false;

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
        $this->parameter->setDefault('parentId=0&commentPage=0&commentsNum=0');
        
        /** 初始化回调函数 */
        if (function_exists('threadedComments')) {
            $this->_customThreadedCommentsCallback = true;
        }
    }
    
    /**
     * 多级评论回调
     * 
     * @access private
     * @param Widget_Comments_Archive $comments 评论组件
     * @return void
     */
    private function threadedCommentsCallback($comments)
    {
        /** 直接返回 */
        if ($this->_customThreadedCommentsCallback) {
            return threadedComments($comments);
        }
        
        
    }

    /**
     * 子评论
     *
     * @access protected
     * @return array
     */
    protected function ___children()
    {
        $result = array();

        if (isset($this->_threadedComments[$this->coid])) {
            //深度清零
            if (!$this->parent) {
                $this->_deep = 0;
            }

            $threadedComments = $this->_threadedComments[$this->coid];
            foreach ($threadedComments as $coid) {
                $result[] = $this->stack[$coid];
                unset($this->stack[$coid]);
            }
        }

        return $result;
    }

    /**
     * 楼层数
     *
     * @access protected
     * @return integer
     */
    protected function ___levels()
    {
        return $this->_levels + 1;
    }

    /**
     * 是否到达顶层
     *
     * @access protected
     * @return boolean
     */
    protected function ___isTopLevel()
    {
        return $this->_levels > $this->options->commentsMaxNestingLevels - 2;
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
     * 返回堆栈每一行的值
     *
     * @return array
     */
    public function next()
    {
        if ($this->stack) {

            // fix issue 379
            do {
                $this->row = &$this->stack[key($this->stack)];
                next($this->stack);
            } while ($this->row && 0 != $this->row['parent'] && isset($this->stack[$this->row['parent']]));

            $this->sequence ++;
        }

        if (!$this->row) {
            reset($this->stack);
            $this->sequence = 0;
            return false;
        }

        return $this->row;
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
            $this->_total = !$this->options->commentsShowCommentOnly
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
        if (!$this->parameter->parentId) {
            return;
        }

        $select = $this->select()->where('table.comments.status = ?', 'approved')
        ->where('table.comments.cid = ?', $this->parameter->parentId);

        if ($this->options->commentsShowCommentOnly) {
            $select->where('table.comments.type = ?', 'comment');
        }

        $this->_countSql = clone $select;

        if ($this->options->commentsPageBreak) {
            $this->_total = empty($type) ? $this->parameter->commentsNum : $this->size($this->_countSql);

            if ('last' == $this->options->commentsPageDisplay && !$this->parameter->commentPage) {
                $this->_currentPage = ceil($this->_total / $this->options->commentsPageSize);
            } else {
                $this->_currentPage = $this->parameter->commentPage ? $this->parameter->commentPage : 1;
            }

            $select->page($this->_currentPage, $this->options->commentsPageSize);
        }

        $select->order('table.comments.created', $this->options->commentsOrder);
        $this->db->fetchAll($select, array($this, 'push'));
    }

    /**
     * 将每行的值压入堆栈
     *
     * @access public
     * @param array $value 每行的值
     * @return array
     */
    public function push(array $value)
    {
        $value = $this->filter($value);

        //存储子父级关系
        if ($value['parent']) {
            $this->_threadedComments[$value['parent']][] = $value['coid'];
        }

        //将行数据按顺序置位
        $this->row = $value;
        $this->length ++;

        //重载push函数,使用coid作为数组键值,便于索引
        $this->stack[$value['coid']] = $value;
        return $value;
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
        if ($this->options->commentsPageBreak && $this->_total > $this->options->commentsPageSize) {
            $pageRow = $this->parentContent;
            $pageRow['permalink'] = $pageRow['pathinfo'];

            $query = Typecho_Router::url('comment_page', $pageRow, $this->options->index);

            /** 使用盒状分页 */
            $nav = new Typecho_Widget_Helper_PageNavigator_Box($this->_total, $this->_currentPage, $this->options->commentsPageSize, $query);
            $nav->setPageHolder('commentPage');
            $nav->setAnchor('comments');
            $nav->render($prev, $next, $splitPage, $splitWord);
        }
    }

    /**
     * 递归输出评论
     *
     * @access protected
     * @param string $before 在子评论之前输出
     * @param string $after 在子评论之后输出
     * @param string $func 回调函数
     * @return void
     */
    public function threadedComments($before = '', $after = '')
    {
        //楼层限制
        if (!$this->options->commentsThreaded || $this->isTopLevel) {
            return;
        }

        $children = $this->children;
        if ($children) {
            //缓存变量便于还原
            $tmp = $this->row;
            $this->_levels ++;
            $this->sequence ++;

            //在子评论之前输出
            echo $before;

            foreach ($children as $child) {
                $this->row = $child;
                $this->threadedCommentsCallback($this);
                $this->row = $tmp;
            }

            //在子评论之后输出
            echo $after;

            $this->sequence --;
            $this->_levels --;
        }
    }

    /**
     * 根据深度余数输出
     *
     * @access public
     * @param string $param 需要输出的值
     * @return void
     */
    public function levelsAlt()
    {
        $args = func_get_args();
        $num = func_num_args();
        $split = $this->_levels % $num;
        echo $args[(0 == $split ? $num : $split) -1];
    }
}
