<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_blacklist`;");
E_C("CREATE TABLE `wb_user_blacklist` (
  `uid` int(11) NOT NULL COMMENT '户用UID',
  `fid` int(11) NOT NULL COMMENT '被屏蔽的用户UID',
  `ctime` int(11) NOT NULL COMMENT '操作时间戳',
  UNIQUE KEY `uid` (`uid`,`fid`),
  KEY `fid` (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>