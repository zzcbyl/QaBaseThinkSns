<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_mobile_user`;");
E_C("CREATE TABLE `wb_mobile_user` (
  `uid` int(11) unsigned NOT NULL COMMENT '户用ID',
  `iphone_device_token` varchar(255) DEFAULT NULL COMMENT 'iPhone的机器码（用于推送消息）',
  `ipad_device_token` varchar(255) DEFAULT NULL COMMENT 'iPad的机器码（用于推送消息）',
  `android_device_token` varchar(255) DEFAULT NULL COMMENT 'Android的机器码（用于推送消息）',
  `iphone_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'iPhone否是开启推送',
  `ipad_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'iPad是开启推送',
  `android_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Android否是开启推送',
  `last_latitude` float(10,6) DEFAULT NULL COMMENT '经度',
  `last_longitude` float(10,6) DEFAULT NULL COMMENT '纬度',
  `last_checkin` int(11) unsigned DEFAULT NULL COMMENT '后最签到时间（访问即签到）',
  `nickname` varchar(255) DEFAULT NULL COMMENT '用户昵称，预留匿名功能',
  `infomation` varchar(255) DEFAULT NULL COMMENT '用户简介，预留',
  `checkin_count` int(11) unsigned DEFAULT '0' COMMENT '签到次数',
  `sex` tinyint(1) unsigned DEFAULT '1' COMMENT '性别：1男、2女',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>