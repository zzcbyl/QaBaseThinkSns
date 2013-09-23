<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_data`;");
E_C("CREATE TABLE `wb_user_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(11) NOT NULL COMMENT '户用UID',
  `key` varchar(50) NOT NULL COMMENT 'Key',
  `value` text COMMENT '对应Key的 值',
  `mtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '前当时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user-key` (`uid`,`key`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=utf8");
E_D("replace into `wb_user_data` values('1','3','following_count','1','2013-09-22 15:03:37');");
E_D("replace into `wb_user_data` values('2','2','follower_count','1','2013-09-22 15:03:37');");
E_D("replace into `wb_user_data` values('4','2','new_folower_count','0','2013-09-22 15:03:53');");
E_D("replace into `wb_user_data` values('5','2','following_count','1','2013-09-22 15:03:55');");
E_D("replace into `wb_user_data` values('6','3','follower_count','1','2013-09-22 15:03:55');");
E_D("replace into `wb_user_data` values('16','3','new_folower_count','0','2013-09-22 15:04:39');");
E_D("replace into `wb_user_data` values('12','2','feed_count','3','2013-09-22 15:04:07');");
E_D("replace into `wb_user_data` values('13','2','weibo_count','3','2013-09-22 15:04:07');");
E_D("replace into `wb_user_data` values('69','3','feed_count','28','2013-09-23 16:27:56');");
E_D("replace into `wb_user_data` values('70','3','weibo_count','28','2013-09-23 16:27:56');");

require("../../inc/footer.php");
?>