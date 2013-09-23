<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_department`;");
E_C("CREATE TABLE `wb_department` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '部门名',
  `parent_dept_id` int(11) NOT NULL DEFAULT '0' COMMENT '上级部门的ID',
  `display_order` int(11) NOT NULL DEFAULT '0' COMMENT '在同级部门中的排序',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`department_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>