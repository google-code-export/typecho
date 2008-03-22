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
 * @package ContentsPost
 */
class ContentsPost extends Post
{
    protected function setTags($cid, $tags, $oldTags = NULL)
    {
        $tags = str_replace(' ', ',', $tags);
        $oldTags = array_unique(array_map('trim', explode(',', $oldTags)));
        $tags = array_unique(array_map('trim', explode(',', $tags)));
        $tagIdList = array();
        
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
                ->select('table.metas', 'mid')
                ->where('type = ?', 'tag')
                ->where('slug = ?', urlencode($tag))
                ->limit(1));
                
                //删除关系
                $this->db->query($this->db->sql()
                ->delete('table.relationships')
                ->where('cid = ?', $cid)
                ->where('mid = ?', $existTag['mid']));
                
                //获取关系数目
                $count = $this->db->fetchRow($this->db->sql()
                ->select('table.relationships', 'COUNT(cid) as num')
                ->where('mid = ?', $existTag['mid']));
                
                //更新冗余字段
                $this->db->query($this->db->sql()
                ->update('table.metas')
                ->rows(array('count' => $count['num']))
                ->where('mid = ?', $existTag['mid']));
            }
        }
        
        //增加标签
        if($insertTags)
        {
            foreach($insertTags as $tag)
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

                $this->db->query($this->db->sql()
                ->insert('table.relationships')
                ->rows(array('cid' => $cid, 'mid' => $tagId)));
            }
        }
        
        return implode(',', $tags);
    }
    
    protected function setCategories($cid, $categories, $oldCategories = array())
    {
        
    }
}
