<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 内容基类
 *
 * @package Widget
 */
class ContentsWidget extends TypechoWidget
{
    /**
     * 实例化的抽象Meta类
     * 
     * @access protected
     * @var MetasWidget
     */
    protected $abstractMetasWidget;

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
        $this->abstractMetasWidget = Typecho::widget('Abstract.Metas');
        
        /** 初始化插件 */
        $this->plugin = TypechoPlugin::instance(__FILE__);
        
        /** 初始化共用选择器 */
        $this->selectSql = $this->db->sql()->select('table.contents', 'table.contents.`cid`, table.contents.`title`, table.contents.`slug`, table.contents.`created`,
        table.contents.`modified`, table.contents.`type`, table.contents.`text`, table.contents.`commentsNum`, table.contents.`meta`, table.contents.`template`, table.contents.`author` AS `authorId`,
        table.contents.`password`, table.contents.`allowComment`, table.contents.`allowPing`, table.contents.`allowFeed`, table.users.`screenName` AS `author`')
        ->join('table.users', 'table.contents.`author` = table.users.`uid`', TypechoDb::LEFT_JOIN);
        
        /** 初始化分页变量 */
        $this->pageSize = 20;
        $this->currentPage = TypechoRoute::getParameter('page', 1);
    }
    
    /**
     * 插入内容
     * 
     * @access public
     * @param array $content 内容数组
     * @return integer
     */
    public function insertContent(array $content)
    {
        /** 构建插入结构 */
        $insertStruct = array(
            'title'         =>  empty($content['title']) ? NULL : $content['title'],
            'created'       =>  empty($content['created']) ? $this->options->gmtTime : $content['created'],
            'modified'      =>  $this->options->gmtTime,
            'text'          =>  empty($content['text']) ? NULL : $content['text'],
            'meta'          =>  empty($content['meta']) ? '0' : $content['meta'],
            'author'        =>  $this->access->uid,
            'template'      =>  empty($content['template']) ? NULL : $content['template'],
            'type'          =>  empty($content['type']) ? 'post' : $content['type'],
            'password'      =>  empty($content['password']) ? NULL : $content['password'],
            'commentsNum'   =>  0,
            'allowComment'  =>  !empty($content['allowComment']) && 1 == $content['allowComment'] ? 'enable' : 'disable',
            'allowPing'     =>  !empty($content['allowPing']) && 1 == $content['allowPing'] ? 'enable' : 'disable',
            'allowFeed'     =>  !empty($content['allowFeed']) && 1 == $content['allowFeed'] ? 'enable' : 'disable',
        );
        
        /** 首先插入部分数据 */
        $insertId = $this->db->query($this->db->sql()->insert('table.contents')->rows($insertStruct));
        
        /** 更新缩略名 */
        $slug = Typecho::slugName(empty($content['slug']) ? NULL : $content['slug'], $insertId);
        $this->db->query($this->db->sql()->update('table.contents')
        ->rows(array('slug' => $slug))
        ->where('`cid` = ?', $insertId));

        return $insertId;
    }
    
    /**
     * 更新内容
     * 
     * @access public
     * @param array $content 内容数组
     * @param integer $cid 内容主键
     * @return boolean
     */
    public function updateContent(array $content, $cid)
    {
        /** 首先验证写入权限 */
        if(!$this->postIsWriteable($cid))
        {
            return false;
        }
    
        /** 构建更新结构 */
        $preUpdateStruct = array(
            'title'         =>  empty($content['title']) ? NULL : $content['title'],
            'meta'          =>  empty($content['meta']) ? '0' : $content['meta'],
            'text'          =>  empty($content['text']) ? NULL : $content['text'],
            'template'      =>  empty($content['template']) ? NULL : $content['template'],
            'type'          =>  empty($content['type']) ? 'post' : $content['type'],
            'password'      =>  empty($content['password']) ? NULL : $content['password'],
            'allowComment'  =>  !empty($content['allowComment']) && 1 == $content['allowComment'] ? 'enable' : 'disable',
            'allowPing'     =>  !empty($content['allowPing']) && 1 == $content['allowPing'] ? 'enable' : 'disable',
            'allowFeed'     =>  !empty($content['allowFeed']) && 1 == $content['allowFeed'] ? 'enable' : 'disable',
        );
        
        $updateStruct = array();
        foreach($content as $key => $val)
        {
            if(isset($preUpdateStruct[$key]))
            {
                $updateStruct[$key] = $preUpdateStruct[$key];
            }
        }
        
        /** 更新创建时间 */
        if(!empty($content['created']))
        {
            $updateStruct['created'] = $content['created'];
        }
        
        $updateStruct['modified'] = $this->options->gmtTime;
        
        /** 首先插入部分数据 */
        $updateRows = $this->db->query($this->db->sql()->update('table.contents')->rows($updateStruct)
        ->where('`cid` = ?', $cid));
        
        /** 如果数据不存在 */
        if($updateRows < 1)
        {
            return false;
        }
        
        /** 更新缩略名 */
        $slug = Typecho::slugName(empty($content['slug']) ? NULL : $content['slug'], $cid);
        $this->db->query($this->db->sql()->update('table.contents')
        ->rows(array('slug' => $slug))
        ->where('`cid` = ?', $cid));

        return true;
    }
    
