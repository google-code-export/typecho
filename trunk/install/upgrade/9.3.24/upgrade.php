<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/** 增加数据库字段 */
$adapterName = $this->db->getAdapterName();
$prefix  = $this->db->getPrefix();

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
            
            break;
            
        default:
            break;
    }
} catch (Typecho_Db_Exception $e) {
    //do nothing
}
