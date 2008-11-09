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
     * 获取查询对象
     * 
     * @access public
     * @return Typecho_Db_Query
     */
    public function select()
    {
        $select = $this->db->select('table.contents.cid', 'table.contents.title', 'table.contents.slug', 'table.contents.created', 'table.contents.type',
        'table.comments.coid', 'table.comments.author', 'table.comments.mail', 'table.comments.url', 'table.comments.ip',
        'table.comments.agent', 'table.comments.text', 'table.comments.mode', 'table.comments.status', 'table.comments.parent',
        array('COUNT(table.comments.cid)' => 'commentsGroupCount', 'table.comments.created' => 'date'))
        ->from('table.comments')
        ->join('table.contents', 'table.comments.cid = table.contents.cid');
        
        if($this->user->hasLogin() && !$this->user->pass('editor', true))
        {
            /** 没有达到编辑权限的用户只能访问其所属的文章 */
            $select->where('table.contents.password IS NULL OR table.contents.author = ? OR table.contents.password = ?', 
            $this->user->uid, $this->request->protectPassword);
        }
        else if(!$this->user->hasLogin())
        {
            /** 普通访问者没有任何访问隐私文章评论的权限 */
            $select->where('table.contents.password IS NULL OR table.contents.password = ?', $this->request->protectPassword);
        }
        
        return $select;
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
            'created'   =>  $this->options->gmtTime,
            'author'    =>  empty($comment['author']) ? NULL : $comment['author'],
            'mail'      =>  empty($comment['mail']) ? NULL : $comment['mail'],
            'url'       =>  empty($comment['url']) ? NULL : $comment['url'],
            'ip'        =>  empty($comment['ip']) ? $this->request->getClientIp() : $comment['ip'],
            'agent'     =>  empty($comment['agent']) ? $_SERVER["HTTP_USER_AGENT"] : $comment['agent'],
            'text'      =>  empty($comment['text']) ? NULL : $comment['text'],
            'mode'      =>  empty($comment['mode']) ? 'comment' : $comment['mode'],
            'status'    =>  empty($comment['status']) ? 'approved' : $comment['status'],
            'parent'    =>  empty($comment['parent']) ? '0' : $comment['parent'],
        );
        
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
        
        if($updateComment)
        {
            $cid = $updateComment->cid;
        }
        else
        {
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
        foreach($comment as $key => $val)
        {
            if(isset($preUpdateStruct[$key]))
            {
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
        
        if($deleteComment)
        {
            $cid = $deleteComment->cid;
        }
        else
        {
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
     * 按照条件计算评论数量
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function count(Typecho_Db_Query $condition)
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
        /** 取出所有分类 */
        $value = $this->widget('Widget_Abstract_Contents')->filter($value);
        
        $value['permalink'] = $value['permalink'] . '#comments-' . $value['coid'];
        
        $value = $this->plugin(__CLASS__)->filter($value);
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
        return parent::push($value);
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
        echo date(empty($format) ? $this->options->commentDateFormat : $format, $this->date + $this->options->timezone);
    }
    
    /**
     * 输出词义化日期
     * 
     * @access public
     * @return void
     */
    public function dateWord()
    {
        echo Typecho_I18n::dateWord($this->date + $this->options->timezone, $this->options->gmtTime + $this->options->timezone);
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
    
        if($this->url && $autoLink)
        {
            echo '<a href="' , $this->url , '"' , ($noFollow ? ' rel="external nofollow"' : NULL) , '>' , $this->author , '</a>';
        }
        else
        {
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
    public function gravatar($size = 40, $rating = 'X', $default = NULL)
    {
        echo '<img src="http://www.gravatar.com/avatar.php?gravatar_id=' . md5($this->mail) . '&size=' .
        $rating . '&default=' . $default . '" alt="' . $this->author . '" width="' . $size . '" height="' . $size . '" />';
    }
    
    /**
     * 输出格式化后的内容
     * 
     * @access public
     * @return void
     */
    public function content()
    {
        echo Typecho_Common::cutParagraph($this->text);
    }
    
    /**
     * 输出锚点id
     * 
     * @access public
     * @return void
     */
    public function theId()
    {
        echo 'comments-' , $this->coid;
    }
    
    /**
     * 输出文章摘要
     *
     * @access public
     * @param integer $length 摘要截取长度
     * @param string $trim 摘要后缀
     * @return void
     */
    public function excerpt($length = 100, $trim = '...')
    {
        echo Typecho_Common::subStr(Typecho_Common::stripTags($this->text), 0, $length, $trim);
    }
}
