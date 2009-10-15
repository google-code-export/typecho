<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */
 
/**
 * 评论基类
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Abstract_Comments extends Widget_Abstract
{
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
     * 获取当前内容结构
     * 
     * @access protected
     * @return array
     */
    protected function ___parentContent()
    {
        return $this->db->fetchRow($this->widget('Widget_Abstract_Contents')->select()
        ->where('table.contents.cid = ?', $this->cid)
        ->limit(1), array($this->widget('Widget_Abstract_Contents'), 'filter'));
    }
    
    /**
     * 获取当前评论标题
     * 
     * @access protected
     * @return string
     */
    protected function ___title()
    {
        return $this->parentContent['title'];
    }
    
    /**
     * 获取当前评论链接
     * 
     * @access protected
     * @return string
     */
    protected function ___permalink()
    {
        return $this->parentContent['permalink'] . '#' . $this->theId;
    }
    
    /**
     * 获取当前评论内容
     * 
     * @access protected
     * @return string
     */
    protected function ___content()
    {
        $text = $this->parentContent['hidden'] ? _t('内容被隐藏') : $this->text;
        
        $text = $this->pluginHandle(__CLASS__)->trigger($plugged)->content($text, $this);
        if (!$plugged) {
            $text = Typecho_Common::cutParagraph($text);
        }
        
        return $this->pluginHandle(__CLASS__)->contentEx($text, $this);
    }
    
    /**
     * 输出词义化日期
     * 
     * @access protected
     * @return string
     */
    protected function ___dateWord()
    {
        return $this->date->word();
    }
    
    /**
     * 锚点id
     * 
     * @access protected
     * @return string
     */
    protected function ___theId()
    {
        return 'comment-' . $this->coid;
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
     * 获取查询对象
     * 
     * @access public
     * @return Typecho_Db_Query
     */
    public function select()
    {
        return $this->db->select('table.comments.coid', 'table.comments.cid', 'table.comments.author', 'table.comments.mail', 'table.comments.url', 'table.comments.ip',
        'table.comments.authorId', 'table.comments.ownerId', 'table.comments.agent', 'table.comments.text', 'table.comments.type', 'table.comments.status', 'table.comments.parent', 'table.comments.created')
        ->from('table.comments');
    }
    
    /**
     * 增加评论
     * 
     * @access public
     * @param array $comment 评论结构数组
     * @return integer
     */
    public function insert(array $comment)
    {
        /** 构建插入结构 */
        $insertStruct = array(
            'cid'       =>  $comment['cid'],
            'created'   =>  empty($comment['created']) ? $this->options->gmtTime : $comment['created'],
            'author'    =>  empty($comment['author']) ? NULL : $comment['author'],
            'authorId'  =>  empty($comment['authorId']) ? 0 : $comment['authorId'],
            'ownerId'   =>  empty($comment['ownerId']) ? 0 : $comment['ownerId'],
            'mail'      =>  empty($comment['mail']) ? NULL : $comment['mail'],
            'url'       =>  empty($comment['url']) ? NULL : $comment['url'],
            'ip'        =>  empty($comment['ip']) ? $this->request->getIp() : $comment['ip'],
            'agent'     =>  empty($comment['agent']) ? $_SERVER["HTTP_USER_AGENT"] : $comment['agent'],
            'text'      =>  empty($comment['text']) ? NULL : $comment['text'],
            'type'      =>  empty($comment['type']) ? 'comment' : $comment['type'],
            'status'    =>  empty($comment['status']) ? 'approved' : $comment['status'],
            'parent'    =>  empty($comment['parent']) ? 0 : $comment['parent'],
        );
        
        if (!empty($comment['coid'])) {
            $insertStruct['coid'] = $comment['coid'];
        }
        
        /** 首先插入部分数据 */
        $insertId = $this->db->query($this->db->insert('table.comments')->rows($insertStruct));
        
        /** 更新评论数 */
        $num = $this->db->fetchObject($this->db->select(array('COUNT(coid)' => 'num'))->from('table.comments')
        ->where('status = ? AND cid = ?', 'approved', $comment['cid']))->num;
        
        $this->db->query($this->db->update('table.contents')->rows(array('commentsNum' => $num))
        ->where('cid = ?', $comment['cid']));
        
        return $insertId;
    }
    
    /**
     * 更新评论
     * 
     * @access public
     * @param array $comment 评论结构数组
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function update(array $comment, Typecho_Db_Query $condition)
    {
        /** 获取内容主键 */
        $updateCondition = clone $condition;
        $updateComment = $this->db->fetchObject($condition->select('cid')->from('table.comments')->limit(1));
        
        if ($updateComment) {
            $cid = $updateComment->cid;
        } else {
            return false;
        }
    
        /** 构建插入结构 */
        $preUpdateStruct = array(
            'author'    =>  empty($comment['author']) ? NULL : $comment['author'],
            'mail'      =>  empty($comment['mail']) ? NULL : $comment['mail'],
            'url'       =>  empty($comment['url']) ? NULL : $comment['url'],
            'text'      =>  empty($comment['text']) ? NULL : $comment['text'],
            'status'    =>  empty($comment['status']) ? 'approved' : $comment['status'],
        );
        
        $updateStruct = array();
        foreach ($comment as $key => $val) {
            if ((array_key_exists($key, $preUpdateStruct))) {
                $updateStruct[$key] = $preUpdateStruct[$key];
            }
        }
        
        /** 更新评论数据 */
        $updateRows = $this->db->query($updateCondition->update('table.comments')->rows($updateStruct));
        
        /** 更新评论数 */
        $num = $this->db->fetchObject($this->db->select(array('COUNT(coid)' => 'num'))->from('table.comments')
        ->where('status = ? AND cid = ?', 'approved', $cid))->num;
        
        $this->db->query($this->update('table.contents')->rows(array('commentsNum' => $num))
        ->where('cid = ?', $cid));
        
        return $updateRows;
    }
    
    /**
     * 删除数据
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function delete(Typecho_Db_Query $condition)
    {
        /** 获取内容主键 */
        $deleteCondition = clone $condition;
        $deleteComment = $this->db->fetchObject($condition->select('cid')->from('table.comments')->limit(1));
        
        if ($deleteComment) {
            $cid = $deleteComment->cid;
        } else {
            return false;
        }
        
        /** 删除评论数据 */
        $deleteRows = $this->db->query($deleteCondition->delete('table.comments'));
        
        /** 更新评论数 */
        $num = $this->db->fetchObject($this->db->select(array('COUNT(coid)' => 'num'))->from('table.comments')
        ->where('status = ? AND cid = ?', 'approved', $cid))->num;
        
        $this->db->query($this->db->update('table.contents')->rows(array('commentsNum' => $num))
        ->where('cid = ?', $cid));
        
        return $deleteRows;
    }
    
    /**
     * 评论是否可以被修改
     * 
     * @access public
     * @param Typecho_Db_Query $condition 条件
     * @return mixed
     */
    public function commentIsWriteable(Typecho_Db_Query $condition = NULL)
    {
        if (empty($condition)) {
            if ($this->have() && ($this->user->pass('editor', true) || $this->ownerId == $this->user->uid)) {
                return true;
            }
        } else {
            $post = $this->db->fetchRow($condition->select('ownerId')->from('table.comments')->limit(1));

            if ($post && ($this->user->pass('editor', true) || $post['ownerId'] == $this->user->uid)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 按照条件计算评论数量
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function size(Typecho_Db_Query $condition)
    {
        return $this->db->fetchObject($condition->select(array('COUNT(coid)' => 'num'))->from('table.comments'))->num;
    }
    
    /**
     * 通用过滤器
     * 
     * @access public
     * @param array $value 需要过滤的行数据
     * @return array
     */
    public function filter(array $value)
    {
        $value['date'] = new Typecho_Date($value['created']);
        $value = $this->pluginHandle(__CLASS__)->filter($value, $this);
        return $value;
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
     * 输出文章发布日期
     *
     * @access public
     * @param string $format 日期格式
     * @return void
     */
    public function date($format = NULL)
    {
        echo $this->date->format($format);
    }
    
    /**
     * 输出作者相关
     * 
     * @access public
     * @param boolean $autoLink 是否自动加上链接
     * @param boolean $noFollow 是否加上nofollow标签
     * @return void
     */
    public function author($autoLink = NULL, $noFollow = NULL)
    {
        $autoLink = (NULL === $autoLink) ? $this->options->commentsShowUrl : $autoLink;
        $noFollow = (NULL === $noFollow) ? $this->options->commentsUrlNofollow : $noFollow;
    
        if ($this->url && $autoLink) {
            echo '<a href="' , $this->url , '"' , ($noFollow ? ' rel="external nofollow"' : NULL) , '>' , $this->author , '</a>';
        } else {
            echo $this->author;
        }
    }
    
    /**
     * 调用gravatar输出用户头像
     * 
     * @access public
     * @param integer $size 头像尺寸
     * @param string $rating 头像评级
     * @param string $default 默认输出头像
     * @return void
     */
    public function gravatar($size = 40, $rating = 'X', $default = NULL, $class = NULL)
    {
        echo '<img' . (empty($class) ? '' : ' class="' . $class . '"') . ' src="http://www.gravatar.com/avatar/' .
        md5($this->mail) . '?s=' . $size . '&amp;r=' . $rating . '&amp;d=' . $default . '" alt="' .
        $this->author . '" width="' . $size . '" height="' . $size . '" />';
    }
    
    /**
     * 输出评论摘要
     *
     * @access public
     * @param integer $length 摘要截取长度
     * @param string $trim 摘要后缀
     * @return void
     */
    public function excerpt($length = 100, $trim = '...')
    {
        echo Typecho_Common::subStr(strip_tags($this->content), 0, $length, $trim);
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
    public function threadedComments($before = '', $after = '', $func = 'threadedComments')
    {
        //楼层限制
        if ($this->isTopLevel) {
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
                $func($this);
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
