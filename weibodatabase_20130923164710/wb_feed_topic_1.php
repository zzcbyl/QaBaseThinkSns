<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_feed_topic`;");
E_C("CREATE TABLE `wb_feed_topic` (
  `topic_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '话题ID',
  `topic_name` varchar(150) NOT NULL COMMENT '话题标题',
  `count` int(11) NOT NULL COMMENT '关联的动态数',
  `ctime` int(11) NOT NULL COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否锁定',
  `domain` varchar(100) NOT NULL COMMENT '个性化地址',
  `recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否推荐',
  `recommend_time` int(11) DEFAULT '0' COMMENT '推荐时间',
  `des` text COMMENT '详细内容',
  `outlink` varchar(100) DEFAULT NULL COMMENT '关联链接',
  `pic` varchar(255) DEFAULT NULL COMMENT '关联图片',
  `essence` tinyint(1) DEFAULT '0' COMMENT '是否精华',
  `note` varchar(255) DEFAULT NULL COMMENT '摘要',
  `topic_user` varchar(255) DEFAULT NULL COMMENT '话题人物推荐',
  PRIMARY KEY (`topic_id`),
  KEY `count` (`count`),
  KEY `name` (`topic_name`,`count`),
  KEY `recommend` (`recommend`,`lock`,`count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>