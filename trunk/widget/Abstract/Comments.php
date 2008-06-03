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
 * 评论基类
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class CommentsWidget extends TypechoWidget
{
    /**
     * 实例化的抽象Meta类
     * 
     * @access protected
     * @var MetasWidget
     */
    protected $abstractContentsWidget;

    /**
     * 分页数目
     *
     * @access protected
     * @var integer
     */
    protected $pageSize;

    /**
     * 当前页
     *
     * @access protected
     * @var integer
     */
    protected $currentPage;

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
     * 公用的选择器
     * 
     * @access protected
     * @var TypechoDbQuery
     */
    protected $selectSql;

    /**
     * 用于计算总数的sql对象
     * 
     * @access protected
     * @var TypechoDbQuery
     */
    protected $countSql;

    /**
     * 插件
     *
     * @access protected
     * @var TypechoPlugin
     */
    protected $plugin;
    
    /**
     * 关键词名称
     * 
     * @access protected
     * @var array
     */
    protected $wordList;

    /**
     * 构造函数,初始化数据库
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        /** 初始化数据库 */
        $this->db = TypechoDb::get();
        
        /** 初始化常用widget */
        $this->options = Typecho::widget('Options');
        $this->access = Typecho::widget('Access');
        $this->abstractContentsWidget = Typecho::widget('Abstract.Contents');
        
        /** 初始化插件 */
        $this->plugin = TypechoPlugin::instance(__FILE__);
        
        /** 初始化共用选择器 */
        $this->selectSql = $this->db->sql()->select('table.comments', 'table.contents.`cid`, table.contents.`title`, table.contents.`slug`, table.contents.`created`, table.contents.`type`,
        table.comments.`coid`, table.comments.`created` AS `date`, table.comments.`author`, table.comments.`mail`, table.comments.`url`, table.comments.`ip`,
        table.comments.`agent`, table.comments.`text`, table.comments.`mode`, table.comments.`status`, table.comments.`parent`')
        ->join('table.contents', 'table.comments.`cid` = table.contents.`cid`');
        
        /** 初始化分页变量 */
        $this->pageSize = 20;
        $this->currentPage = TypechoRequest::getParameter('page', 1);
    }
    
    public function insertComment($comment, $cid)
    {
        /** 构建插入结构 */
        $insertStruct = array(
            'created'   =>  Typecho::widget('Options')->gmtTime,
            'author'    =>  empty($comment['author']) ? NULL : $comment['author'],
            'mail'      =>  empty($comment['mail']) ? NULL : $comment['mail'],
            'url'       =>  empty($comment['url']) ? NULL : $comment['url'],
            'ip'        =>  empty($comment['ip']) ? TypechoRequest::getClientIp() : $comment['ip'],
            'agent'     =>  empty($comment['agent']) ? $_SERVER["HTTP_USER_AGENT"] : $comment['agent'],
            'text'      =>  empty($comment['text']) ? NULL : $comment['text'],
        );
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
        
        $this->plugin->filter(__METHOD__, $value);
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
     * 输出评论分页
     *
     * @access public
     * @param string $pageTemplate 分页模板
     * @return void
     */
    public function pageNav($pageTemplate = NULL)
    {        
        $num = $this->db->fetchObject($this->countSql->select('table.comments', 'COUNT(table.comments.`coid`) AS `num`'))->num;
        $nav = new TypechoWidgetNavigator($num,
                                          $this->currentPage,
                                          $this->pageSize, NULL != $pageTemplate ? $pageTemplate : 
                                          TypechoRoute::parse(TypechoRoute::$current . '_page', $this->_row, $this->options->index));

        $nav->makeBoxNavigator(_t('上一页'), _t('下一页'));
    }
    
    /**
     * 按照条件计算评论数量
     * 
     * @access public
     * @param string $status 状态
     * @param string $mode 模式
     * @param integer $author 作者
     * @return integer
     */
    public function count($status = NULL, $mode = NULL, $author = NULL)
    {
        $countSql = clone $this->selectSql;
        
        /** 增加状态判断 */
        if(!empty($status))
        {
            $countSql->where('table.comments.`status` = ?', $status);
        }
        
        /** 增加模式判断 */
        if(!empty($mode))
        {
            $countSql->where('table.comments.`mode` = ?', $mode);
        }
        
        /** 增加作者判断 */
        if(!empty($author))
        {
            $countSql->where('table.contents.`author` = ?', $author);
        }
        
        return $this->db->fetchObject($countSql->select('table.comments', 'COUNT(table.comments.`coid`) AS `num`'))->num;
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
        echo TypechoI18n::dateWord($this->created + $this->options->timezone, $this->options->gmtTime + $this->options->timezone);
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
        echo Typecho::subStr(Typecho::stripTags($this->text), 0, $length);
    }
    
    /**
     * 输出评论状态
     * 
     * @access public
     * @return void
     */
    public function status()
    {
        switch($this->status)
        {
            case 'approved':
                echo _t('展现');
                break;
            case 'spam':
                echo _t('垃圾');
                break;
            case 'waiting':
                echo _t('待审核');
                break;
            default:
                echo _t('不明');
                break;
        }
    }
    
    /**
     * 输出评论类型
     * 
     * @access public
     * @return void
     */
    public function mode()
    {
        switch($this->mode)
        {
            case 'pingback':
                echo _t('广播');
                break;
            case 'trackback':
                echo _t('引用');
                break;
            case 'comment':
                echo _t('评论');
                break;
            default:
                echo _t('不明');
                break;
        }
    }
    
    /**
     * 入口函数
     *
     * @access public
     * @return void
     */
    public function render()
    {
        /** Just Return */
        return;
    }
}
