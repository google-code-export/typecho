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
class ContentsPost extends Post
{
    protected function setTags($cid, $tags, $oldTags = NULL)
    {
        $tags = str_replace(' ', ',', $tags);
        $oldTags = array_unique(array_map('trim', explode(',', $oldTags)));
        $tags = array_unique(array_map('trim', explode(',', $tags)));
        
        //需要删除的标签
        $deleteTags = array_diff($oldTags, $tags);
        
        //需要增加的标签
        $insertTags = array_diff($tags, $oldTags);
        
        //删除标签
        if($deleteTags)
        {
            foreach($deleteTags as $tag)
            {
                //获取tag索引
                $existTag = $this->db->fetchRow($this->db->sql()
                ->select('table.metas', '`mid`')
                ->where('`type` = ?', 'tag')
                ->where('`slug` = ?', urlencode($tag))
                ->limit(1));
                
                //删除关系
                $this->db->query($this->db->sql()
                ->delete('table.relationships')
                ->where('`cid` = ?', $cid)
                ->where('`mid` = ?', $existTag['mid']));
                
                //获取关系数目
                $count = $this->db->fetchRow($this->db->sql()
                ->select('table.relationships', 'COUNT(`cid`) AS `num`')
                ->where('`mid` = ?', $existTag['mid']));
                
                //更新冗余字段
                $this->db->query($this->db->sql()
                ->update('table.metas')
                ->rows(array('count' => $count['num']))
                ->where('`mid` = ?', $existTag['mid']));
            }
        }
        
        //增加标签
        if($insertTags)
        {
            foreach($insertTags as $tag)
            {
                //获取tag索引
                $existTag = $this->db->fetchRow($this->db->sql()
                ->select('table.metas', '`mid`')
                ->where('`type` = ?', 'tag')
                ->where('`slug` = ?', urlencode($tag))
                ->limit(1));

                if($existTag)
                {
                    $tagId = $existTag['mid'];
                }
                else
                {
                    $tagRows = array(
                        'name'  => $tag,
                        'slug'  => urlencode($tag),
                        'type'  => 'tag',
                        'count' => 1
                    );
                        
                    $tagId = $this->db->query($this->db->sql()
                    ->insert('table.metas')
                    ->rows($tagRows));
                }

                $this->db->query($this->db->sql()
                ->insert('table.relationships')
                ->rows(array('cid' => $cid, 'mid' => $tagId)));
            }
        }
        
        return implode(',', $tags);
    }
    
    protected function setCategories($cid, $categories, $oldCategories = array())
    {
        $oldToldCategoriesags = array_unique(array_map('trim', $oldCategories));
        $categories = array_unique(array_map('trim', $categories));
        
        //需要删除的分类
        $deleteCategories = array_diff($oldCategories, $categories);
        
        //需要增加的分类
        $insertCategories = array_diff($categories, $oldCategories);
        
        //删除分类
        if($deleteCategories)
        {
            foreach($deleteCategories as $category)
            {
                //获取category索引
                $existCategory = $this->db->fetchRow($this->db->sql()
                ->select('table.metas', '`mid`')
                ->where('`type` = ?', 'category')
                ->where('`name` = ?', $category)
                ->limit(1));
                
                //删除关系
                $this->db->query($this->db->sql()
                ->delete('table.relationships')
                ->where('`cid` = ?', $cid)
                ->where('`mid` = ?', $existCategory['mid']));
                
                //获取关系数目
                $count = $this->db->fetchRow($this->db->sql()
                ->select('table.relationships', 'COUNT(`cid`) AS `num`')
                ->where('`mid` = ?', $existCategory['mid']));
                
                //更新冗余字段
                $this->db->query($this->db->sql()
                ->update('table.metas')
                ->rows(array('count' => $count['num']))
                ->where('`mid` = ?', $existCategory['mid']));
            }
        }
        
        //增加分类
        if($insertCategories)
        {
            $maxCategory = $this->db->fetchRow($this->db->sql()
            ->select('table.metas', 'max(`sort`) AS `maxSort`')
            ->where('`type` = ?', 'category'));
            
            $maxCategory = empty($maxCategory['maxSort']) ? 0 : $maxCategory['maxSort'];
        
            foreach($insertCategories as $category)
            {
                $maxCategory ++;
                
                //获取category索引
                $existCategory = $this->db->fetchRow($this->db->sql()
                ->select('table.metas', '`mid`')
                ->where('`type` = ?', 'category')
                ->where('`name` = ?', $category)
                ->limit(1));
                
                if($existCategory)
                {
                    $categoryId = $existCategory['mid'];
                }
                else
                {
                    $categoryRows = array(
                        'name'  => $category,
                        'slug'  => typechoSlugName($category, $this->getAutoIncrement('metas')),
                        'type'  => 'category',
                        'count' => 1,
                        'sort'  => $maxCategory
                    );
                        
                    $categoryId = $this->db->query($this->db->sql()
                    ->insert('table.metas')
                    ->rows($categoryRows));
                }

                $this->db->query($this->db->sql()
                ->insert('table.relationships')
                ->rows(array('cid' => $cid, 'mid' => $categoryId)));
            }
        }
        
        //获取优先分类
        $headCategory = $this->db->fetchRow($this->db->sql()
        ->select('table.relationships', 'table.relationships.`mid`')
        ->join('table.metas', 'table.relationships.`mid` = table.metas.`mid`')
        ->where('`cid` = ?', $cid)
        ->where('`type` = ?', 'category')
        ->group('table.relationships.`mid`')
        ->order('table.metas.`sort`')
        ->limit(1));
        
        return $headCategory ? $headCategory['mid'] : 0;
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
