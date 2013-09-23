<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_group_link`;");
E_C("CREATE TABLE `wb_user_group_link` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(10) NOT NULL COMMENT '户用UID',
  `user_group_id` int(10) NOT NULL COMMENT '户用组ID',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `user_group_id` (`user_group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8");
E_D("replace into `wb_user_group_link` values('1','1','1');");
E_D("replace into `wb_user_group_link` values('2','2','3');");
E_D("replace into `wb_user_group_link` values('3','3','3');");

require("../../inc/footer.php");
?>