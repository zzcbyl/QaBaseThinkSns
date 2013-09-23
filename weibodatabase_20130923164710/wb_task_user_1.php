<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_task_user`;");
E_C("CREATE TABLE `wb_task_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户UID',
  `tid` int(11) DEFAULT NULL COMMENT '任务ID',
  `task_level` int(11) DEFAULT NULL COMMENT '任务等级',
  `task_type` varchar(255) DEFAULT NULL COMMENT '任务类型',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  `status` int(11) DEFAULT NULL COMMENT '完成状态',
  `desc` varchar(255) DEFAULT NULL COMMENT '说明',
  `receive` int(11) DEFAULT '0' COMMENT '是否领取奖励',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>