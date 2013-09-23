<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_task_receive`;");
E_C("CREATE TABLE `wb_task_receive` (
  `task_level` int(11) DEFAULT NULL COMMENT '任务等级',
  `task_type` int(11) DEFAULT NULL COMMENT '任务类型：每日任务 ，新手任务等等',
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>