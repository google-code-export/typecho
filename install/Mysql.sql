-- phpMyAdmin SQL Dump
-- version 2.11.5
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2008 年 07 月 06 日 18:00
-- 服务器版本: 5.0.51
-- PHP 版本: 5.2.5

--
-- 数据库: `typecho`
--

-- --------------------------------------------------------

--
-- 表的结构 `typecho_comments`
--

CREATE TABLE `typecho_comments` (
  `coid` int(10) unsigned NOT NULL auto_increment COMMENT 'comment表主键',
  `cid` int(10) unsigned default '0' COMMENT 'post表主键,关联字段',
  `created` int(10) unsigned default '0' COMMENT '评论生成时的GMT unix时间戳',
  `author` varchar(200) default NULL COMMENT '评论作者',
  `authorId` int(10) unsigned default '0' COMMENT '评论所属用户id',
  `ownerId` int(10) unsigned default '0' COMMENT '评论所属内容作者id',
  `mail` varchar(200) default NULL COMMENT '评论者邮件',
  `url` varchar(200) default NULL COMMENT '评论者网址',
  `ip` varchar(64) default NULL COMMENT '评论者ip地址',
  `agent` varchar(200) default NULL COMMENT '评论者客户端',
  `text` text COMMENT '评论文字',
  `type` varchar(16) default 'comment' COMMENT '评论类型',
  `status` varchar(16) default 'approved' COMMENT '评论状态',
  `parent` int(10) unsigned default '0' COMMENT '父级评论',
  PRIMARY KEY  (`coid`),
  KEY `cid` (`cid`),
  KEY `created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_contents`
--

CREATE TABLE `typecho_contents` (
  `cid` int(10) unsigned NOT NULL auto_increment COMMENT 'post表主键',
  `title` varchar(200) default NULL COMMENT '内容标题',
  `slug` varchar(200) default NULL COMMENT '内容缩略名',
  `created` int(10) unsigned default '0' COMMENT '内容生成时的GMT unix时间戳',
  `modified` int(10) unsigned default '0' COMMENT '内容更改时的GMT unix时间戳',
  `text` text COMMENT '内容文字',
  `order` int(10) unsigned default '0',
  `authorId` int(10) unsigned default '0' COMMENT '内容所属用户id',
  `template` varchar(32) default NULL COMMENT '内容使用的模板',
  `type` varchar(16) default 'post' COMMENT '内容类别',
  `status` varchar(16) default 'publish' COMMENT '内容状态',
  `password` varchar(32) default NULL COMMENT '受保护内容,此字段对应内容保护密码',
  `commentsNum` int(10) unsigned default '0' COMMENT '内容所属评论数,冗余字段',
  `allowComment` char(1) default '0' COMMENT '是否允许评论',
  `allowPing` char(1) default '0' COMMENT '是否允许ping',
  `allowFeed` char(1) default '0' COMMENT '允许出现在聚合中',
  `parent` int(10) unsigned default '0' COMMENT '父id',
  PRIMARY KEY  (`cid`),
  UNIQUE KEY `slug` (`slug`),
  KEY `created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_metas`
--

CREATE TABLE `typecho_metas` (
  `mid` int(10) unsigned NOT NULL auto_increment COMMENT '项目主键',
  `name` varchar(200) default NULL COMMENT '名称',
  `slug` varchar(200) default NULL COMMENT '项目缩略名',
  `type` varchar(32) NOT NULL COMMENT '项目类型',
  `description` varchar(200) default NULL COMMENT '选项描述',
  `count` int(10) unsigned default '0' COMMENT '项目所属内容个数',
  `order` int(10) unsigned default '0' COMMENT '项目排序',
  PRIMARY KEY  (`mid`),
  KEY `slug` (`slug`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_options`
--

CREATE TABLE `typecho_options` (
  `name` varchar(32) NOT NULL COMMENT '配置名称',
  `user` int(10) unsigned NOT NULL default '0' COMMENT '配置所属用户,默认为0(全局配置)',
  `value` text COMMENT '配置值',
  PRIMARY KEY  (`name`,`user`)
) ENGINE=MyISAM DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_relationships`
--

CREATE TABLE `typecho_relationships` (
  `cid` int(10) unsigned NOT NULL COMMENT '内容主键',
  `mid` int(10) unsigned NOT NULL COMMENT '项目主键',
  PRIMARY KEY  (`cid`,`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=%charset%;

-- --------------------------------------------------------

--
-- 表的结构 `typecho_users`
--

CREATE TABLE `typecho_users` (
  `uid` int(10) unsigned NOT NULL auto_increment COMMENT 'user表主键',
  `name` varchar(32) default NULL COMMENT '用户名称',
  `password` varchar(64) default NULL COMMENT '用户密码',
  `mail` varchar(200) default NULL COMMENT '用户的邮箱',
  `url` varchar(200) default NULL COMMENT '用户的主页',
  `screenName` varchar(32) default NULL COMMENT '用户显示的名称',
  `created` int(10) unsigned default '0' COMMENT '用户注册时的GMT unix时间戳',
  `activated` int(10) unsigned default '0' COMMENT '最后活动时间',
  `logged` int(10) unsigned default '0' COMMENT '上次登录最后活跃时间',
  `group` varchar(16) default 'visitor' COMMENT '用户组',
  `authCode` varchar(64) default NULL COMMENT '登录验证码',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=MyISAM  DEFAULT CHARSET=%charset%;
