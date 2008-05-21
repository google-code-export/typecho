<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Posts.php 200 2008-05-21 06:33:20Z magike.net $
 */

require_once 'abstract/Contents.php';

/**
 * 内容的文章基类
 * 定义的css类
 * p.more:阅读全文链接所属段落
 *
 * @package Widget
 */
class PostsWidget extends TypechoWidget
{
    /**
     * 入口函数
     *
     * @access public
     * @param integer $pageSize 每页文章数
     * @return void
     */
    public function render($pageSize = NULL)
    {
        $this->_pageSize = empty($pageSize) ? $this->options->pageSize : $pageSize;
        $this->_currentPage = TypechoRoute::getParameter('page') ? 1 : TypechoRoute::getParameter('page');
        
        $select = $this->selectSql->where('table.contents.`type` = ?', 'post')
        ->where('table.contents.`password` IS NULL')
        ->where('table.contents.`created` < ?', $this->options->gmtTime);

        if('tag' == TypechoRoute::$current)
        {
            $tag = $this->db->fetchRow($this->db->sql()->select('table.metas')
            ->where('`type` = ?', 'tag')
            ->where('`slug` = ?', urlencode(TypechoRoute::getParameter('slug')))->limit(1));
            
            if(!$tag)
            {
                throw new TypechoWidgetException(_t('标签%s不存在', TypechoRoute::getParameter('slug')), TypechoException::NOTFOUND);
            }
        
            $select->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
            ->where('table.relationships.`mid` = ?', $tag['mid']);
            
            /** 设置关键词 */
            Typecho::header('meta', array('name' => 'keywords', 'content' => $tag['name']));
            
            /** 设置头部feed */
            /** RSS 2.0 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => 'RSS 2.0',
            'href' => TypechoRoute::parse('feed', array('feed' => TypechoRoute::parse('tag', $tag)), $this->options->index)));
            
            /** RSS 0.92 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'text/xml', 'title' => 'RSS 0.92',
            'href' => TypechoRoute::parse('feed', array('feed' => '/rss' .
            TypechoRoute::parse('tag', $tag)), $this->options->index)));
            
            /** Atom 0.3 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/atom+xml', 'title' => 'Atom 0.3',
            'href' => TypechoRoute::parse('feed', array('feed' => '/atom' .
            TypechoRoute::parse('tag', $tag)), $this->options->index)));
            
            /** 设置标题 */
            $this->options->title = _t('%s &raquo; 标签 &raquo; %s', $tag['name'], $this->options->title);
        }
        else if('category' == TypechoRoute::$current)
        {
            $category = $this->db->fetchRow($this->db->sql()->select('table.metas')
            ->where('`type` = ?', 'category')
            ->where('`slug` = ?', TypechoRoute::getParameter('slug'))->limit(1));
            
            if(!$category)
            {
                throw new TypechoWidgetException(_t('分类不存在'), TypechoException::NOTFOUND);
            }
        
            $select->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
            ->where('table.relationships.`mid` = ?', $category['mid']);
            
            /** 设置关键词 */
            Typecho::header('meta', array('name' => 'keywords', 'content' => $category['name']));
            
            /** 设置头部feed */
            /** RSS 2.0 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => 'RSS 2.0',
            'href' => TypechoRoute::parse('feed', array('feed' => TypechoRoute::parse('category', $category)), $this->options->index)));
            
            /** RSS 0.92 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'text/xml', 'title' => 'RSS 0.92',
            'href' => TypechoRoute::parse('feed', array('feed' => '/rss' .
            TypechoRoute::parse('category', $category)), $this->options->index)));
            
            /** Atom 0.3 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/atom+xml', 'title' => 'Atom 0.3',
            'href' => TypechoRoute::parse('feed', array('feed' => '/atom' .
            TypechoRoute::parse('category', $category)), $this->options->index)));
            
            /** 设置标题 */
            $this->options->title = _t('%s &raquo; 分类 &raquo; %s', $category['name'], $this->options->title);
        }
        else
        {
            /** 设置关键词 */
            Typecho::header('meta', array('name' => 'keywords', 'content' => $this->options->keywords));
        
            /** 设置头部feed */
            /** RSS 2.0 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => 'RSS 2.0',
            'href' => $this->options->feedUrl));
            
            /** RSS 0.92 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'text/xml', 'title' => 'RSS 0.92',
            'href' => TypechoRoute::parse('feed', array('feed' => '/rss'), $this->options->index)));
            
            /** Atom 0.3 */
            Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/atom+xml', 'title' => 'Atom 0.3',
            'href' => TypechoRoute::parse('feed', array('feed' => '/atom'), $this->options->index)));
        }
        
        $this->countSql = clone $select;

        $select->group('table.contents.`cid`')
        ->order('table.contents.`created`', TypechoDb::SORT_DESC)
        ->page($this->_currentPage, $this->_pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
