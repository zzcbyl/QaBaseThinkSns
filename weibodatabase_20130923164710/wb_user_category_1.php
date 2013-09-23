<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_category`;");
E_C("CREATE TABLE `wb_user_category` (
  `user_category_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户分类ID - 主键',
  `title` varchar(255) NOT NULL COMMENT '用户分类名称',
  `pid` int(11) NOT NULL COMMENT '父级ID',
  `sort` int(11) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`user_category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8");
E_D("replace into `wb_user_category` values('1','测试','0','1');");
E_D("replace into `wb_user_category` values('2','测试用户1','1','1');");
E_D("replace into `wb_user_category` values('3','测试用户2','1','2');");
E_D("replace into `wb_user_category` values('4','测试用户3','1','3');");

require("../../inc/footer.php");
?>