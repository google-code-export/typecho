CREATE TABLE typecho_comments ( coid INTEGER NOT NULL PRIMARY KEY, cid int(10) default '0' , created int(10) default '0' , author varchar(200) default NULL , mail varchar(200) default NULL , url varchar(200) default NULL , ip varchar(64) default NULL , agent varchar(200) default NULL , text text , mode varchar(9) default 'comment' , status varchar(8) default 'approved' , parent int(10) default '0' );
CREATE INDEX typecho_comments_cid ON typecho_comments (cid);
CREATE INDEX typecho_comments_created ON typecho_comments (created);

CREATE TABLE typecho_contents ( cid INTEGER NOT NULL PRIMARY KEY, title varchar(200) default NULL , slug varchar(128) default NULL , uri varchar(200) default NULL , created int(10) default '0' , modified int(10) default '0' , text text , meta int(10) default '0' , author int(10) default '0' , template varchar(32) default NULL , type varchar(32) default NULL , password varchar(32) default NULL , commentsNum int(10) default '0' , allowComment varchar(7) default 'disable' , allowPing varchar(7) default 'disable' , allowFeed varchar(7) default 'disable' );
CREATE INDEX typecho_contents_slug ON typecho_contents (slug);
CREATE INDEX typecho_contents_created ON typecho_contents (created);
CREATE INDEX typecho_contents_author ON typecho_contents (author);

CREATE TABLE typecho_metas ( mid INTEGER NOT NULL PRIMARY KEY, name varchar(200) default NULL , slug varchar(128) default NULL , type varchar(32) NOT NULL , description varchar(200) default NULL , count int(10) default '0' , sort int(10) default '0' );
CREATE INDEX typecho_metas_slug ON typecho_metas (slug);

CREATE TABLE typecho_options ( name varchar(32) NOT NULL , user int(10) NOT NULL default '0' , value text );
CREATE UNIQUE INDEX typecho_options_ ON typecho_options (name,user);

CREATE TABLE typecho_relationships ( cid int(10) NOT NULL , mid int(10) NOT NULL );
CREATE UNIQUE INDEX typecho_relationships_ ON typecho_relationships (cid,mid);

CREATE TABLE typecho_users ( uid INTEGER NOT NULL PRIMARY KEY, name varchar(32) default NULL , password varchar(32) default NULL , mail varchar(200) default NULL , url varchar(200) default NULL , screenName varchar(32) default NULL , created int(10) default '0' , activated int(10) default '0' , logged int(10) default '0' , "group" varchar(13) default 'visitor' , authCode varchar(40) default NULL );
CREATE UNIQUE INDEX typecho_users_name ON typecho_users (name);
CREATE UNIQUE INDEX typecho_users_mail ON typecho_users (mail);
