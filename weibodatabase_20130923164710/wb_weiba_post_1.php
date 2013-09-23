<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_weiba_post`;");
E_C("CREATE TABLE `wb_weiba_post` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '帖子ID',
  `weiba_id` int(11) NOT NULL COMMENT '所属微吧ID',
  `post_uid` int(11) NOT NULL COMMENT '发表者uid',
  `title` varchar(255) NOT NULL COMMENT '帖子标题',
  `content` text NOT NULL COMMENT '帖子内容',
  `post_time` int(11) NOT NULL COMMENT '发表时间',
  `reply_count` int(10) DEFAULT '0' COMMENT '回复数',
  `read_count` int(10) DEFAULT '0' COMMENT '浏览数',
  `last_reply_uid` int(11) DEFAULT '0' COMMENT '最后回复人',
  `last_reply_time` int(11) DEFAULT '0' COMMENT '最后回复时间',
  `digest` tinyint(1) DEFAULT '0' COMMENT '全局精华 0-否 1-是',
  `top` tinyint(1) DEFAULT '0' COMMENT '置顶帖 0-否 1-吧内 2-全局',
  `lock` tinyint(1) DEFAULT '0' COMMENT '锁帖（不允许回复）0-否 1-是',
  `recommend` tinyint(1) DEFAULT '0' COMMENT '是否设为推荐',
  `recommend_time` int(11) DEFAULT '0' COMMENT '设为推荐的时间',
  `is_del` tinyint(2) DEFAULT '0' COMMENT '是否已删除 0-否 1-是',
  `feed_id` int(11) NOT NULL COMMENT '对应的微博ID',
  `reply_all_count` int(11) NOT NULL DEFAULT '0' COMMENT '全部评论数目',
  PRIMARY KEY (`post_id`),
  KEY `id_recommend` (`recommend_time`,`weiba_id`,`recommend`),
  KEY `post_time` (`post_time`,`weiba_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>