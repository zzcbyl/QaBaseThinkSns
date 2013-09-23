<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_feedback`;");
E_C("CREATE TABLE `wb_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feedbacktype` int(11) DEFAULT NULL COMMENT '反馈类型',
  `feedback` varchar(255) DEFAULT NULL COMMENT '反馈内容',
  `uid` int(11) DEFAULT NULL COMMENT '用户UID',
  `cTime` int(11) DEFAULT NULL COMMENT '创建时间',
  `mTime` int(11) DEFAULT NULL COMMENT '修改时间',
  `type` int(1) DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>