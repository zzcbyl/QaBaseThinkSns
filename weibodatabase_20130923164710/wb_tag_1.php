<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_tag`;");
E_C("CREATE TABLE `wb_tag` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，标签编号',
  `name` varchar(255) NOT NULL COMMENT '标签名',
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tag_name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8");
E_D("replace into `wb_tag` values('1','测试用户2');");
E_D("replace into `wb_tag` values('2','测试用户1');");

require("../../inc/footer.php");
?>