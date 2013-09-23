<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_credit_history`;");
E_C("CREATE TABLE `wb_user_credit_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '键主ID',
  `uid` int(11) NOT NULL COMMENT '操作用户UID',
  `info` varchar(255) DEFAULT NULL COMMENT '动作描述',
  `action` char(30) DEFAULT NULL COMMENT '作动',
  `type` char(10) NOT NULL DEFAULT 'credit' COMMENT '类型:（experience:经验 gold:财富）',
  `credit` mediumint(3) NOT NULL DEFAULT '0' COMMENT '富财或者经验的曾减值',
  `mtime` int(11) NOT NULL COMMENT '操作时间戳',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>