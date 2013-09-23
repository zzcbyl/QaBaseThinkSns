<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_medal_user`;");
E_C("CREATE TABLE `wb_medal_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户UID',
  `medal_id` int(11) NOT NULL COMMENT '勋章ID',
  `desc` varchar(255) DEFAULT NULL COMMENT '勋章获得说明',
  `ctime` int(11) DEFAULT NULL COMMENT '获取勋章时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_medal_id` (`uid`,`medal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>