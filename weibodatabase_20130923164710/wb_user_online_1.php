<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_online`;");
E_C("CREATE TABLE `wb_user_online` (
  `uid` int(11) NOT NULL COMMENT '户用UID',
  `ctime` int(11) NOT NULL COMMENT '最后一次操作动作时间戳，与当前时间相隔五分钟之内为在线',
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>