<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Posts.php 200 2008-05-21 06:33:20Z magike.net $
 */

/** 载入文章基类支持 **/
require_once 'Abstract/Contents.php';

/**
 * 内容的文章基类
 * 定义的css类
 * p.more:阅读全文链接所属段落
 *
 * @package Widget
 */
class ArchiveWidget extends ContentsWidget
{
    /**
     * 将每行的值压入堆栈
     *
     * @access public
     * @param array $value 每行的值
     * @return array
     */
    public function singlePush($value)
    {
        $value['tags'] = $this->db->fetchAll($this->db->sql()
        ->select('table.metas')->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $value['cid'])
        ->where('table.metas.`type` = ?', 'tag')
        ->group('table.metas.`mid`'), array($this->abstractMetasWidget, 'filter'));
        
        return parent::push($value);
    }

    /**
     * 入口函数
     *
     * @access public
     * @param integer $pageSize 每页文章数
     * @return void
     */
    public function render($pageSize = NULL)
    {
        /** 初始化分页变量 */
        $this->pageSize = empty($pageSize) ? $this->options->pageSize : $pageSize;
        $this->currentPage = TypechoRoute::getParameter('page', 1);
        $hasPushed = false;
    
        $select = $this->selectSql->where('table.contents.`password` IS NULL')
        ->where('table.contents.`created` < ?', $this->options->gmtTime);

        if('post' == TypechoRoute::$current || 'page' == TypechoRoute::$current)
        {
            /** 如果是单篇文章或独立页面 */
            if(NULL !== TypechoRoute::getParameter('cid'))
            {
                $select->where('table.contents.`cid` = ?', TypechoRoute::getParameter('cid'));
            }
        
            if(NULL !== TypechoRoute::getParameter('slug'))
            {
                $select->where('table.contents.`slug` = ?', TypechoRoute::getParameter('slug'));
            }
            
            $select->where('table.contents.`type` = ?', TypechoRoute::$current)
            ->group('table.contents.`cid`')->limit(1);
            $post = $this->db->fetchRow($select, array($this, 'singlePush'));

            if($post && $post['category'] == TypechoRoute::getParameter('category', $post['category'])
            && $post['year'] == TypechoRoute::getParameter('year', $post['year'])
            && $post['month'] == TypechoRoute::getParameter('month', $post['month'])
            && $post['day'] == TypechoRoute::getParameter('day', $post['day']))
            {
                /** 设置关键词 */
                $this->options->keywords = implode(',', Typecho::arrayFlatten($post['tags'], 'name'));
                
                /** 设置头部feed */
                /** RSS 2.0 */
                $this->options->feedUrl = $post['feedUrl'];
                
                /** RSS 0.92 */
                $this->options->feedRssUrl = $post['feedRssUrl'];
                
                /** ATOM 1.0 */
                $this->options->feedAtomUrl = $post['feedAtomUrl'];
                
                /** 设置标题 */
                $this->options->archiveTitle = $post['title'];
            }
            else
            {
                throw new TypechoWidgetException('post' == TypechoRoute::$current ? _t('文章不存在') : _t('页面不存在'), TypechoException::NOTFOUND);
            }
            
            $hasPushed = true;
        }
        else if('category' == TypechoRoute::$current || 'category_page' == TypechoRoute::$current)
        {
            /** 如果是分类 */
            $category = $this->db->fetchRow($this->db->sql()->select('table.metas')
            ->where('`type` = ?', 'category')
            ->where('`slug` = ?', TypechoRoute::getParameter('slug'))->limit(1),
            array($this->abstractMetasWidget, 'filter'));
            
            if(!$category)
            {
                throw new TypechoWidgetException(_t('分类不存在'), TypechoException::NOTFOUND);
            }
        
            $select->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
            ->where('table.relationships.`mid` = ?', $category['mid']);
            
            /** 设置关键词 */
            $this->options->keywords = $category['name'];
            
            /** 设置头部feed */
            /** RSS 2.0 */
            $this->options->feedUrl = $category['feedUrl'];
            
            /** RSS 0.92 */
            $this->options->feedRssUrl = $category['feedRssUrl'];
            
            /** ATOM 1.0 */
            $this->options->feedAtomUrl = $category['feedAtomUrl'];
            
            /** 设置标题 */
            $this->options->archiveTitle = $category['name'];
        }
        else if('tag' == TypechoRoute::$current || 'tag_page' == TypechoRoute::$current)
        {
            /** 如果是标签 */
            $tag = $this->db->fetchRow($this->db->sql()->select('table.metas')
            ->where('`type` = ?', 'tag')
            ->where('`slug` = ?', TypechoRoute::getParameter('slug'))->limit(1),
            array($this->abstractMetasWidget, 'filter'));
            
            if(!$tag)
            {
                throw new TypechoWidgetException(_t('标签%s不存在', TypechoRoute::getParameter('slug')), TypechoException::NOTFOUND);
            }
        
            $select->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
            ->where('table.relationships.`mid` = ?', $tag['mid']);
            
            /** 设置关键词 */
            $this->options->keywords = $tag['name'];
            
            /** 设置头部feed */
            /** RSS 2.0 */
            $this->options->feedUrl = $tag['feedUrl'];
            
            /** RSS 0.92 */
            $this->options->feedRssUrl = $tag['feedRssUrl'];
            
            /** ATOM 1.0 */
            $this->options->feedAtomUrl = $tag['feedAtomUrl'];
            
            /** 设置标题 */
            $this->options->archiveTitle = $tag['name'];
        }
        else if('archive_year' == TypechoRoute::$current || 'archive_month' == TypechoRoute::$current
        || 'archive_day' == TypechoRoute::$current)
        {
            /** 如果是按日期归档 */
            $year = TypechoRoute::getParameter('year');
            $month = TypechoRoute::getParameter('month');
            $day = TypechoRoute::getParameter('day');
            
            /** 如果按日归档 */
            if(!empty($year) && !empty($month) && !empty($day))
            {
                $from = mktime(0, 0, 0, $month, $day, $year) - $this->options->timezone;
                $to = mktime(23, 59, 59, $month, $day, $year) - $this->options->timezone;
                
                /** 设置标题 */
                $this->options->archiveTitle = _t('%s年%s月%s日', $year, $month, $day);
            }
            /** 如果按月归档 */
            else if(!empty($year) && !empty($month))
            {
                $from = mktime(0, 0, 0, $month, 1, $year) - $this->options->timezone;
                $to = mktime(23, 59, 59, $month, idate('t', $from), $year) - $this->options->timezone;
                
                /** 设置标题 */
                $this->options->archiveTitle = _t('%s年%s月', $year, $month);
            }
            /** 如果按年归档 */
            else if(!empty($year))
            {
                $from = mktime(0, 0, 0, 1, 1, $year) - $this->options->timezone;
                $to = mktime(23, 59, 59, 12, 31, $year) - $this->options->timezone;
                
                /** 设置标题 */
                $this->options->archiveTitle = _t('%s年', $year);
            }
            
            $select->where('table.contents.`created` >= ?', $from)
            ->where('table.contents.`created` <= ?', $to);
            
            /** 设置头部feed */
            $value = array('year' => $year, 'month' => $month, 'day' => $day);
            
            /** RSS 2.0 */
            $this->options->feedUrl = TypechoRoute::parse(TypechoRoute::$current, $value, $this->options->feedUrl);
            
            /** RSS 0.92 */
            $this->options->feedRssUrl = TypechoRoute::parse(TypechoRoute::$current, $value, $this->options->feedRssUrl);
            
            /** ATOM 1.0 */
            $this->options->feedAtomUrl = TypechoRoute::parse(TypechoRoute::$current, $value, $this->options->feedAtomUrl);
        }

        /** 设置关键词 */
        Typecho::header('meta', array('name' => 'keywords', 'content' => $this->options->keywords));
    
        /** 设置头部feed */
        /** RSS 2.0 */
        Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => 'RSS 2.0', 'href' => $this->options->feedUrl));
        
        /** RSS 0.92 */
        Typecho::header('link', array('rel' => 'alternate', 'type' => 'text/xml', 'title' => 'RSS 0.92', 'href' => $this->options->feedRssUrl));
        
        /** ATOM 1.0 */
        Typecho::header('link', array('rel' => 'alternate', 'type' => 'application/atom+xml', 'title' => 'ATOM 1.0', 'href' => $this->options->feedAtomUrl));
        
        /** 如果已经提前压入则直接返回 */
        if($hasPushed)
        {
            return;
        }
        
        $this->countSql = clone $select;

        $select->where('table.contents.`type` = ?', 'post')
        ->group('table.contents.`cid`')
        ->order('table.contents.`created`', TypechoDb::SORT_DESC)
        ->page($this->currentPage, $this->pageSize);
        
        $this->db->fetchAll($select, array($this, 'push'));
    }
}
