--
-- Table structure for table `typecho_comments`
--
CREATE SEQUENCE "typecho_comments_coid_seq";

CREATE TABLE "typecho_comments" (  "coid" BIGINT NOT NULL DEFAULT nextval('typecho_comments_coid_seq'),
  "cid" BIGINT NULL DEFAULT '0',
  "created" BIGINT NULL DEFAULT '0',
  "author" VARCHAR(200) NULL DEFAULT NULL,
  "mail" VARCHAR(200) NULL DEFAULT NULL,
  "url" VARCHAR(200) NULL DEFAULT NULL,
  "ip" VARCHAR(64) NULL DEFAULT NULL,
  "agent" VARCHAR(200) NULL DEFAULT NULL,
  "text" TEXT NULL DEFAULT NULL,
  "mode" CHAR(255) NULL DEFAULT 'comment',
  "status" CHAR(255) NULL DEFAULT 'approved',
  "parent" BIGINT NULL DEFAULT '0',
  PRIMARY KEY ("coid")
); 
CREATE INDEX "typecho_comments_cid" ON "typecho_comments" ("cid");
CREATE INDEX "typecho_comments_created" ON "typecho_comments" ("created");

INSERT INTO "typecho_comments" ("coid", "cid", "created", "author", "mail", "url", "ip", "agent", "text", "mode", "status", "parent") VALUES(1, 1, 1211300209, 'Typecho', NULL, 'http://www.typecho.org', '127.0.0.1', 'Typecho 0.2/8.7.6', '欢迎加入Typecho大家族', 'comment', 'approved', 0);

SELECT setval('typecho_comments_coid_seq', (SELECT max("coid") FROM "typecho_comments"));


--
-- Table structure for table `typecho_contents`
--

CREATE SEQUENCE "typecho_contents_cid_seq";

CREATE TABLE "typecho_contents" (  "cid" BIGINT NOT NULL DEFAULT nextval('typecho_contents_cid_seq'),
  "title" VARCHAR(200) NULL DEFAULT NULL,
  "slug" VARCHAR(128) NULL DEFAULT NULL,
  "uri" VARCHAR(200) NULL DEFAULT NULL,
  "created" BIGINT NULL DEFAULT '0',
  "modified" BIGINT NULL DEFAULT '0',
  "text" TEXT NULL DEFAULT NULL,
  "meta" BIGINT NULL DEFAULT '0',
  "author" BIGINT NULL DEFAULT '0',
  "template" VARCHAR(32) NULL DEFAULT NULL,
  "type" VARCHAR(32) NULL DEFAULT NULL,
  "password" VARCHAR(32) NULL DEFAULT NULL,
  "commentsNum" BIGINT NULL DEFAULT '0',
  "allowComment" CHAR(255) NULL DEFAULT 'disable',
  "allowPing" CHAR(255) NULL DEFAULT 'disable',
  "allowFeed" CHAR(255) NULL DEFAULT 'disable',
  PRIMARY KEY ("cid")
); 
CREATE INDEX "typecho_contents_slug" ON "typecho_contents" ("slug");
CREATE INDEX "typecho_contents_created" ON "typecho_contents" ("created");
CREATE INDEX "typecho_contents_author" ON "typecho_contents" ("author");

INSERT INTO "typecho_contents" ("cid", "title", "slug", "uri", "created", "modified", "text", "meta", "author", "template", "type", "password", "commentsNum", "allowComment", "allowPing", "allowFeed") VALUES(1, '欢迎使用Typecho', 'start', NULL, 1211300160, 1215644540, '<p>如果您看到这篇文章,表示您的blog已经安装成功.</p>', 0, 1, NULL, 'post', NULL, 1, 'enable', 'enable', 'enable');
INSERT INTO "typecho_contents" ("cid", "title", "slug", "uri", "created", "modified", "text", "meta", "author", "template", "type", "password", "commentsNum", "allowComment", "allowPing", "allowFeed") VALUES(2, '欢迎使用Typecho', 'start', NULL, 1211300209, 1211300209, '<p>这只是个测试页面.</p>', 1, 1, NULL, 'page', NULL, 0, 'enable', 'enable', 'enable');

SELECT setval('typecho_contents_cid_seq', (SELECT max("cid") FROM "typecho_contents"));


--
-- Table structure for table `typecho_metas`
--

CREATE SEQUENCE "typecho_metas_mid_seq";

CREATE TABLE "typecho_metas" (  "mid" BIGINT NOT NULL DEFAULT nextval('typecho_metas_mid_seq'),
  "name" VARCHAR(200) NULL DEFAULT NULL,
  "slug" VARCHAR(128) NULL DEFAULT NULL,
  "type" VARCHAR(32) NOT NULL DEFAULT '',
  "description" VARCHAR(200) NULL DEFAULT NULL,
  "count" BIGINT NULL DEFAULT '0',
  "sort" BIGINT NULL DEFAULT '0',
  PRIMARY KEY ("mid")
); 
CREATE INDEX "typecho_metas_slug" ON "typecho_metas" ("slug");

