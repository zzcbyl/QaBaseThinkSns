<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_task_custom`;");
E_C("CREATE TABLE `wb_task_custom` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自定义任务ID',
  `task_name` varchar(255) DEFAULT NULL COMMENT '任务名称',
  `task_desc` varchar(255) DEFAULT NULL COMMENT '任务说明',
  `num` int(11) DEFAULT NULL COMMENT '剩余领取数',
  `condition` varchar(255) DEFAULT NULL COMMENT '任务完成条件',
  `task_condition` varchar(255) DEFAULT NULL COMMENT '前置任务',
  `reward` varchar(255) DEFAULT NULL COMMENT '任务奖励',
  `medal_id` int(11) DEFAULT NULL COMMENT '勋章ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>