<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_login_logs`;");
E_C("CREATE TABLE `wb_login_logs` (
  `login_logs_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '登录日志ID - 主键',
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `ip` varchar(15) DEFAULT NULL COMMENT '登录IP',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`login_logs_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8");
E_D("replace into `wb_login_logs` values('1','1','0.0.0.0','1379476169');");
E_D("replace into `wb_login_logs` values('2','1','0.0.0.0','1379476180');");
E_D("replace into `wb_login_logs` values('3','1','0.0.0.0','1379476357');");
E_D("replace into `wb_login_logs` values('4','1','0.0.0.0','1379476364');");
E_D("replace into `wb_login_logs` values('5','1','0.0.0.0','1379813884');");
E_D("replace into `wb_login_logs` values('6','1','0.0.0.0','1379813884');");
E_D("replace into `wb_login_logs` values('7','1','0.0.0.0','1379813884');");
E_D("replace into `wb_login_logs` values('8','1','0.0.0.0','1379813905');");
E_D("replace into `wb_login_logs` values('9','2','0.0.0.0','1379833338');");
E_D("replace into `wb_login_logs` values('10','3','0.0.0.0','1379833389');");
E_D("replace into `wb_login_logs` values('11','2','0.0.0.0','1379833430');");
E_D("replace into `wb_login_logs` values('12','3','0.0.0.0','1379833474');");

require("../../inc/footer.php");
?>