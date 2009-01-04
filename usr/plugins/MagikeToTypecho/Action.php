<?php

class MagikeToTypecho_Action extends Typecho_Widget implements Widget_Interface_Do
{
    public function doImport()
    {
        $options = $this->widget('Widget_Options')->plugin('MagikeToTypecho');

        /** 初始化一个db */
        if (Typecho_Db_Adapter_Mysql::isAvailable()) {
            $db = new Typecho_Db('Mysql', $options->prefix);
        } else {
            $db = new Typecho_Db('Pdo_Mysql', $options->prefix);
        }
        
        /** 只读即可 */
        $db->addServer(array (
          'host' => $options->host,
          'user' => $options->user,
          'password' => $options->password,
          'charset' => 'utf8',
          'port' => $options->port,
          'database' => $options->database
        ), Typecho_Db::READ);
        
        /** 删除当前内容 */
        $masterDb = Typecho_Db::get();
        $this->widget('Widget_Abstract_Contents')->to($contents)->delete($masterDb->sql()->where('1 = 1'));
        $this->widget('Widget_Abstract_Comments')->to($comments)->delete($masterDb->sql()->where('1 = 1'));
        $this->widget('Widget_Abstract_Metas')->to($metas)->delete($masterDb->sql()->where('1 = 1'));
        $this->widget('Widget_Contents_Post_Edit')->to($edit);
        $masterDb->query($masterDb->delete('table.relationships')->where('1 = 1'));
        $userId = $this->widget('Widget_User')->uid;
        
        /** 转换程序 */
        
        /** 转换评论 */
        $i = 1;
        
        while (true) {
            $result = $db->query($db->select()->from('table.comments')
            ->order('comment_id', Typecho_Db::SORT_ASC)->page($i, 100));
            $j = 0;
            
            while ($row = $db->fetchRow($result)) {
                $comments->insert(array(
                    'coid'      =>  $row['comment_id'],
                    'cid'       =>  $row['post_id'],
                    'created'   =>  $row['comment_date'],
                    'author'    =>  $row['comment_user'],
                    'authorId'  =>  $row['user_id'],
                    'ownerId'   =>  $userId,
                    'mail'      =>  $row['comment_email'],
                    'url'       =>  $row['comment_homepage'],
                    'ip'        =>  $row['comment_ip'],
                    'agent'     =>  $row['comment_agent'],
                    'text'      =>  $row['comment_text'],
                    'type'      =>  $row['comment_type'],
                    'status'    =>  $row['comment_publish'],
                    'parent'    =>  $row['comment_parent']
                ));
                $j ++;
                unset($row);
            }
            
            if ($j < 100) {
                break;
            }
            
            $i ++;
            unset($result);
        }
        
        /** 转换分类 */
        $cats = $db->fetchAll($db->select()->from('table.categories'));
        foreach ($cats as $cat) {
            $metas->insert(array(
                'mid'           =>  $cat['category_id'],
                'name'          =>  $cat['category_name'],
                'slug'          =>  $cat['category_postname'],
                'description'   =>  $cat['category_describe'],
                'count'         =>  $cat['category_count'],
                'type'          =>  'category',
                'order'         =>  $cat['category_sort']
            ));
        }
        
        /** 转换内容 */
        $i = 1;
        
        while (true) {
            $result = $db->query($db->select()->from('table.posts')
            ->order('post_id', Typecho_Db::SORT_ASC)->page($i, 100));
            $j = 0;
            
            while ($row = $db->fetchRow($result)) {
                $contents->insert(array(
                    'cid'           =>  $row['post_id'],
                    'title'         =>  $row['post_title'],
                    'slug'          =>  $row['post_name'],
                    'created'       =>  $row['post_time'],
                    'modified'      =>  $row['post_edit_time'],
                    'text'          =>  $row['post_content'],
                    'order'         =>  0,
                    'authorId'      =>  $row['user_id'],
                    'template'      =>  NULL,
                    'type'          =>  $row['post_is_page'] ? 'page' : 'post',
                    'status'        =>  $row['post_is_draft'] ? 'draft' : 'publish',
                    'password'      =>  $row['post_password'],
                    'commentsNum'   =>  $row['post_comment_num'],
                    'allowComment'  =>  $row['post_allow_comment'],
                    'allowFeed'     =>  $row['post_allow_feed'],
                    'allowPing'     =>  $row['post_allow_ping']
                ));
                
                /** 插入分类关系 */
                $edit->setCategories($row['post_id'], array($row['category_id']), !$row['post_is_draft']);
                
                /** 设置标签 */
                $edit->setTags($row['post_id'], $row['post_tags'], !$row['post_is_draft']);
                
                $j ++;
                unset($row);
            }
            
            if ($j < 100) {
                break;
            }
            
            $i ++;
            unset($result);
        }
    }

    public function action()
    {
        $this->widget('Widget_User')->pass('administrator');
        $this->onPost()->doImport();
    }
}
