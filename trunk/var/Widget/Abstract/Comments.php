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
class Widget_Abstract_Comments extends Typecho_Widget_Abstract_Dataset
{
    /**
     * 实例化的抽象Meta类
     * 
     * @access protected
     * @var MetasWidget
     */
    protected $abstractContentsWidget;

    /**
     * 数据库对象
     *
     * @access protected
     * @var TypechoDb
     */
    protected $db;

    /**
     * 实例化的配置对象
     *
     * @access protected
     * @var TypechoWidget
     */
    protected $options;

    /**
     * 实例化的权限对象
     *
     * @access protected
     * @var TypechoWidget
     */
    protected $access;

    /**
     * 插件
     *
     * @access protected
     * @var TypechoPlugin
     */
    protected $plugin;

    /**
     * 构造函数,初始化数据库
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        /** 初始化数据库 */
        parent::__construct();
        
        /** 初始化常用widget */
        $this->options = Typecho_API::factory('Widget_Abstract_Options');
        $this->access = Typecho_API::factory('Widget_Users_Current');
        $this->abstractContentsWidget = Typecho_API::factory('Widget_Abstract_Contents');
        
        /** 初始化插件 */
        $this->plugin = _p(__FILE__, 'Filter');
    }
    
    /**
     * 获取查询对象
     * 
     * @access public
     * @return Typecho_Db_Query
     */
    public function select()
    {
        return $this->db->sql()->select('table.comments', 'table.contents.`cid`, table.contents.`title`, table.contents.`slug`, table.contents.`created`, table.contents.`type`,
        table.comments.`coid`, table.comments.`created` AS `date`, table.comments.`author`, table.comments.`mail`, table.comments.`url`, table.comments.`ip`,
        table.comments.`agent`, table.comments.`text`, table.comments.`mode`, table.comments.`status`, table.comments.`parent`')
        ->join('table.contents', 'table.comments.`cid` = table.contents.`cid`');
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
            'created'   =>  Typecho_API::widget('Options')->gmtTime,
            'author'    =>  empty($comment['author']) ? NULL : $comment['author'],
            'mail'      =>  empty($comment['mail']) ? NULL : $comment['mail'],
            'url'       =>  empty($comment['url']) ? NULL : $comment['url'],
            'ip'        =>  empty($comment['ip']) ? Typecho_Request::getClientIp() : $comment['ip'],
            'agent'     =>  empty($comment['agent']) ? $_SERVER["HTTP_USER_AGENT"] : $comment['agent'],
            'text'      =>  empty($comment['text']) ? NULL : $comment['text'],
            'mode'      =>  empty($comment['mode']) ? 'comment' : $comment['mode'],
            'status'    =>  empty($comment['status']) ? 'approved' : $comment['status'],
            'parent'    =>  empty($comment['parent']) ? '0' : $comment['parent'],
        );
        
        /** 首先插入部分数据 */
        $insertId = $this->db->query($this->db->sql()->insert('table.comments')->rows($insertStruct));
        
        /** 更新评论数 */
        $num = $this->db->fetchObject($this->db->sql()->select('table.comments', 'COUNT(`coid`) AS `num`')
        ->where('`status` = ? AND `cid` = ?', 'approved', $cid))->num;
        
        $this->db->query($this->db->sql()->update('table.contents')->rows(array('commentsNum' => $num))
        ->where('`cid` = ?', $cid));
        
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
        $updateComment = $this->db->fetchObject($condition->select('table.comments', '`cid`')->limit(1));
        
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
        $num = $this->db->fetchObject($this->db->sql()->select('table.comments', 'COUNT(`coid`) AS `num`')
        ->where('`status` = ? AND `cid` = ?', 'approved', $cid))->num;
        
        $this->db->query($this->db->sql()->update('table.contents')->rows(array('commentsNum' => $num))
        ->where('`cid` = ?', $cid));
        
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
        $deleteComment = $this->db->fetchObject($condition->select('table.comments', '`cid`')->limit(1));
        
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
        $num = $this->db->fetchObject($this->db->sql()->select('table.comments', 'COUNT(`coid`) AS `num`')
        ->where('`status` = ? AND `cid` = ?', 'approved', $cid))->num;
        
        $this->db->query($this->db->sql()->update('table.contents')->rows(array('commentsNum' => $num))
        ->where('`cid` = ?', $cid));
        
        return $deleteRows;
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
        return $this->db->fetchObject($condition->select('table.comments', 'COUNT(table.comments.`coid`) AS `num`'))->num;
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
        $value = $this->abstractContentsWidget->filter($value);
        
        $value['permalink'] = $value['permalink'] . '#comments-' . $value['coid'];
        
        $value = $this->plugin->filter($value);
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
    public function date($format)
    {
        echo date($format, $this->created + $this->options->timezone);
    }
    
    /**
     * 输出词义化日期
     * 
     * @access public
     * @return void
     */
    public function dateWord()
    {
        echo Typecho_I18n::dateWord($this->created + $this->options->timezone, $this->options->gmtTime + $this->options->timezone);
    }
    
    /**
     * 输出文章摘要
     *
     * @access public
     * @param integer $length 摘要截取长度
     * @return void
     */
    public function excerpt($length = 100)
    {
        echo Typecho_API::subStr(Typecho_API::stripTags($this->text), 0, $length);
    }
}
