<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_official_category`;");
E_C("CREATE TABLE `wb_user_official_category` (
  `user_official_category_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '官方用户分类',
  `title` varchar(255) NOT NULL COMMENT '官方用户分类名称',
  `pid` int(11) NOT NULL COMMENT '父级分类ID',
  `sort` int(11) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`user_official_category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8");
E_D("replace into `wb_user_official_category` values('1','官方分类1','0','1');");
E_D("replace into `wb_user_official_category` values('2','官方分类2','0','2');");
E_D("replace into `wb_user_official_category` values('3','官方分类3','0','3');");

require("../../inc/footer.php");
?>