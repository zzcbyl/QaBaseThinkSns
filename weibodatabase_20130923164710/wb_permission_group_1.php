<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_permission_group`;");
E_C("CREATE TABLE `wb_permission_group` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `appname` varchar(50) NOT NULL COMMENT '应用名称',
  `appgroup` varchar(50) NOT NULL COMMENT '应用组名称',
  `appgroup_name` varchar(50) NOT NULL COMMENT '组别名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8");
E_D("replace into `wb_permission_group` values('1','admin','admin','普通管理员');");
E_D("replace into `wb_permission_group` values('2','admin','superadmin','超级管理员');");

require("../../inc/footer.php");
?>