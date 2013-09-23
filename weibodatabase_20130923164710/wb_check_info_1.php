<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_check_info`;");
E_C("CREATE TABLE `wb_check_info` (
  `uid` int(11) DEFAULT NULL COMMENT '用户UID',
  `con_num` int(11) DEFAULT '1' COMMENT '连续签到次数',
  `total_num` int(11) DEFAULT '1' COMMENT '总签到次数',
  `ctime` int(11) DEFAULT '0' COMMENT '签到时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>