<?php
/**
 * 聚合生成
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/** 载入聚合库支持 **/
require_once __TYPECHO_LIB_DIR__ . '/Feed.php';

/** 载入父类支持 **/
require_once 'Posts.php';

/**
 * 聚合生成组件
 * 
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class FeedWidget extends PostsWidget
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
        $item->setDate($this->created + $this->options->timezone);
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
        $value = parent::singlePush($value);
    
        $item = $this->feed->createNewItem();
        $item->setTitle($value['title']);
        $item->setLink($value['permalink']);
        $item->setDate($this->created + $this->options->timezone);
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
        
        $this->feed->addItem($item);
    }
    
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
            $feedType = TypechoFeed::ATOM;
        }
        
        $this->feed = TypechoFeed::generator($feedType);
        
        if(TypechoFeed::RSS1 == $feedType)
        {
            /** 如果是RSS1标准 */
            $this->feed->setChannelAbout($this->options->feedRssUrl);
        }
        else if(TypechoFeed::ATOM == $feedType)
        {
            /** 如果是ATOM标准 */
            $this->feed->setLink($this->options->feedAtomUrl);
        }
        else
        {
            $this->feed->setLink($this->options->feedUrl);
        }
        
        /** 解析路径 */
        if(false !== ($value = TypechoRoute::match(TypechoConfig::get('Route'), $feedQuery)))
        {
            parent::render(10);
            $this->type = $feedType;

            $this->feed->setTitle(($this->options->archiveTitle ? $this->options->archiveTitle . ' - ' : NULL) . $this->options->title);
            $this->feed->setSubTitle($this->options->description);

            if(TypechoFeed::RSS2 == $feedType)
            {
                $this->feed->setChannelElement('language', _t('zh-cn'));
            }

            if(TypechoFeed::RSS1 == $feedType || TypechoFeed::RSS2 == $feedType)
            {
                $this->feed->setDescription($this->options->description);
            }

            if(TypechoFeed::RSS2 == $feedType || TypechoFeed::ATOM == $feedType)
            {
                $this->feed->setChannelElement(TypechoFeed::RSS2 == $feedType ? 'pubDate' : 'updated',
                date(TypechoFeed::dateFormat($feedType), 
                $this->options->gmtTime + $this->options->timezone));
            }
            
            $this->feed->generateFeed();
            return;
        }
        
        throw new TypechoWidgetException(_t('聚合页不存在'), TypechoException::NOTFOUND);
    }
}
