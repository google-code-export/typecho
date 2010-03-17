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
     * 用于分割的评论id
     * 
     * @access private
     * @var integer
     */
    private $_splitCommentId = 0;

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
     * 评论回调函数
     * 
     * @access private
     * @param string $before 在评论之前输出
     * @param string $after 在评论之后输出
     * @param string $singleCommentOptions 单个评论自定义选项
     * @return void
     */
    private function threadedCommentsCallback($before, $after, $singleCommentOptions)
    {
        if ($this->_customThreadedCommentsCallback) {
            return threadedComments($this, $before, $after, $singleCommentOptions);
        }
        
        $commentClass = '';
        if ($this->authorId) {
            if ($this->authorId == $this->ownerId) {
                $commentClass .= ' comment-by-author';
            } else {
                $commentClass .= ' comment-by-user';
            }
        } 
        
        $commentLevelClass = $this->_levels > 0 ? ' comment-child' : ' comment-parent';
?>
<li id="<?php $this->theId(); ?>" class="comment-body<?php
    if ($this->_levels > 0) {
        echo ' comment-child';
        $this->levelsAlt(' comment-level-odd', ' comment-level-even');
    } else {
        echo ' comment-parent';
    }
    $this->alt(' comment-odd', ' comment-even');
    echo $commentClass;
?>">
    <div class="comment-author">
        <?php $this->gravatar($singleCommentOptions->avatarSize, $singleCommentOptions->defaultAvatar); ?>
        <cite class="fn"><?php $singleCommentOptions->beforeAuthor();
        $this->author();
        $singleCommentOptions->afterAuthor(); ?></cite>
    </div>
    <div class="comment-meta">
        <a href="<?php $this->permalink(); ?>"><?php $singleCommentOptions->beforeDate();
        $this->date($singleCommentOptions->dateFormat);
        $singleCommentOptions->afterDate(); ?></a>
    </div>
    <?php $this->content(); ?>
    <?php if ($this->children) { ?>
    <div class="comment-children">
        <?php $this->threadedComments($before, $after, $singleCommentOptions); ?>
    </div>
    <?php } ?>
    <div class="comment-reply">
        <?php $this->reply($singleCommentOptions->replyWord); ?>
    </div>
</li>
<?php
    }
    
    /**
     * 获取当前评论链接
     *
     * @access protected
     * @return string
     */
    protected function ___permalink()
    {

        if ($this->options->commentsPageBreak) {            
            $pageRow = array('permalink' => $this->parentContent['pathinfo'], 'commentPage' => $this->_currentPage);
            return Typecho_Router::url('comment_page',
                        $pageRow, $this->options->index) . '#' . $this->theId;
        }
        
        return $this->parentContent['permalink'] . '#' . $this->theId;
    }

    /**
     * 子评论
     *
     * @access protected
     * @return array
     */
    protected function ___children()
    {
        return $this->options->commentsThreaded && !$this->isTopLevel && isset($this->_threadedComments[$this->coid]) 
            ? $this->_threadedComments[$this->coid] : array();
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
     * 输出文章评论数
     *
     * @access public
     * @param string $string 评论数格式化数据
     * @return void
     */
    public function num()
    {
        if (false === $this->_total) {
            $this->_total = !$this->options->commentsThreaded && !$this->options->commentsShowCommentOnly
            ? $this->parameter->commentsNum : $this->size($this->_countSql);
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
        $threadedSelect = NULL;
        
        if ($this->options->commentsShowCommentOnly) {
            $select->where('table.comments.type = ?', 'comment');
        }
        
        if ($this->options->commentsThreaded) {
            $threadedSelect = clone $select;
            $select->where('table.comments.parent = ?', 0);
        }

        $this->_countSql = clone $select;

        if ($this->options->commentsPageBreak) {
            $this->_total = !$this->options->commentsThreaded && !$this->options->commentsShowCommentOnly
            ? $this->parameter->commentsNum : $this->size($this->_countSql);

            if ('last' == $this->options->commentsPageDisplay && !$this->parameter->commentPage) {
                $this->_currentPage = ceil($this->_total / $this->options->commentsPageSize);
            } else {
                $this->_currentPage = $this->parameter->commentPage ? $this->parameter->commentPage : 1;
            }

            $select->page($this->_currentPage, $this->options->commentsPageSize);
        }

        $select->order('table.comments.coid', $this->options->commentsOrder);
        $this->db->fetchAll($select, array($this, 'push'));
        
        if ($threadedSelect) {
            $threadedSelect->where('table.comments.parent <> ? AND table.comments.coid > ?', 0, $this->_splitCommentId)
            ->order('table.comments.coid', $this->options->commentsOrder);
            $threadedComments = $this->db->fetchAll($threadedSelect, array($this, 'filter'));
            
            foreach ($threadedComments as $comment) {
                $this->_threadedComments[$comment['parent']][$comment['coid']] = $comment;
            }
        }
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

        // 取出本页最小值
        if ('DESC' == $this->options->commentsOrder || 0 == $this->_splitCommentId) {
            $this->_splitCommentId = $value['coid'];
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
            $pageRow = $this->parameter->parentContent;
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
     * @param Typecho_Config $singleCommentOptions 单个评论自定义选项
     * @return void
     */
    public function threadedComments($before = '', $after = '', $singleCommentOptions = NULL)
    {
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
                $this->threadedCommentsCallback($before, $after, $singleCommentOptions);
                $this->row = $tmp;
            }

            //在子评论之后输出
            echo $after;

            $this->sequence --;
            $this->_levels --;
        }
    }
    
    /**
     * 列出评论
     * 
     * @access private
     * @param string $before 在评论之前输出
     * @param string $after 在评论之后输出
     * @param mixed $singleCommentOptions 单个评论自定义选项
     * @return void
     */
    public function listComments($before = '<ol class="comment-list">', $after = '</ol>', $singleCommentOptions = NULL)
    {
        if ($this->have()) {
            //初始化一些变量
            $parsedSingleCommentOptions = Typecho_Config::factory($singleCommentOptions);
            $parsedSingleCommentOptions->setDefault(array(
                'beforeAuthor'  =>  '',
                'afterAuthor'   =>  '',
                'beforeDate'    =>  '',
                'afterDate'     =>  '',
                'dateFormat'    =>  $this->options->commentDateFormat,
                'replyWord'     =>  _t('回复'),
                'avatarSize'    =>  32,
                'defaultAvatar' =>  NULL
            ));
        
            echo $before;
            
            while ($this->next()) {
                $this->threadedCommentsCallback($before, $after, $parsedSingleCommentOptions);
            }
            
            echo $after;
        }
    }
    
    /**
     * 重载alt函数,以适应多级评论
     * 
     * @access public
     * @return void
     */
    public function alt()
    {
        $args = func_get_args();
        $num = func_num_args();
        
        $sequence = $this->_levels <= 0 ? $this->sequence :
        array_search($this->coid, $this->_threadedComments[$this->parent]) + 1;
        
        $split = $sequence % $num;
        echo $args[(0 == $split ? $num : $split) -1];
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
    
    /**
     * 评论回复链接
     * 
     * @access public
     * @param string $word 回复链接文字
     * @return void
     */
    public function reply($word = '')
    {
        if ($this->options->commentsThreaded && !$this->isTopLevel) {
            $word = empty($word) ? _t('回复') : $word;
            $this->pluginHandle()->trigger($plugged)->reply($word, $this);
            
            if (!$plugged) {
                echo '<a href="' . substr($this->permalink, 0, - strlen($this->theId) - 1) . '?replyTo=' . $this->coid .
                    '#' . $this->parameter->respondId . '" rel="nofollow" onclick="return TypechoComment.reply(\'' .
                    $this->theId . '\', ' . $this->coid . ');">' . $word . '</a>';
            }
        }
    }
    
    /**
     * 取消评论回复链接
     * 
     * @access public
     * @param string $word 取消回复链接文字
     * @return void
     */
    public function cancelReply($word = '')
    {
        if ($this->options->commentsThreaded) {
            $word = empty($word) ? _t('取消回复') : $word;
            $this->pluginHandle()->trigger($plugged)->cancelReply($word, $this);
            
            if (!$plugged) {
                $replyId = $this->request->filter('int')->replyTo;
                echo '<a id="cancel-comment-reply-link" href="' . $this->parameter->parentContent['permalink'] . '#' . $this->parameter->respondId .
                '" rel="nofollow"' . ($replyId ? '' : ' style="display:none"') . ' onclick="return TypechoComment.cancelReply();">' . $word . '</a>';
            }
        }
    }
}
