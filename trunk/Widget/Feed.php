<?php
/**
 * 聚合生成
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 聚合生成组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Feed extends Widget_Archive implements Typecho_Widget_Interface_Response
{
    /**
     * feed生成对象
     * 
     * @access private
     * @var FeedWriter
     */
    private $feed;
    
    /**
     * feed类型
     * 
     * @access private
     * @var string
     */
    private $type;
    
    /**
     * 是否输出评论
     * 
     * @access private
     * @var boolean
     */
    private $_isComments = false;
    
    /**
     * 重载构造函数,设置分页
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {        
        $feedQuery = Typecho_Router::getParameter('feed');
        $feedType = Typecho_Feed::RSS2;
        
        /** 过滤路径 */
        if(0 === strpos($feedQuery, '/rss/') || '/rss' == $feedQuery)
        {
            /** 如果是RSS1标准 */
            $feedQuery = substr($feedQuery, 4);
            $feedType = Typecho_Feed::RSS1;
        }
        else if(0 === strpos($feedQuery, '/atom/') || '/atom' == $feedQuery)
        {
            /** 如果是ATOM标准 */
            $feedQuery = substr($feedQuery, 5);
            $feedType = Typecho_Feed::ATOM1;
        }

        $this->feed = Typecho_Feed::generator($feedType);
        $this->type = $feedType;
        
        /** 处理评论聚合 */
        if('/comments' == $feedQuery || '/comments/' == $feedQuery)
        {
            $this->_isComments = true;
        }
        
        /** 解析路径 */
        if(false === Typecho_Router::match(Typecho_Config::get('Router'), $feedQuery))
        {
            throw new Typecho_Widget_Exception(_t('聚合页不存在'), Typecho_Exception::NOTFOUND);
        }
        else
        {
            parent::__construct(10);
        }
        
        $this->feed->setTitle(($this->options->archiveTitle ? $this->options->archiveTitle . ' - ' : NULL) . $this->options->title);
        $this->feed->setSubTitle($this->options->description);

        if(Typecho_Feed::RSS2 == $feedType)
        {
            $this->feed->setChannelElement('language', _t('zh-cn'));
            $this->feed->setLink($this->options->feedUrl);
        }
        
        if(Typecho_Feed::RSS1 == $feedType)
        {
            /** 如果是RSS1标准 */
            $this->feed->setChannelAbout($this->options->feedRssUrl);
        }
        
        if(Typecho_Feed::ATOM1 == $feedType)
        {
            /** 如果是ATOM标准 */
            $this->feed->setLink($this->options->feedAtomUrl);
        }

        if(Typecho_Feed::RSS1 == $feedType || Typecho_Feed::RSS2 == $feedType)
        {
            $this->feed->setDescription($this->options->description);
        }

        if(Typecho_Feed::RSS2 == $feedType || Typecho_Feed::ATOM1 == $feedType)
        {
            $this->feed->setChannelElement(Typecho_Feed::RSS2 == $feedType ? 'pubDate' : 'updated',
            date(Typecho_Feed::dateFormat($feedType), 
            $this->options->gmtTime + $this->options->timezone));
        }
        
        /** 增加插件接口 */
        Typecho_Plugin::instance(__FILE__)->render($this->feed, $feedType);
    }
    
    /**
     * 增加评论节点
     * 
     * @access public
     * @param CommentsWidget $comments
     * @return void
     */
    public function pushCommentElement($comments)
    {
        if($comments->have())
        {
            while($comments->get())
            {
                $item = $this->feed->createNewItem();
                $item->setTitle($comments->author);
                $item->setLink($comments->permalink);
                $item->setDate($comments->created + idate('Z'));
                $item->setDescription($comments->text);

                if(Typecho_Feed::RSS2 == $this->type)
                {
                    $item->addElement('guid', $comments->permalink);
                    $item->addElement('content:encoded', Typecho_API::subStr(Typecho_API::stripTags($comments->text), 0, 100, '...'));
                    $item->addElement('author', $comments->author);
                    $item->addElement('dc:creator', $comments->author);
                }
                
                Typecho_Plugin::instance(__FILE__)->pushCommentElement($item, $comments, $this->type);
                $this->feed->addItem($item);
            }
        }
    }
    
    /**
     * 生成节点
     * 
     * @access public
     * @param array $value
     * @return array
     */
    public function push(array $value)
    {
        $value = parent::push($value);
    
        if($this->_isComments)
        {
            Typecho_API::widget('Widget_Comments_Recent', 20)->to($comments);
            $this->pushCommentElement($comments);
        
            $this->options->feedUrl = Typecho_API::pathToUrl('/comments/', $this->options->feedUrl);
            $this->options->feedRssUrl = Typecho_API::pathToUrl('/comments/', $this->options->feedRssUrl);
            $this->options->feedAtomUrl = Typecho_API::pathToUrl('/comments/', $this->options->feedAtomUrl);
        }
        else
        {
            $item = $this->feed->createNewItem();
            $item->setTitle($value['title']);
            $item->setLink($value['permalink']);
            $item->setDate($value['created'] + idate('Z'));
            $item->setDescription($value['text']);
            $item->setCategory($value['categories']);
            
            if(Typecho_Feed::RSS2 == $this->type)
            {
                $item->addElement('guid', $value['permalink']);
                $item->addElement('comments', $value['permalink'] . '#comments');
                $item->addElement('content:encoded', Typecho_API::subStr(Typecho_API::stripTags($value['text']), 0, 100, '...'));
                $item->addElement('author', $value['author']);
                $item->addElement('dc:creator', $value['author']);
                $item->addElement('wfw:commentRss', $value['feedUrl']);
            }
            
            Typecho_Plugin::instance(__FILE__)->push($item, $value, $this->type);
            $this->feed->addItem($item);
        }
    }
    
    /**
     * 单篇内容节点
     * 
     * @access public
     * @param array $value
     * @return array
     */
    public function singlePush(array $value)
    {
        Typecho_API::widget('ArchiveComments')->to($comments);
        $this->pushCommentElement($comments);
        
        return parent::singlePush($value);
    }
    
    /**
     * 入口函数
     * 
     * @access public
     * @return void
     */
    public function render()
    {
        $this->feed->generateFeed();
    }
}
