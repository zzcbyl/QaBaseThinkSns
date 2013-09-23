<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_weiba`;");
E_C("CREATE TABLE `wb_weiba` (
  `weiba_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '微吧ID',
  `weiba_name` varchar(255) NOT NULL DEFAULT '微吧名称',
  `uid` int(11) NOT NULL COMMENT '创建者ID',
  `ctime` int(11) NOT NULL COMMENT '创建时间',
  `logo` varchar(255) DEFAULT NULL COMMENT '微吧logo',
  `intro` text COMMENT '微吧简介',
  `who_can_post` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发帖权限 0-所有人 1-仅成员',
  `who_can_reply` tinyint(1) NOT NULL DEFAULT '0' COMMENT '回帖权限 0-所有人 1-仅成员',
  `follower_count` int(10) DEFAULT '0' COMMENT '成员数',
  `thread_count` int(10) DEFAULT '0' COMMENT '帖子数',
  `admin_uid` int(11) NOT NULL COMMENT '超级吧主uid',
  `recommend` tinyint(1) DEFAULT '0' COMMENT '是否设为推荐（热门）0-否，1-是',
  `status` tinyint(1) DEFAULT '0' COMMENT '是否通过审核：0-未通过，1-已通过',
  `is_del` int(2) DEFAULT '0' COMMENT '是否删除 默认为0',
  `notify` varchar(255) DEFAULT NULL COMMENT '微吧公告',
  PRIMARY KEY (`weiba_id`),
  KEY `recommend` (`recommend`,`is_del`),
  KEY `count` (`is_del`,`follower_count`,`thread_count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>