INSERT INTO "typecho_metas" ("mid", "name", "slug", "type", "description", "count", "sort") VALUES(1, '默认分类', 'default', 'category', '只是一个默认分类', 1, 1);
INSERT INTO "typecho_metas" ("mid", "name", "slug", "type", "description", "count", "sort") VALUES(2, 'Typecho官方网站', 'http://www.typecho.org', 'link', 'Typecho的老巢', 0, 1);

SELECT setval('typecho_metas_mid_seq', (SELECT max("mid") FROM "typecho_metas"));


--
-- Table structure for table `typecho_options`
--

CREATE TABLE "typecho_options" (  "name" VARCHAR(32) NOT NULL DEFAULT '',
  "user" BIGINT NOT NULL DEFAULT '0',
  "value" TEXT NULL DEFAULT NULL,
  PRIMARY KEY ("name","user")
); 

INSERT INTO "typecho_options" ("name", "user", "value") VALUES('theme', 0, 'default');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('timezone', 0, '28800');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('charset', 0, 'UTF-8');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('generator', 0, 'Typecho 0.2/8.7.6');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('title', 0, 'Hello World');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('description', 0, 'Just So So ...');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('keywords', 0, 'typecho,php,blog');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('rewrite', 0, '0');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('commentsRequireMail', 0, '1');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('commentsRequireURL', 0, '0');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('attachmentExtensions', 0, 'zip|rar|jpg|png|gif|txt');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('commentsRequireModeration', 0, '0');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('plugins', 0, 'a:0:{}');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('commentDateFormat', 0, 'Y-m-d H:i:s');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('siteUrl', 0, 'http://localhost/typecho');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('defaultCategory', 0, '1');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('allowRegister', 0, '0');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('defaultAllowComment', 0, '1');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('defaultAllowPing', 0, '1');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('defaultAllowFeed', 0, '1');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('pageSize', 0, '5');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('postsListSize', 0, '10');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('commentsListSize', 0, '10');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('commentsHTMLTagAllowed', 0, NULL);
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('postDateFormat', 0, 'Y-m-d');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('feedFullArticlesLayout', 0, '1');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('editorSize', 0, '16');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('autoSave', 0, '0');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('commentsPostTimeout', 0, '0');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('commentsUrlNofollow', 0, '1');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('commentsShowUrl', 0, '1');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('commentsUniqueIpInterval', 0, '0');
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('commentsStopWords', 0, NULL);
INSERT INTO "typecho_options" ("name", "user", "value") VALUES('commentsIpBlackList', 0, NULL);


--
-- Table structure for table `typecho_relationships`
--

CREATE TABLE "typecho_relationships" (  "cid" BIGINT NOT NULL DEFAULT '0',
  "mid" BIGINT NOT NULL DEFAULT '0',
  PRIMARY KEY ("cid","mid")
); 

INSERT INTO "typecho_relationships" ("cid", "mid") VALUES(1, 1);

--
-- Table structure for table `typecho_users`
--
CREATE SEQUENCE "typecho_users_uid_seq";

CREATE TABLE "typecho_users" (  "uid" BIGINT NOT NULL DEFAULT nextval('typecho_users_uid_seq') ,
  "name" VARCHAR(32) NULL DEFAULT NULL,
  "password" VARCHAR(32) NULL DEFAULT NULL,
  "mail" VARCHAR(200) NULL DEFAULT NULL,
  "url" VARCHAR(200) NULL DEFAULT NULL,
  "screenName" VARCHAR(32) NULL DEFAULT NULL,
  "created" BIGINT NULL DEFAULT '0',
  "activated" BIGINT NULL DEFAULT '0',
  "logged" BIGINT NULL DEFAULT '0',
  "group" CHAR(255) NULL DEFAULT 'visitor',
  "authCode" VARCHAR(40) NULL DEFAULT NULL,
  PRIMARY KEY ("uid"),
  UNIQUE ("name"),
  UNIQUE ("mail")
);

INSERT INTO "typecho_users" ("uid", "name", "password", "mail", "url", "screenName", "created", "activated", "logged", "group", "authCode") VALUES(1, 'admin', 'ef809aa7f1b9ca401071589dec6cd413', 'example@yourdomain.com', 'http://www.typecho.org', 'admin', 1215630648, 1216001513, 1215992826, 'administrator', '13d3b33a93f16bcc2572aceb0ecdb9bda6b11745');

SELECT setval('typecho_users_uid_seq', (SELECT max("uid") FROM "typecho_users"));

