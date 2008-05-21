<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: FeedPosts.php 160 2008-05-06 15:22:19Z magike.net $
 */

/** 载入聚合库支持 **/
require_once __TYPECHO_LIB_DIR__ . '/Feed.php';

/** 载入Contents抽象类支持 */
require_once __TYPECHO_WIDGET_DIR__ . '/Abstract/Contents.php';

/**
 * 输出文章聚合
 *
 * @package Widget
 */
class PostsFeedWidget extends ContentsWidget
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
        $item->setDate($this->options->gmtTime + $this->options->timezone);
        $item->setDescription($value['text']);
        
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
     * 重载父类入口函数
     *
     * @access public
     * @param string $feedType 聚合类型
     * @param array $parameters 参数列表
     * @param string $link 聚合链接
     * @return void
     */
    public function render($feedType, array $parameters, $link)
    {
        $this->feed = TypechoFeed::generator($feedType);
        $this->type = $feedType;
        
        $this->feed->setTitle($this->options->title);
        $this->feed->setLink($link);
        
        if(TypechoFeed::RSS1 == $feedType)
        {
            $this->feed->setChannelAbout($link);
        }
        
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
    
        $this->db->fetchAll($this->selectSql->where('table.contents.`type` = ?', 'post')
        ->where('table.contents.`allowFeed` = ?', 'enable')
        ->where('table.contents.`password` IS NULL')
        ->where('table.contents.`created` < ?', $this->options->gmtTime)
        ->group('table.contents.`cid`')
        ->order('table.contents.`created`', TypechoDb::SORT_DESC)
        ->limit(10), array($this, 'push'));
        
        $this->feed->generateFeed();
    }
}
