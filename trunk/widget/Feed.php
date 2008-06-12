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

/** 载入父类支持 **/
require_once 'Archive.php';

/**
 * 聚合生成组件
 * 
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class FeedWidget extends ArchiveWidget
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

                if(TypechoFeed::RSS2 == $this->type)
                {
                    $item->addElement('guid', $comments->permalink);
                    $item->addElement('content:encoded', Typecho::subStr(Typecho::stripTags($comments->text), 0, 100, '...'));
                    $item->addElement('author', $comments->author);
                    $item->addElement('dc:creator', $comments->author);
                }
                
                TypechoPlugin::instance(__FILE__)->pushCommentElement($item, $comments, $this->type);
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
    
        $item = $this->feed->createNewItem();
        $item->setTitle($value['title']);
        $item->setLink($value['permalink']);
        $item->setDate($value['created'] + idate('Z'));
        $item->setDescription($value['text']);
        $item->setCategory($value['categories']);
        
        if(TypechoFeed::RSS2 == $this->type)
        {
            $item->addElement('guid', $value['permalink']);
            $item->addElement('comments', $value['permalink'] . '#comments');
            $item->addElement('content:encoded', Typecho::subStr(Typecho::stripTags($value['text']), 0, 100, '...'));
            $item->addElement('author', $value['author']);
            $item->addElement('dc:creator', $value['author']);
            $item->addElement('wfw:commentRss', $value['feedUrl']);
        }
        
        TypechoPlugin::instance(__FILE__)->push($item, $value, $this->type);
        $this->feed->addItem($item);
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
        Typecho::widget('ArchiveComments')->to($comments);
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
        $feedQuery = TypechoRoute::getParameter('feed');
        $feedType = TypechoFeed::RSS2;
        
        /** 过滤路径 */
        if(0 === strpos($feedQuery, '/rss/') || '/rss' == $feedQuery)
        {
            /** 如果是RSS1标准 */
            $feedQuery = substr($feedQuery, 4);
            $feedType = TypechoFeed::RSS1;
        }
        else if(0 === strpos($feedQuery, '/atom/') || '/atom' == $feedQuery)
        {
            /** 如果是ATOM标准 */
            $feedQuery = substr($feedQuery, 5);
            $feedType = TypechoFeed::ATOM1;
        }

        $this->feed = TypechoFeed::generator($feedType);
        $this->type = $feedType;
        
        /** 处理评论聚合 */
        if('/comments' == $feedQuery || '/comments/' == $feedQuery)
        {
            Typecho::widget('RecentComments', 20)->to($comments);
            $this->pushCommentElement($comments);
        
            $this->options->feedUrl = Typecho::pathToUrl('/comments/', $this->options->feedUrl);
            $this->options->feedRssUrl = Typecho::pathToUrl('/comments/', $this->options->feedRssUrl);
            $this->options->feedAtomUrl = Typecho::pathToUrl('/comments/', $this->options->feedAtomUrl);
        }
        /** 解析路径 */
        else if(false !== TypechoRoute::match(TypechoConfig::get('Route'), $feedQuery))
        {
            parent::render(10, false);
        }
        else
        {
            throw new TypechoWidgetException(_t('聚合页不存在'), TypechoException::NOTFOUND);
        }
        
        $this->feed->setTitle(($this->options->archiveTitle ? $this->options->archiveTitle . ' - ' : NULL) . $this->options->title);
        $this->feed->setSubTitle($this->options->description);

        if(TypechoFeed::RSS2 == $feedType)
        {
            $this->feed->setChannelElement('language', _t('zh-cn'));
            $this->feed->setLink($this->options->feedUrl);
        }
        
        if(TypechoFeed::RSS1 == $feedType)
        {
            /** 如果是RSS1标准 */
            $this->feed->setChannelAbout($this->options->feedRssUrl);
        }
        
        if(TypechoFeed::ATOM1 == $feedType)
        {
            /** 如果是ATOM标准 */
            $this->feed->setLink($this->options->feedAtomUrl);
        }

        if(TypechoFeed::RSS1 == $feedType || TypechoFeed::RSS2 == $feedType)
        {
            $this->feed->setDescription($this->options->description);
        }

        if(TypechoFeed::RSS2 == $feedType || TypechoFeed::ATOM1 == $feedType)
        {
            $this->feed->setChannelElement(TypechoFeed::RSS2 == $feedType ? 'pubDate' : 'updated',
            date(TypechoFeed::dateFormat($feedType), 
            $this->options->gmtTime + $this->options->timezone));
        }
        
        /** 增加插件接口 */
        TypechoPlugin::instance(__FILE__)->render($this->feed, $feedType);
        $this->feed->generateFeed();
    }
}
