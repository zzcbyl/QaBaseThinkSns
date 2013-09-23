<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_login_record`;");
E_C("CREATE TABLE `wb_login_record` (
  `login_record_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户UID',
  `ip` varchar(15) DEFAULT NULL COMMENT 'IP',
  `place` varchar(255) DEFAULT NULL COMMENT '地点',
  `ctime` int(11) DEFAULT NULL COMMENT '时间',
  `locktime` int(11) NOT NULL COMMENT '账号锁定截至日期',
  PRIMARY KEY (`login_record_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8");
E_D("replace into `wb_login_record` values('1','1','0.0.0.0',NULL,'1379813905','0');");
E_D("replace into `wb_login_record` values('2','2','0.0.0.0',NULL,'1379833430','0');");
E_D("replace into `wb_login_record` values('3','3','0.0.0.0',NULL,'1379898248','0');");

require("../../inc/footer.php");
?>