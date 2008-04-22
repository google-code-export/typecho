<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/** 载入提交基类支持 **/
require_once 'Post.php';

/**
 * 内容处理类
 *
 * @package Widget
 */
class ContentsPostWidget extends PostWidget
{
    /**
     * 设置内容标签
     * 
     * @access protected
     * @param integer $cid
     * @param string $tags
     * @param string $type
     * @return string
     */
    protected function setTags($cid, $tags, $type = 'post')
    {
        $tags = str_replace(' ', ',', $tags);
        $tags = array_unique(array_map('trim', explode(',', $tags)));

        /** 取出已有tag */
        $existTags = Typecho::arrayFlatten($this->db->fetchAll(
        $this->db->sql()->select('table.metas', '`mid`')
        ->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $cid)
        ->where('table.metas.`type` = ?', 'tag')
        ->group('table.metas.`mid`')), 'mid');
        
        /** 删除已有tag */
        if($existTags)
        {
            foreach($existTags as $tag)
            {
                $this->db->query($this->db->sql()->delete('table.relationships')
                ->where('`cid` = ?', $cid)
                ->where('`mid` = ?', $tag));
                
                $row = $this->db->fetchRow($this->db->sql()->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->join('table.contents', 'table.relationships.`cid` = table.contents.`cid`')
                ->where('table.relationships.`mid` = ?', $tag)
                ->where('table.contents.`type` = ?', $type));
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $row['num'])
                ->where('`mid` = ?', $tag));
            }
        }
        
        /** 取出插入tag */
        $insertTags = $this->getTags($tags);
        
        /** 插入tag */
        if($insertTags)
        {
            foreach($insertTags as $tag)
            {
                $this->db->query($this->db->sql()->insert('table.relationships')
                ->rows(array(
                    'mid'  =>   $tag,
                    'cid'  =>   $cid
                )));
                
                $row = $this->db->fetchRow($this->db->sql()->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->join('table.contents', 'table.relationships.`cid` = table.contents.`cid`')
                ->where('table.relationships.`mid` = ?', $tag)
                ->where('table.contents.`type` = ?', $type));
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $row['num'])
                ->where('`mid` = ?', $tag));
            }
        }

        return implode(',', $tags);
    }
    
    /**
     * 根据tag获取ID
     * 
     * @access protected
     * @param array $tags
     * @return array
     */
    protected function getTags(array $tags)
    {
        $result = array();
        foreach($tags as $tag)
        {
            $row = $this->db->fetchRow($this->db->sql()->select('table.metas', '`mid`')
            ->where('`name` = ?', $tag)->limit(1));
            
            if($row)
            {
                $result[] = $row['mid'];
            }
            else
            {
                $result[] = 
                $this->db->query($this->db->sql()->insert('table.metas')
                ->rows(array(
                    'name'  =>  $tag,
                    'slug'  =>  urlencode($tag),
                    'type'  =>  'tag',
                    'count' =>  0,
                    'sort'  =>  0,
                )));
            }
        }
        
        return $result;
    }

    /**
     * 设置分类
     * 
     * @access protected
     * @param integer $cid
     * @param array $categories
     * @param string $type
     * @return integer
     */
    protected function setCategories($cid, array $categories, $type = 'post')
    {
        $categories = array_unique(array_map('trim', $categories));

        /** 取出已有category */
        $existCategories = Typecho::arrayFlatten($this->db->fetchAll(
        $this->db->sql()->select('table.metas', '`mid`')
        ->join('table.relationships', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('table.relationships.`cid` = ?', $cid)
        ->where('table.metas.`type` = ?', 'category')
        ->group('table.metas.`mid`')), 'mid');
        
        /** 删除已有category */
        if($existCategories)
        {
            foreach($existCategories as $category)
            {
                $this->db->query($this->db->sql()->delete('table.relationships')
                ->where('`cid` = ?', $cid)
                ->where('`mid` = ?', $category));
                
                $row = $this->db->fetchRow($this->db->sql()->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->join('table.contents', 'table.relationships.`cid` = table.contents.`cid`')
                ->where('table.relationships.`mid` = ?', $category)
                ->where('table.contents.`type` = ?', $type));
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $row['num'])
                ->where('`mid` = ?', $category));
            }
        }
        
        /** 插入category */
        if($categories)
        {
            foreach($categories as $category)
            {
                $this->db->query($this->db->sql()->insert('table.relationships')
                ->rows(array(
                    'mid'  =>   $category,
                    'cid'  =>   $cid
                )));
                
                $row = $this->db->fetchRow($this->db->sql()->select('table.relationships', 'COUNT(table.relationships.`cid`) AS `num`')
                ->join('table.contents', 'table.relationships.`cid` = table.contents.`cid`')
                ->where('table.relationships.`mid` = ?', $category)
                ->where('table.contents.`type` = ?', $type));
                
                $this->db->query($this->db->sql()->update('table.metas')
                ->row('count', $row['num'])
                ->where('`mid` = ?', $category));
            }
        }
        
        /** 取出第一个分类 */
        $allCategories = Typecho::arrayFlatten($this->db->fetchAll(
        $this->db->sql()->select('table.metas', '`mid`')
        ->where('table.metas.`type` = ?', 'category')
        ->order('table.metas.`sort`', 'ASC')), 'mid');
        $currentCategory = widget('Options')->defaultCategory;
        
        foreach($allCategories as $category)
        {
            if(in_array($category, $categories))
            {
                $currentCategory = $category;
                break;
            }
        }

        return $currentCategory;
    }

    protected function havePostPermission($userId)
    {
        if(!widget('Access')->pass('editor', true))
        {
            if($userId != widget('Access')->user('uid'))
            {
                return false;
            }
        }

        return true;
    }
}
