<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_feedback_type`;");
E_C("CREATE TABLE `wb_feedback_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '反馈类型ID',
  `type_name` varchar(255) NOT NULL COMMENT '反馈类型名称',
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>