    /**
     * 删除内容
     * 
     * @access public
     * @param integer $cid 内容主键
     * @return boolean
     */
    public function deleteContent($cid)
    {
        /** 首先验证写入权限 */
        if(!$this->postIsWriteable($cid))
        {
            return false;
        }

        $deleteRows = $this->db->query($this->db->sql()->delete('table.contents')
        ->where('`cid` = ?', $cid));
        
        return true;
    }
    
    /**
     * 对内容按照meta字段排序
     * 
     * @access public
     * @param array $contents
     * @param string $type
     * @return void
     */
    public function sortContent(array $contents, $type)
    {
        foreach($contents as $cid => $sort)
        {
            if($this->postIsWriteable($cid))
            {
                $this->db->query($this->db->sql()->update('table.contents')->row('meta', $sort)
                ->where('`cid` = ?', $cid)->where('`type` = ?', $type));
            }
        }
    }

    /**
     * 检测当前用户是否具备修改权限
     * 
     * @access public
     * @param integer $userId 文章的作者id
     * @return boolean
     */
    public function haveContentPermission($userId)
    {
        if(!Typecho::widget('Access')->pass('editor', true))
        {
            if($userId != Typecho::widget('Access')->uid)
            {
                return false;
            }
        }

        return true;
    }
    
    /**
     * 内容是否可以被修改
     * 
     * @access public
     * @param integer $cid 内容主键
     * @return mixed
     */
    public function postIsWriteable($cid)
    {
        $post = $this->db->fetchRow($this->db->sql()->select('table.contents', '`author`')
        ->where('`cid` = ?', $cid)->limit(1));
        
        if($post && $this->haveContentPermission($post['author']))
        {
            return true;
        }
        
        return false;
    }

    /**
     * 输出内容分页
     *
     * @access public
     * @param string $pageTemplate 分页模板
     * @return void
     */
    public function pageNav($pageTemplate = NULL)
    {        
        $num = $this->db->fetchObject($this->countSql->select('table.contents', 'COUNT(table.contents.`cid`) AS `num`'))->num;
        $nav = new TypechoWidgetNavigator($num,
                                          $this->currentPage,
                                          $this->pageSize, NULL != $pageTemplate ? $pageTemplate : 
                                          TypechoRoute::parse(TypechoRoute::$current . '_page', $this->_row, $this->options->index));

        $nav->makeBoxNavigator(_t('上一页'), _t('下一页'));
    }
    
    /**
     * 按照条件计算内容数量
     * 
     * @access public
     * @param string $type 类型
     * @param integer $author 作者
     * @return integer
     */
    public function count($type = NULL, $author = NULL)
    {
        $countSql = clone $this->selectSql;
        
        /** 增加类型判断 */
        if(!empty($type))
        {
            if(is_array($type))
            {
                $args[] = implode(' OR ', array_fill(0, count($type), 'table.contents.`type` = ?'));

                foreach($type as $val)
                {
                    $args[] = $val;
                }
                
                call_user_func_array(array($countSql, 'where'), $args);
            }
            else
            {
                $countSql->where('table.contents.`type` = ?', $type);
            }
        }
        
        /** 增加作者判断 */
        if(!empty($author))
        {
            $countSql->where('table.contents.`author` = ?', $author);
        }
        
        return $this->db->fetchObject($countSql->select('table.contents', 'COUNT(table.contents.`cid`) AS `num`'))->num;
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
        $value['categories'] = $this->db->fetchAll($this->db->sql()
        ->select('table.metas')->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $value['cid'])
        ->where('table.metas.`type` = ?', 'category')
        ->group('table.metas.`mid`')
        ->order('`sort`', 'ASC'), array($this->abstractMetasWidget, 'filter'));
        
        /** 取出第一个分类作为slug条件 */
        $value['category'] = current(Typecho::arrayFlatten($value['categories'], 'slug'));

        /** 生成日期 */
        $value['year'] = date('Y', $value['created'] + $this->options->timezone);
        $value['month'] = date('n', $value['created'] + $this->options->timezone);
        $value['day'] = date('j', $value['created'] + $this->options->timezone);

