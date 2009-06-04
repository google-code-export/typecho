<?php
/**
 * 升级程序
 * 
 * @category typecho
 * @package Upgrade
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 升级程序
 * 
 * @category typecho
 * @package Upgrade
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Upgrade
{
    /**
     * 升级至9.1.7
     * 
     * @access public
     * @param Typecho_Db $db 数据库对象
     * @param Typecho_Widget $options 全局信息组件
     * @return void
     */
    public static function v0_3r9_1_7($db, $options)
    {
        /** 转换评论 */
        $i = 1;

        while (true) {
            $result = $db->query($db->select('coid', 'text')->from('table.comments')
            ->order('coid', Typecho_Db::SORT_ASC)->page($i, 100));
            $j = 0;
            
            while ($row = $db->fetchRow($result)) {
                $text = nl2br($row['text']);

                $db->query($db->update('table.comments')
                ->rows(array('text' => $text))
                ->where('coid = ?', $row['coid']));
                
                $j ++;
                unset($text);
                unset($row);
            }
            
            if ($j < 100) {
                break;
            }
            
            $i ++;
            unset($result);
        }

        /** 转换内容 */
        $i = 1;

        while (true) {
            $result = $db->query($db->select('cid', 'text')->from('table.contents')
            ->order('cid', Typecho_Db::SORT_ASC)->page($i, 100));
            $j = 0;
            
            while ($row = $db->fetchRow($result)) {
                $text = preg_replace(
                array("/\s*<p>/is", "/\s*<\/p>\s*/is", "/\s*<br\s*\/>\s*/is",
                "/\s*<(div|blockquote|pre|table|ol|ul)>/is", "/<\/(div|blockquote|pre|table|ol|ul)>\s*/is"),
                array('', "\n\n", "\n", "\n\n<\\1>", "</\\1>\n\n"), 
                $row['text']);

                $db->query($db->update('table.contents')
                ->rows(array('text' => $text))
                ->where('cid = ?', $row['cid']));
                
                $j ++;
                unset($text);
                unset($row);
            }
            
            if ($j < 100) {
                break;
            }
            
            $i ++;
            unset($result);
        }
    }
    
    /**
     * 升级至9.1.14
     * 
     * @access public
     * @param Typecho_Db $db 数据库对象
     * @param Typecho_Widget $options 全局信息组件
     * @return void
     */
    public static function v0_4r9_1_14($db, $options)
    {
        if (is_writeable(__TYPECHO_ROOT_DIR__ . '/config.inc.php')) {
            $handle = fopen(__TYPECHO_ROOT_DIR__ . '/config.inc.php', 'ab');
            fwrite($handle, '
/** 初始化时区 */
Typecho_Date::setTimezoneOffset($options->timezone);
');
            fclose($handle);
        } else {
            throw new Typecho_Exception(_t('config.inc.php 文件无法写入, 请将它的权限设置为可写'));
        }
    }
    
    /**
     * 升级至9.2.3
     * 
     * @access public
     * @param Typecho_Db $db 数据库对象
     * @param Typecho_Widget $options 全局信息组件
     * @return void
     */
    public static function v0_5r9_2_3($db, $options)
    {
        /** 转换评论 */
        $i = 1;

        while (true) {
            $result = $db->query($db->select('coid', 'text')->from('table.comments')
            ->order('coid', Typecho_Db::SORT_ASC)->page($i, 100));
            $j = 0;
            
            while ($row = $db->fetchRow($result)) {
                $text = preg_replace("/\s*<br\s*\/>\s*/i", "\n", $row['text']);

                $db->query($db->update('table.comments')
                ->rows(array('text' => $text))
                ->where('coid = ?', $row['coid']));
                
                $j ++;
                unset($text);
                unset($row);
            }
            
            if ($j < 100) {
                break;
            }
            
            $i ++;
            unset($result);
        }
    }
    
    /**
     * 升级至9.2.18
     * 
     * @access public
     * @param Typecho_Db $db 数据库对象
     * @param Typecho_Widget $options 全局信息组件
     * @return void
     */
    public static function v0_5r9_2_18($db, $options)
    {
        /** 升级编辑器接口 */
        $db->query($db->update('table.options')
        ->rows(array('value' => 350))
        ->where('name = ?', 'editorSize'));
    }
    
    /**
     * 升级至9.2.25
     * 
     * @access public
     * @param Typecho_Db $db 数据库对象
     * @param Typecho_Widget $options 全局信息组件
     * @return void
     */
    public static function v0_5r9_2_25($db, $options)
    {
        /** 升级编辑器接口 */
        $db->query($db->insert('table.options')
        ->rows(array('name' => 'useRichEditor', 'value' => 1)));
    }
    
    /**
     * 升级至9.4.3
     * 
     * @access public
     * @param Typecho_Db $db 数据库对象
     * @param Typecho_Widget $options 全局信息组件
     * @return void
     */
    public static function v0_6r9_4_3($db, $options)
    {
        /** 修改数据库字段 */
        $adapterName = $db->getAdapterName();
        $prefix  = $db->getPrefix();

        //删除老数据
        try {
            switch (true) {
                case false !== strpos($adapterName, 'Mysql'):
                    $db->query('ALTER TABLE  `' . $prefix . 'users` DROP  `meta`', Typecho_Db::WRITE);
                    break;
                    
                case false !== strpos($adapterName, 'Pgsql'):
                    $db->query('ALTER TABLE  "' . $prefix . 'users" DROP COLUMN  "meta"', Typecho_Db::WRITE);
                    break;
                    
                case false !== strpos($adapterName, 'SQLite'):
                    $uuid = uniqid();
                    $db->query('CREATE TABLE ' . $prefix . 'users_' . $uuid . ' ( "uid" INTEGER NOT NULL PRIMARY KEY, 
            "name" varchar(32) default NULL , 
            "password" varchar(64) default NULL , 
            "mail" varchar(200) default NULL , 
            "url" varchar(200) default NULL , 
            "screenName" varchar(32) default NULL , 
            "created" int(10) default \'0\' , 
            "activated" int(10) default \'0\' , 
            "logged" int(10) default \'0\' , 
            "group" varchar(16) default \'visitor\' , 
            "authCode" varchar(64) default NULL)', Typecho_Db::WRITE);
                    $db->query('INSERT INTO ' . $prefix . 'users_' . $uuid . ' ("uid", "name", "password", "mail", "url"
                    , "screenName", "created", "activated", "logged", "group", "authCode") SELECT "uid", "name", "password", "mail", "url"
                    , "screenName", "created", "activated", "logged", "group", "authCode" FROM ' . $prefix . 'users', Typecho_Db::WRITE);
                    $db->query('DROP TABLE  ' . $prefix . 'users', Typecho_Db::WRITE);
                    $db->query('CREATE TABLE ' . $prefix . 'users ( "uid" INTEGER NOT NULL PRIMARY KEY, 
            "name" varchar(32) default NULL , 
            "password" varchar(64) default NULL , 
            "mail" varchar(200) default NULL , 
            "url" varchar(200) default NULL , 
            "screenName" varchar(32) default NULL , 
            "created" int(10) default \'0\' , 
            "activated" int(10) default \'0\' , 
            "logged" int(10) default \'0\' , 
            "group" varchar(16) default \'visitor\' , 
            "authCode" varchar(64) default NULL)', Typecho_Db::WRITE);
                    $db->query('INSERT INTO ' . $prefix . 'users SELECT * FROM ' . $prefix . 'users_' . $uuid, Typecho_Db::WRITE);
                    $db->query('DROP TABLE  ' . $prefix . 'users_' . $uuid, Typecho_Db::WRITE);
                    $db->query('CREATE UNIQUE INDEX ' . $prefix . 'users_name ON ' . $prefix . 'users ("name")', Typecho_Db::WRITE);
                    $db->query('CREATE UNIQUE INDEX ' . $prefix . 'users_mail ON ' . $prefix . 'users ("mail")', Typecho_Db::WRITE);
                    
                    break;
                    
                default:
                    break;
            }
        } catch (Typecho_Db_Exception $e) {
            //do nothing
        }

        //将slug字段长度增加到200
        try {
            switch (true) {
                case false !== strpos($adapterName, 'Mysql'):
                    $db->query("ALTER TABLE  `" . $prefix . "contents` MODIFY COLUMN `slug` varchar(200)", Typecho_Db::WRITE);
                    $db->query("ALTER TABLE  `" . $prefix . "metas` MODIFY COLUMN `slug` varchar(200)", Typecho_Db::WRITE);
                    break;
                    
                case false !== strpos($adapterName, 'Pgsql'):
                    $db->query('ALTER TABLE  "' . $prefix . 'contents" ALTER COLUMN  "slug" TYPE varchar(200)', Typecho_Db::WRITE);
                    $db->query('ALTER TABLE  "' . $prefix . 'metas" ALTER COLUMN  "slug" TYPE varchar(200)', Typecho_Db::WRITE);
                    break;
                    
                case false !== strpos($adapterName, 'SQLite'):
                    $uuid = uniqid();
                    $db->query('CREATE TABLE ' . $prefix . 'contents' . $uuid . ' ( "cid" INTEGER NOT NULL PRIMARY KEY, 
        "title" varchar(200) default NULL , 
        "slug" varchar(200) default NULL , 
        "created" int(10) default \'0\' , 
        "modified" int(10) default \'0\' , 
        "text" text , 
        "order" int(10) default \'0\' , 
        "authorId" int(10) default \'0\' , 
        "template" varchar(32) default NULL , 
        "type" varchar(16) default \'post\' , 
        "status" varchar(16) default \'publish\' , 
        "password" varchar(32) default NULL , 
        "commentsNum" int(10) default \'0\' , 
        "allowComment" char(1) default \'0\' , 
        "allowPing" char(1) default \'0\' , 
        "allowFeed" char(1) default \'0\' )', Typecho_Db::WRITE);
                    $db->query('INSERT INTO ' . $prefix . 'contents' . $uuid . ' SELECT * FROM ' . $prefix . 'contents', Typecho_Db::WRITE);
                    $db->query('DROP TABLE  ' . $prefix . 'contents', Typecho_Db::WRITE);
                    $db->query('CREATE TABLE ' . $prefix . 'contents ( "cid" INTEGER NOT NULL PRIMARY KEY, 
        "title" varchar(200) default NULL , 
        "slug" varchar(200) default NULL , 
        "created" int(10) default \'0\' , 
        "modified" int(10) default \'0\' , 
        "text" text , 
        "order" int(10) default \'0\' , 
        "authorId" int(10) default \'0\' , 
        "template" varchar(32) default NULL , 
        "type" varchar(16) default \'post\' , 
        "status" varchar(16) default \'publish\' , 
        "password" varchar(32) default NULL , 
        "commentsNum" int(10) default \'0\' , 
        "allowComment" char(1) default \'0\' , 
        "allowPing" char(1) default \'0\' , 
        "allowFeed" char(1) default \'0\' )', Typecho_Db::WRITE);
                    $db->query('INSERT INTO ' . $prefix . 'contents SELECT * FROM ' . $prefix . 'contents' . $uuid, Typecho_Db::WRITE);
                    $db->query('DROP TABLE  ' . $prefix . 'contents' . $uuid, Typecho_Db::WRITE);
                    $db->query('CREATE UNIQUE INDEX ' . $prefix . 'contents_slug ON ' . $prefix . 'contents ("slug")', Typecho_Db::WRITE);
                    $db->query('CREATE INDEX ' . $prefix . 'contents_created ON ' . $prefix . 'contents ("created")', Typecho_Db::WRITE);
                    
                    $db->query('CREATE TABLE ' . $prefix . 'metas' . $uuid . ' ( "mid" INTEGER NOT NULL PRIMARY KEY, 
        "name" varchar(200) default NULL , 
        "slug" varchar(200) default NULL , 
        "type" varchar(32) NOT NULL , 
        "description" varchar(200) default NULL , 
        "count" int(10) default \'0\' , 
        "order" int(10) default \'0\' )', Typecho_Db::WRITE);
                    $db->query('INSERT INTO ' . $prefix . 'metas' . $uuid . ' SELECT * FROM ' . $prefix . 'metas', Typecho_Db::WRITE);
                    $db->query('DROP TABLE  ' . $prefix . 'metas', Typecho_Db::WRITE);
                    $db->query('CREATE TABLE ' . $prefix . 'metas ( "mid" INTEGER NOT NULL PRIMARY KEY, 
        "name" varchar(200) default NULL , 
        "slug" varchar(200) default NULL , 
        "type" varchar(32) NOT NULL , 
        "description" varchar(200) default NULL , 
        "count" int(10) default \'0\' , 
        "order" int(10) default \'0\' )', Typecho_Db::WRITE);
                    $db->query('INSERT INTO ' . $prefix . 'metas SELECT * FROM ' . $prefix . 'metas' . $uuid, Typecho_Db::WRITE);
                    $db->query('DROP TABLE  ' . $prefix . 'metas' . $uuid, Typecho_Db::WRITE);
                    $db->query('CREATE INDEX ' . $prefix . 'metas_slug ON ' . $prefix . 'metas ("slug")', Typecho_Db::WRITE);
                    
                    break;
                    
                default:
                    break;
            }
        } catch (Typecho_Db_Exception $e) {
            //do nothing
        }
    }
    
    /**
     * 升级至9.4.21
     * 
     * @access public
     * @param Typecho_Db $db 数据库对象
     * @param Typecho_Widget $options 全局信息组件
     * @return void
     */
    public static function v0_6r9_4_21($db, $options)
    {
        //创建上传目录
        $uploadDir = Typecho_Common::url(Widget_Upload::UPLOAD_PATH, __TYPECHO_ROOT_DIR__);
        if (is_dir($uploadDir)) {
            if (!is_writeable($uploadDir)) {
                if (!@chmod($uploadDir, 0644)) {
                    throw new Typecho_Widget_Exception(_t('上传目录无法写入, 请手动将安装目录下的 %s 目录的权限设置为可写然后继续升级', Widget_Upload::UPLOAD_PATH));
                }
            }
        } else {
            if (!@mkdir($uploadDir, 0644)) {
                throw new Typecho_Widget_Exception(_t('上传目录无法创建, 请手动创建安装目录下的 %s 目录, 并将它的权限设置为可写然后继续升级', Widget_Upload::UPLOAD_PATH));
            }
        }

        /** 增加自定义主页 */
        $db->query($db->insert('table.options')
                ->rows(array('name' => 'customHomePage', 'value' => 0)));
                
        /** 增加文件上传散列函数 */
        $db->query($db->insert('table.options')
                ->rows(array('name' => 'uploadHandle', 'value' => 'a:2:{i:0;s:13:"Widget_Upload";i:1;s:12:"uploadHandle";}')));
                
        /** 增加文件删除函数 */
        $db->query($db->insert('table.options')
                ->rows(array('name' => 'deleteHandle', 'value' => 'a:2:{i:0;s:13:"Widget_Upload";i:1;s:12:"deleteHandle";}')));
                
        /** 增加文件展现散列函数 */
        $db->query($db->insert('table.options')
                ->rows(array('name' => 'attachmentHandle', 'value' => 'a:2:{i:0;s:13:"Widget_Upload";i:1;s:16:"attachmentHandle";}')));
                
        /** 增加文件扩展名 */
        $db->query($db->insert('table.options')
                ->rows(array('name' => 'attachmentTypes', 'value' => '*.jpg;*.gif;*.png;*.zip;*.tar.gz')));
                
        /** 增加路由 */
        $routingTable = $options->routingTable;
        if (isset($routingTable[0])) {
            unset($routingTable[0]);
        }

        $pre = array_slice($routingTable, 0, 2);
        $next = array_slice($routingTable, 2);

        $routingTable = array_merge($pre, array('attachment' => 
          array (
            'url' => '/attachment/[cid:digital]/',
            'widget' => 'Widget_Archive',
            'action' => 'render',
          )), $next);

        $db->query($db->update('table.options')
                ->rows(array('value' => serialize($routingTable)))
                ->where('name = ?', 'routingTable'));
    }
    
    /**
     * 升级至9.6.1
     * 
     * @access public
     * @param Typecho_Db $db 数据库对象
     * @param Typecho_Widget $options 全局信息组件
     * @return void
     */
    public static function v0_6r9_6_1($db, $options)
    {
        /** 去掉所见即所得编辑器 */
        $db->query($db->delete('table.options')
        ->where('name = ?', 'useRichEditor'));
        
        /** 修正自动保存值 */
        $db->query($db->update('table.options')
        ->rows(array('value' => 0))
        ->where('name = ?', 'autoSave'));
        
        /** 增加堆楼楼层数目限制 */
        $db->query($db->insert('table.options')
        ->rows(array('name' => 'commentsMaxNestingLevels', 'value' => 5)));
    }
}
