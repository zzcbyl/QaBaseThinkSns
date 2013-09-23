<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_online_stats`;");
E_C("CREATE TABLE `wb_online_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day` date NOT NULL COMMENT '日期',
  `total_users` int(11) NOT NULL DEFAULT '0' COMMENT '总用户数',
  `total_guests` int(11) NOT NULL DEFAULT '0' COMMENT '总游客数',
  `total_pageviews` int(11) NOT NULL DEFAULT '0' COMMENT '页面访问次数',
  `most_online_users` int(11) NOT NULL DEFAULT '0' COMMENT '最多在线用户数',
  `most_online_guests` int(11) NOT NULL DEFAULT '0' COMMENT '最多游客在线数',
  `most_online_time` int(11) DEFAULT NULL COMMENT '最大在线时间',
  `most_online` int(11) NOT NULL COMMENT '最大在线人数',
  PRIMARY KEY (`id`),
  KEY `day` (`day`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8");
E_D("replace into `wb_online_stats` values('1','2013-09-18','1','9','15','0','0',NULL,'0');");
E_D("replace into `wb_online_stats` values('2','2013-09-22','1','1','2','0','1','1379813921','1');");

require("../../inc/footer.php");
?>