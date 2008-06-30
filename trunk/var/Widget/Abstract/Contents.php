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
class Widget_Abstract_Contents extends Typecho_Widget_Abstract_Dataset
{
    /**
     * 实例化的抽象Meta类
     * 
     * @access protected
     * @var MetasWidget
     */
    protected $abstractMetasWidget;

    /**
     * 实例化的配置对象
     *
     * @access protected
     * @var TypechoWidget
     */
    protected $options;

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
        $this->abstractMetasWidget = Typecho_API::factory('Widget_Abstract_Metas');
        
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
        return $this->db->sql()->select('table.contents', 'table.contents.`cid`, table.contents.`title`, table.contents.`slug`, table.contents.`created`,
        table.contents.`modified`, table.contents.`type`, table.contents.`text`, table.contents.`commentsNum`, table.contents.`meta`, table.contents.`template`, table.contents.`author` AS `authorId`,
        table.contents.`password`, table.contents.`allowComment`, table.contents.`allowPing`, table.contents.`allowFeed`, table.users.`screenName` AS `author`')
        ->join('table.users', 'table.contents.`author` = table.users.`uid`', Typecho_Db::LEFT_JOIN);
    }
    
    /**
     * 插入内容
     * 
     * @access public
     * @param array $content 内容数组
     * @return integer
     */
    public function insert(array $content)
    {
        /** 构建插入结构 */
        $insertStruct = array(
            'title'         =>  empty($content['title']) ? NULL : $content['title'],
            'created'       =>  empty($content['created']) ? $this->options->gmtTime : $content['created'],
            'modified'      =>  $this->options->gmtTime,
            'text'          =>  empty($content['text']) ? NULL : $content['text'],
            'meta'          =>  is_numeric($content['meta']) ? '0' : $content['meta'],
            'author'        =>  Typecho_API::factory('Widget_Users_Current')->uid,
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
        $slug = Typecho_API::slugName(empty($content['slug']) ? NULL : $content['slug'], $insertId);
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
     * @param Typecho_Db_Query $condition 更新条件
     * @return integer
     */
    public function update(array $content, Typecho_Db_Query $condition)
    {
        /** 首先验证写入权限 */
        if(!$this->postIsWriteable(clone $condition))
        {
            return false;
        }
    
        /** 构建更新结构 */
        $preUpdateStruct = array(
            'title'         =>  empty($content['title']) ? NULL : $content['title'],
            'meta'          =>  is_numeric($content['meta']) ? '0' : $content['meta'],
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
        $updateCondition = clone $condition;
        $updateRows = $this->db->query($condition->update('table.contents')->rows($updateStruct));
        
        /** 更新缩略名 */
        $slug = Typecho_API::slugName(empty($content['slug']) ? NULL : $content['slug'], $cid);
        $this->db->query($updateCondition->update('table.contents')->rows(array('slug' => $slug)));

        return $updateRows;
    }
    
    /**
     * 删除内容
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function delete(Typecho_Db_Query $condition)
    {
        return $this->db->query($condition->delete('table.contents'));
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
        if(!Typecho_API::factory('Widget_Users_Current')->pass('editor', true))
        {
            if($userId != Typecho_API::factory('Widget_Users_Current')->uid)
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
     * @param Typecho_Db_Query $condition 更新条件
     * @return mixed
     */
    public function postIsWriteable(Typecho_Db_Query $condition)
    {
        $post = $this->db->fetchRow($condition->select('table.contents', '`author`')->limit(1));

        if($post && $this->haveContentPermission($post['author']))
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * 按照条件计算内容数量
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public function size(Typecho_Db_Query $condition)
    {
        return $this->db->fetchObject($condition->select('table.contents', 'COUNT(table.contents.`cid`) AS `num`'))->num;
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
        $value['category'] = current(Typecho_API::arrayFlatten($value['categories'], 'slug'));

        /** 生成日期 */
        $value['year'] = date('Y', $value['created'] + $this->options->timezone);
        $value['month'] = date('m', $value['created'] + $this->options->timezone);
        $value['day'] = date('d', $value['created'] + $this->options->timezone);

        /** 获取路由类型并判断此类型在路由表中是否存在 */
        $type = $value['type'];
        $routeExists = isset(Typecho_Config::get('Router')->{$type});
        
        $tmpSlug = $value['slug'];
        $value['slug'] = urlencode($value['slug']);
        
        /** 生成静态路径 */
        $linkPath = $routeExists ? Typecho_Router::url($type, $value) : '#';
        
        /** 生成反馈地址 */
        /** 评论 */
        $value['commentUrl'] = Typecho_Router::url('feedback', 
        array('type' => 'comment', 'permalink' => $linkPath), $this->options->index);
        
        /** trackback */
        $value['trackbackUrl'] = Typecho_Router::url('feedback', 
        array('type' => 'trackback', 'permalink' => $linkPath), $this->options->index);
        
        /** 生成静态链接 */
        $value['permalink'] = Typecho_API::pathToUrl($linkPath, $this->options->index);
        
        /** 生成聚合链接 */
        /** RSS 2.0 */
        $value['feedUrl'] = $routeExists ? Typecho_Router::url($type, $value, $this->options->feedUrl) : '#';
        
        /** RSS 1.0 */
        $value['feedRssUrl'] = $routeExists ? Typecho_Router::url($type, $value, $this->options->feedRssUrl) : '#';
        
        /** ATOM 1.0 */
        $value['feedAtomUrl'] = $routeExists ? Typecho_Router::url($type, $value, $this->options->feedAtomUrl) : '#';
        
        $value['slug'] = $tmpSlug;
        
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
        echo date(empty($format) ? $this->options->postDateFormat : $format, $this->created + $this->options->timezone);
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
     * 输出文章内容
     *
     * @access public
     * @param string $more 文章截取后缀
     * @return void
     */
    public function content($more = NULL)
    {
        $content = str_replace('<p><!--more--></p>', '<!--more-->', $this->text);
        $contents = explode('<!--more-->', $content);
        
        list($abstract) = $contents;
        echo empty($more) ? $content : (Typecho_API::fixHtml($abstract) . (count($contents) > 1 ? '<p class="more"><a href="'
        . $this->permalink . '">' . $more . '</a></p>' : NULL));
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
        echo Typecho_API::subStr(Typecho_API::stripTags($this->text), 0, $length, $trim);
    }

    /**
     * 输出文章评论数
     *
     * @access public
     * @param string $string 评论数格式化数据
     * @return void
     */
    public function commentsNum($string = 'Comments %d')
    {
        $args = func_get_args();
        $num = intval($this->commentsNum);
        
        echo '<a href="' , $this->permalink , '#comments">' . 
        sprintf(isset($args[$num]) ? $args[$num] : array_pop($args), $num) , '</a>';
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
     * @param string $default 如果没有则输出
     * @return void
     */
    public function category($split = ',', $link = true, $default = NULL)
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
            echo $default;
        }
    }

    /**
     * 输出文章标签
     *
     * @access public
     * @param string $split 多个标签之间分隔符
     * @param boolean $link 是否输出链接
     * @param string $default 如果没有则输出
     * @return void
     */
    public function tags($split = ',', $link = true, $default = NULL)
    {
        $tags = isset($this->tags) ? $this->tags : $this->db->fetchAll($this->db->sql()
        ->select('table.metas')->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $this->cid)
        ->where('table.metas.`type` = ?', 'tag')
        ->group('table.metas.`mid`'), array($this->abstractMetasWidget, 'filter'));

        if($tags)
        {
            $result = array();
            foreach($tags as $tag)
            {
                $result[] = $link ? '<a href="' . $tag['permalink'] . '">'
                . $tag['name'] . '</a>' : $tag['name'];
            }

            echo implode($split, $result);
        }
        else
        {
            echo $default;
        }
    }
}