        /** 获取路由类型并判断此类型在路由表中是否存在 */
        $type = $value['type'];
        $routeExists = isset(TypechoConfig::get('Route')->$type);
        
        /** 生成静态链接 */
        $value['permalink'] = $routeExists ? TypechoRoute::parse($type, $value, $this->options->index) : '#';
        
        /** 生成聚合链接 */
        /** RSS 2.0 */
        $value['feedUrl'] = $routeExists ? TypechoRoute::parse($type, $value, $this->options->feedUrl) : '#';
        
        /** RSS 1.0 */
        $value['feedRssUrl'] = $routeExists ? TypechoRoute::parse($type, $value, $this->options->feedRssUrl) : '#';
        
        /** ATOM 1.0 */
        $value['feedAtomUrl'] = $routeExists ? TypechoRoute::parse($type, $value, $this->options->feedAtomUrl) : '#';
        
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
     * 输出文章评论提交地址
     *
     * @access public
     * @return void
     */
    public function commentsPostUrl()
    {
        printf(TypechoRoute::parse('do', array('do' => 'CommentsPost'), $this->options->index) . '?%d.%d',
        $this->cid, $this->created);
    }

    /**
     * 输出文章引用通告地址
     *
     * @access public
     * @return void
     */
    public function trackbackUrl()
    {
        printf(TypechoRoute::parse('do', array('do' => 'Trackback'), $this->options->index) . '?%d.%d',
        $this->cid, $this->created);
    }

    /**
     * 输出文章内容
     *
     * @access public
     * @param string $more 文章截取后缀
     * @return void
     */
    public function content($more = NULL)
    {
        $content = str_replace('<p><!--more--></p>', '<!--more-->', $this->text);
        list($abstract) = explode('<!--more-->', $content);
        echo Typecho::fixHtml($abstract) . ($more ? '<p class="more"><a href="'
        . $this->permalink . '">' . $more . '</a></p>' : NULL);
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
     * 输出文章评论数
     *
     * @access public
     * @param string $string 评论数格式化数据
     * @param string $tag 评论链接锚点
     * @return void
     */
    public function commentsNum($string = 'Comments %d')
    {
        $args = func_get_args();
        $num = intval($this->commentsNum);
        
        echo '<a href="' . $this->permalink . '#comments">' . 
        sprintf(isset($args[$num]) ? $args[$num] : array_pop($args), $num) . '</a>';
    }

    /**
     * 获取文章权限
     *
     * @access public
     * @param string $permission 权限
     * @return unknown
     */
    public function allow()
    {
        $permissions = func_get_args();
        $allow = true;

        foreach($permissions as $permission)
        {
            $allow &= ($this->_row['allow' . ucfirst($permission)] == 'enable');
        }

        return $allow;
    }

    /**
     * 输出文章分类
     *
     * @access public
     * @param string $split 多个分类之间分隔符
     * @param boolean $link 是否输出链接
     * @return void
     */
    public function category($split = ',', $link = true)
    {
        $categories = $this->categories;
        if($categories)
        {
            $result = array();
            
            foreach($categories as $category)
            {
                $result[] = $link ? '<a href="' . $category['permalink'] . '">'
                . $category['name'] . '</a>' : $category['name'];
            }

            echo implode($split, $result);
        }
        else
        {
            echo _t('没有归类');
        }
    }

    /**
     * 输出文章标签
     *
     * @access public
     * @param string $split 多个标签之间分隔符
     * @param boolean $link 是否输出链接
     * @return void
     */
    public function tags($split = ',', $link = true)
    {
        $tags = isset($this->tags) ? $this->tags : $this->db->fetchAll($this->db->sql()
        ->select('table.metas')->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $this->cid)
        ->where('table.metas.`type` = ?', 'tag')
        ->group('table.metas.`mid`'), array($this->abstractMetasWidget, 'filter'));

        $result = array();
        foreach($tags as $tag)
        {
            $result[] = $link ? '<a href="' . $tag['permalink'] . '">'
            . $tag['name'] . '</a>' : $tag['name'];
        }

        echo implode($split, $result);
    }

    /**
     * 入口函数
     *
     * @access public
     * @return void
     */
    public function render()
    {
        /******************************************
        $this->countSql = clone $this->selectSql;

        $this->selectSql->group('table.contents.`cid`')
        ->order('table.contents.`created`', TypechoDb::SORT_DESC)
        ->page($this->currentPage, $this->pageSize);

        $this->db->fetchAll($select, array($this, 'push'));
        ********************************************/
        
        /** Just Return */
        return;
    }
}
