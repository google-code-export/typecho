<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/** 修改数据库字段 */
$adapterName = $this->db->getAdapterName();
$prefix  = $this->db->getPrefix();

//删除老数据
try {
    switch (true) {
        case false !== strpos($adapterName, 'Mysql'):
            $this->db->query('ALTER TABLE  `' . $prefix . 'users` DROP  `meta`', Typecho_Db::WRITE);
            break;
            
        case false !== strpos($adapterName, 'Pgsql'):
            $this->db->query('ALTER TABLE  "' . $prefix . 'users" DROP COLUMN  "meta"', Typecho_Db::WRITE);
            break;
            
        case false !== strpos($adapterName, 'SQLite'):
            $uuid = uniqid();
            $this->db->query('CREATE TABLE ' . $prefix . 'users_' . $uuid . ' ( "uid" INTEGER NOT NULL PRIMARY KEY, 
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
            $this->db->query('INSERT INTO ' . $prefix . 'users_' . $uuid . ' ("uid", "name", "password", "mail", "url"
            , "screenName", "created", "activated", "logged", "group", "authCode") SELECT "uid", "name", "password", "mail", "url"
            , "screenName", "created", "activated", "logged", "group", "authCode" FROM ' . $prefix . 'users', Typecho_Db::WRITE);
            $this->db->query('DROP TABLE  ' . $prefix . 'users', Typecho_Db::WRITE);
            $this->db->query('CREATE TABLE ' . $prefix . 'users ( "uid" INTEGER NOT NULL PRIMARY KEY, 
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
            $this->db->query('INSERT INTO ' . $prefix . 'users SELECT * FROM ' . $prefix . 'users_' . $uuid, Typecho_Db::WRITE);
            $this->db->query('DROP TABLE  ' . $prefix . 'users_' . $uuid, Typecho_Db::WRITE);
            $this->db->query('CREATE UNIQUE INDEX ' . $prefix . 'users_name ON ' . $prefix . 'users ("name")', Typecho_Db::WRITE);
            $this->db->query('CREATE UNIQUE INDEX ' . $prefix . 'users_mail ON ' . $prefix . 'users ("mail")', Typecho_Db::WRITE);
            
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
            $this->db->query("ALTER TABLE  `" . $prefix . "contents` MODIFY COLUMN `slug` varchar(200)", Typecho_Db::WRITE);
            $this->db->query("ALTER TABLE  `" . $prefix . "metas` MODIFY COLUMN `slug` varchar(200)", Typecho_Db::WRITE);
            break;
            
        case false !== strpos($adapterName, 'Pgsql'):
            $this->db->query('ALTER TABLE  "' . $prefix . 'contents" ALTER COLUMN  "slug" TYPE varchar(200)', Typecho_Db::WRITE);
            $this->db->query('ALTER TABLE  "' . $prefix . 'metas" ALTER COLUMN  "slug" TYPE varchar(200)', Typecho_Db::WRITE);
            break;
            
        case false !== strpos($adapterName, 'SQLite'):
            $uuid = uniqid();
            $this->db->query('CREATE TABLE ' . $prefix . 'contents' . $uuid . ' ( "cid" INTEGER NOT NULL PRIMARY KEY, 
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
            $this->db->query('INSERT INTO ' . $prefix . 'contents' . $uuid . ' SELECT * FROM ' . $prefix . 'contents', Typecho_Db::WRITE);
            $this->db->query('DROP TABLE  ' . $prefix . 'contents', Typecho_Db::WRITE);
            $this->db->query('CREATE TABLE ' . $prefix . 'contents ( "cid" INTEGER NOT NULL PRIMARY KEY, 
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
            $this->db->query('INSERT INTO ' . $prefix . 'contents SELECT * FROM ' . $prefix . 'contents' . $uuid, Typecho_Db::WRITE);
            $this->db->query('DROP TABLE  ' . $prefix . 'contents' . $uuid, Typecho_Db::WRITE);
            $this->db->query('CREATE UNIQUE INDEX ' . $prefix . 'contents_slug ON ' . $prefix . 'contents ("slug")', Typecho_Db::WRITE);
            $this->db->query('CREATE INDEX ' . $prefix . 'contents_created ON ' . $prefix . 'contents ("created")', Typecho_Db::WRITE);
            
            $this->db->query('CREATE TABLE ' . $prefix . 'metas' . $uuid . ' ( "mid" INTEGER NOT NULL PRIMARY KEY, 
"name" varchar(200) default NULL , 
"slug" varchar(200) default NULL , 
"type" varchar(32) NOT NULL , 
"description" varchar(200) default NULL , 
"count" int(10) default \'0\' , 
"order" int(10) default \'0\' )', Typecho_Db::WRITE);
            $this->db->query('INSERT INTO ' . $prefix . 'metas' . $uuid . ' SELECT * FROM ' . $prefix . 'metas', Typecho_Db::WRITE);
            $this->db->query('DROP TABLE  ' . $prefix . 'metas', Typecho_Db::WRITE);
            $this->db->query('CREATE TABLE ' . $prefix . 'metas ( "mid" INTEGER NOT NULL PRIMARY KEY, 
"name" varchar(200) default NULL , 
"slug" varchar(200) default NULL , 
"type" varchar(32) NOT NULL , 
"description" varchar(200) default NULL , 
"count" int(10) default \'0\' , 
"order" int(10) default \'0\' )', Typecho_Db::WRITE);
            $this->db->query('INSERT INTO ' . $prefix . 'metas SELECT * FROM ' . $prefix . 'metas' . $uuid, Typecho_Db::WRITE);
            $this->db->query('DROP TABLE  ' . $prefix . 'metas' . $uuid, Typecho_Db::WRITE);
            $this->db->query('CREATE INDEX ' . $prefix . 'metas_slug ON ' . $prefix . 'metas ("slug")', Typecho_Db::WRITE);
            
            break;
            
        default:
            break;
    }
} catch (Typecho_Db_Exception $e) {
    //do nothing
}
