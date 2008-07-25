--
-- Table structure for table `typecho_comments`
--
CREATE SEQUENCE "typecho_comments_coid_seq";

CREATE TABLE "typecho_comments" (  "coid" INT NOT NULL DEFAULT nextval('typecho_comments_coid_seq'),
  "cid" INT NULL DEFAULT '0',
  "created" INT NULL DEFAULT '0',
  "author" VARCHAR(200) NULL DEFAULT NULL,
  "mail" VARCHAR(200) NULL DEFAULT NULL,
  "url" VARCHAR(200) NULL DEFAULT NULL,
  "ip" VARCHAR(64) NULL DEFAULT NULL,
  "agent" VARCHAR(200) NULL DEFAULT NULL,
  "text" TEXT NULL DEFAULT NULL,
  "mode" VARCHAR(16) NULL DEFAULT 'comment',
  "status" VARCHAR(16) NULL DEFAULT 'approved',
  "parent" INT NULL DEFAULT '0',
  PRIMARY KEY ("coid")
); 
CREATE INDEX "typecho_comments_cid" ON "typecho_comments" ("cid");
CREATE INDEX "typecho_comments_created" ON "typecho_comments" ("created");

SELECT setval('typecho_comments_coid_seq', (SELECT max("coid") FROM "typecho_comments"));


--
-- Table structure for table `typecho_contents`
--

CREATE SEQUENCE "typecho_contents_cid_seq";

CREATE TABLE "typecho_contents" (  "cid" INT NOT NULL DEFAULT nextval('typecho_contents_cid_seq'),
  "title" VARCHAR(200) NULL DEFAULT NULL,
  "slug" VARCHAR(128) NULL DEFAULT NULL,
  "uri" VARCHAR(200) NULL DEFAULT NULL,
  "created" INT NULL DEFAULT '0',
  "modified" INT NULL DEFAULT '0',
  "text" TEXT NULL DEFAULT NULL,
  "meta" INT NULL DEFAULT '0',
  "author" INT NULL DEFAULT '0',
  "template" VARCHAR(32) NULL DEFAULT NULL,
  "type" VARCHAR(32) NULL DEFAULT NULL,
  "password" VARCHAR(32) NULL DEFAULT NULL,
  "commentsNum" INT NULL DEFAULT '0',
  "allowComment" VARCHAR(16) NULL DEFAULT 'disable',
  "allowPing" VARCHAR(16) NULL DEFAULT 'disable',
  "allowFeed" VARCHAR(16) NULL DEFAULT 'disable',
  PRIMARY KEY ("cid")
); 
CREATE INDEX "typecho_contents_slug" ON "typecho_contents" ("slug");
CREATE INDEX "typecho_contents_created" ON "typecho_contents" ("created");
CREATE INDEX "typecho_contents_author" ON "typecho_contents" ("author");

SELECT setval('typecho_contents_cid_seq', (SELECT max("cid") FROM "typecho_contents"));


--
-- Table structure for table `typecho_metas`
--

CREATE SEQUENCE "typecho_metas_mid_seq";

CREATE TABLE "typecho_metas" (  "mid" INT NOT NULL DEFAULT nextval('typecho_metas_mid_seq'),
  "name" VARCHAR(200) NULL DEFAULT NULL,
  "slug" VARCHAR(128) NULL DEFAULT NULL,
  "type" VARCHAR(16) NOT NULL DEFAULT '',
  "description" VARCHAR(200) NULL DEFAULT NULL,
  "count" INT NULL DEFAULT '0',
  "sort" INT NULL DEFAULT '0',
  PRIMARY KEY ("mid")
); 
CREATE INDEX "typecho_metas_slug" ON "typecho_metas" ("slug");

SELECT setval('typecho_metas_mid_seq', (SELECT max("mid") FROM "typecho_metas"));


--
-- Table structure for table `typecho_options`
--

CREATE TABLE "typecho_options" (  "name" VARCHAR(32) NOT NULL DEFAULT '',
  "user" INT NOT NULL DEFAULT '0',
  "value" TEXT NULL DEFAULT NULL,
  PRIMARY KEY ("name","user")
);

--
-- Table structure for table `typecho_relationships`
--

CREATE TABLE "typecho_relationships" (  "cid" INT NOT NULL DEFAULT '0',
  "mid" INT NOT NULL DEFAULT '0',
  PRIMARY KEY ("cid","mid")
); 

--
-- Table structure for table `typecho_users`
--
CREATE SEQUENCE "typecho_users_uid_seq";

CREATE TABLE "typecho_users" (  "uid" INT NOT NULL DEFAULT nextval('typecho_users_uid_seq') ,
  "name" VARCHAR(32) NULL DEFAULT NULL,
  "password" VARCHAR(32) NULL DEFAULT NULL,
  "mail" VARCHAR(200) NULL DEFAULT NULL,
  "url" VARCHAR(200) NULL DEFAULT NULL,
  "screenName" VARCHAR(32) NULL DEFAULT NULL,
  "created" INT NULL DEFAULT '0',
  "activated" INT NULL DEFAULT '0',
  "logged" INT NULL DEFAULT '0',
  "group" VARCHAR(16) NULL DEFAULT 'visitor',
  "authCode" VARCHAR(40) NULL DEFAULT NULL,
  PRIMARY KEY ("uid"),
  UNIQUE ("name"),
  UNIQUE ("mail")
);

SELECT setval('typecho_users_uid_seq', (SELECT max("uid") FROM "typecho_users"));

