<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_verified_category`;");
E_C("CREATE TABLE `wb_user_verified_category` (
  `user_verified_category_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '认证分类主键',
  `title` varchar(225) NOT NULL COMMENT '认证分类名称',
  `pid` int(11) NOT NULL COMMENT '父分类ID',
  `sort` int(11) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`user_verified_category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8");
E_D("replace into `wb_user_verified_category` values('1','认证分类1','5','1');");
E_D("replace into `wb_user_verified_category` values('2','认证分类2','6','2');");
E_D("replace into `wb_user_verified_category` values('3','认证分类3','7','3');");

require("../../inc/footer.php");
?>