<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_department`;");
E_C("CREATE TABLE `wb_user_department` (
  `uid` int(10) NOT NULL COMMENT '用户ID',
  `department_id` int(10) NOT NULL COMMENT '部门ID',
  KEY `department_id` (`department_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>