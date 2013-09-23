<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_feed`;");
E_C("CREATE TABLE `wb_feed` (
  `feed_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '动态ID',
  `uid` int(11) NOT NULL COMMENT '产生动态的用户UID',
  `type` char(50) DEFAULT NULL COMMENT 'feed类型.由发表feed的程序控制',
  `app` char(30) DEFAULT 'public' COMMENT 'feed来源的appname',
  `app_row_table` varchar(50) NOT NULL DEFAULT 'feed' COMMENT '关联资源所在的表',
  `app_row_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联的来源ID（如博客的id）',
  `publish_time` int(11) NOT NULL COMMENT '产生时间戳',
  `is_del` int(2) NOT NULL DEFAULT '0' COMMENT '是否删除 默认为0',
  `from` tinyint(2) NOT NULL DEFAULT '0' COMMENT '客户端类型，0：网站；1：手机网页版；2：android；3：iphone',
  `comment_count` int(10) DEFAULT '0' COMMENT '评论数',
  `repost_count` int(10) DEFAULT '0' COMMENT '分享数',
  `comment_all_count` int(10) DEFAULT '0' COMMENT '全部评论数目',
  `digg_count` int(11) DEFAULT '0',
  `is_repost` int(2) DEFAULT '0' COMMENT '是否转发 0-否  1-是',
  `is_audit` int(2) DEFAULT '1' COMMENT '是否已审核 0-未审核 1-已审核',
  PRIMARY KEY (`feed_id`),
  KEY `is_del` (`is_del`,`publish_time`),
  KEY `uid` (`uid`,`is_del`,`publish_time`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8");
E_D("replace into `wb_feed` values('1','2','post','public','feed','0','1379833441','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('2','2','post','public','feed','0','1379833444','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('3','2','post','public','feed','0','1379833447','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('4','3','post','public','feed','0','1379833478','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('5','3','post','public','feed','0','1379833483','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('6','3','post','public','feed','0','1379900815','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('7','3','post','public','feed','0','1379905929','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('8','3','post','public','feed','0','1379905942','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('9','3','post','public','feed','0','1379907442','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('10','3','post','public','feed','0','1379916828','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('11','3','post','public','feed','0','1379921810','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('12','3','post','public','feed','0','1379922022','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('13','3','post','public','feed','0','1379922029','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('14','3','post','public','feed','0','1379922036','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('15','3','post','public','feed','0','1379922424','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('16','3','post','public','feed','0','1379922609','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('17','3','post','public','feed','0','1379922642','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('18','3','post','public','feed','0','1379922696','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('19','3','post','public','feed','0','1379922707','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('20','3','post','public','feed','0','1379922990','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('21','3','post','public','feed','0','1379923039','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('22','3','post','public','feed','0','1379923158','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('23','3','post','public','feed','0','1379923321','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('24','3','post','public','feed','0','1379923342','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('25','3','post','public','feed','0','1379923461','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('26','3','post','public','feed','0','1379923500','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('27','3','post','public','feed','0','1379923526','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('28','3','post','public','feed','0','1379923620','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('29','3','post','public','feed','0','1379923836','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('30','3','post','public','feed','0','1379923844','0','0','0','0','0','0','0','1');");
E_D("replace into `wb_feed` values('31','3','post','public','feed','0','1379924876','0','0','0','0','0','0','0','1');");

require("../../inc/footer.php");
?>