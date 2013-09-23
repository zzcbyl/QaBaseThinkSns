<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_official`;");
E_C("CREATE TABLE `wb_user_official` (
  `official_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `user_official_category_id` int(11) NOT NULL COMMENT '官方分类ID',
  `info` varchar(255) DEFAULT NULL COMMENT '官方用户信息',
  PRIMARY KEY (`official_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>