<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_online`;");
E_C("CREATE TABLE `wb_online` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户UID',
  `uname` varchar(50) NOT NULL COMMENT '用户名',
  `app` char(20) DEFAULT NULL COMMENT '应用',
  `ip` varchar(20) DEFAULT NULL COMMENT 'IP地址',
  `agent` char(20) DEFAULT NULL COMMENT '访问的浏览器',
  `activeTime` int(11) DEFAULT NULL COMMENT '访问时间',
  PRIMARY KEY (`id`),
  KEY `active_time` (`activeTime`),
  KEY `uid_ip` (`uid`,`ip`),
  KEY `uid_activeTime` (`uid`,`activeTime`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8");
E_D("replace into `wb_online` values('1','0','guest','public','::1','Firefox','1379813848');");
E_D("replace into `wb_online` values('2','1','管理员','public','::1','Firefox','1379833309');");
E_D("replace into `wb_online` values('3','3','ceshi1','public','::1','Firefox','1379923833');");

require("../../inc/footer.php");
?>