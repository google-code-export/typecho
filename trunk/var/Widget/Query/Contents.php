<?php

class Widget_Query_Contents extends Widget_Query
{
    /**
     * 查询方法
     * 
     * @access public
     * @return Typecho_Db_Query
     */
    public static function select()
    {
        return self::db()->sql()->select('table.contents', 'table.contents.`cid`, table.contents.`title`, table.contents.`slug`, table.contents.`created`,
        table.contents.`modified`, table.contents.`type`, table.contents.`text`, table.contents.`commentsNum`, table.contents.`meta`, table.contents.`template`, table.contents.`author` AS `authorId`,
        table.contents.`password`, table.contents.`allowComment`, table.contents.`allowPing`, table.contents.`allowFeed`, table.users.`screenName` AS `author`, COUNT(table.contents.`cid`) AS `contentsGroupCount`')
        ->join('table.users', 'table.contents.`author` = table.users.`uid`', Typecho_Db::LEFT_JOIN);
    }
    
    /**
     * 获得所有记录数
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return integer
     */
    public static function count(Typecho_Db_Query $condition)
    {
        return self::db()->fetchObject($condition->select('table.contents', 'COUNT(table.contents.`cid`) AS `num`'))->num;
    }
    
    /**
     * 增加记录方法
     * 
     * @access public
     * @param array $content 字段对应值
     * @return Typecho_Db_Query
     */
    public static function insert(array $content)
    {
        $options = self::options();
    
        /** 构建插入结构 */
        $insertStruct = array(
            'title'         =>  empty($content['title']) ? NULL : $content['title'],
            'created'       =>  empty($content['created']) ? $options->gmtTime : $content['created'],
            'modified'      =>  $options->gmtTime,
            'text'          =>  empty($content['text']) ? NULL : $content['text'],
            'slug'          =>  Typecho_API::slugName(empty($content['slug']) ? (empty($content['title']) ? NULL : $content['title'])
            : $content['slug'], $options->gmtTime),
            'meta'          =>  is_numeric($content['meta']) ? '0' : $content['meta'],
            'author'        =>  self::user()->uid,
            'template'      =>  empty($content['template']) ? NULL : $content['template'],
            'type'          =>  empty($content['type']) ? 'post' : $content['type'],
            'password'      =>  empty($content['password']) ? NULL : $content['password'],
            'commentsNum'   =>  0,
            'allowComment'  =>  !empty($content['allowComment']) && 1 == $content['allowComment'] ? 'enable' : 'disable',
            'allowPing'     =>  !empty($content['allowPing']) && 1 == $content['allowPing'] ? 'enable' : 'disable',
            'allowFeed'     =>  !empty($content['allowFeed']) && 1 == $content['allowFeed'] ? 'enable' : 'disable',
        );
        
        /** 首先插入部分数据 */
        return self::db()->insert('table.contents')->rows($insertStruct);
    }
    
    /**
     * 更新记录方法
     * 
     * @access public
     * @param array $content 字段对应值
     * @param Typecho_Db_Query $condition 查询对象
     * @return Typecho_Db_Query
     */
    public static function update(array $content, Typecho_Db_Query $condition)
    {
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
            'slug'          =>  Typecho_API::slugName(empty($content['slug']) ? (empty($content['title']) ? NULL : $content['title'])
            : $content['slug'], $options->gmtTime),
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
        
        $updateStruct['modified'] = self::options()->gmtTime;
        
        /** 首先插入部分数据 */
        return $condition->update('table.contents')->rows($updateStruct);
    }
    
    /**
     * 删除记录方法
     * 
     * @access public
     * @param Typecho_Db_Query $condition 查询对象
     * @return Typecho_Db_Query
     */
    public static function delete(Typecho_Db_Query $condition)
    {
        return $condition->delete('table.contents');
    }
    
    /**
     * 检测当前用户是否具备修改权限
     * 
     * @access public
     * @param integer $userId 文章的作者id
     * @return boolean
     */
    public static function haveContentPermission($userId)
    {
        $user = self::user();
    
        if(!$user->pass('editor', true))
        {
            if($userId != $user->uid)
            {
                return false;
            }
        }

        return true;
    }
    
    /**
     * 获取内容
     * 
     * @access public
     * @param integer $page 页面
     * @param integer $pageSize 页面大小
     * @param string $type 内容类型
     * @return Typecho_Db_Query
     */
    public static function getContents($page = 0, $pageSize = 10, $type = NULL)
    {
        $select = self::select();
        
        if(!empty($type))
        {
            $select->where('table.contents.`type` = ?', $type);
        }
        
        return $select->group('table.contents.`cid`')
        ->order('table.contents.`created`', Typecho_Db::SORT_DESC)
        ->page($page, $pageSize);
    }
    
    /**
     * 获取内容数量
     * 
     * @access public
     * @param string $type 内容类型
     * @return Typecho_Db_Query
     */
    public static function countContents($type = NULL)
    {
        $select = self::select();
        
        if(!empty($type))
        {
            $select->where('table.contents.`type` = ?', $type);
        }
        
        return $select;
    }
    
    /**
     * 根据描述信息表获取内容
     * 
     * @access public
     * @param integer $mid 描述信息表id
     * @param integer $page 页面
     * @param integer $pageSize 页面大小
     * @param string $type 内容类型
     * @return Typecho_Db_Query
     */
    public static function getContentsByMeta($mid, $page = 0, $pageSize = 10, $type = NULL)
    {
        return self::getContents($page, $pageSize, $type)
        ->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
        ->where('table.relationships.`mid` = ?', $mid);
    }
    
    /**
     * 根据描述信息表获取内容数量
     * 
     * @access public
     * @param integer $mid 描述信息表id
     * @param string $type 内容类型
     * @return Typecho_Db_Query
     */
    public static function countContentsByMeta($mid, $type = NULL)
    {
        return self::countContents($type)
        ->join('table.relationships', 'table.contents.`cid` = table.relationships.`cid`')
        ->where('table.relationships.`mid` = ?', $mid);
    }
    
    public static function getSingleContentById($cid, $type = NULL)
    {
        $select = self::select();
        
        if(!empty($type))
        {
            $select->where('table.contents.`type` = ?', $type);
        }
        
        return $select->where('table.contents.`cid` = ?', $cid)
        ->group('table.contents.`cid`')
        ->limit(1);
    }
    
    public static function getSingleContentBySlugName($slugName, $type = NULL)
    {
        $select = self::select();
        
        if(!empty($type))
        {
            $select->where('table.contents.`type` = ?', $type);
        }
        
        return $select->where('table.contents.`slug` = ?', $slugName)
        ->group('table.contents.`cid`')
        ->limit(1);
    }
}
