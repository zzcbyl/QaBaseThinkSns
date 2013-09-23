<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_credit_user`;");
E_C("CREATE TABLE `wb_credit_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户UID',
  `score` int(11) DEFAULT NULL COMMENT '积分总值',
  `experience` int(11) DEFAULT NULL COMMENT '经验总值',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8");
E_D("replace into `wb_credit_user` values('1','1','16','8');");
E_D("replace into `wb_credit_user` values('2','2','219','17');");
E_D("replace into `wb_credit_user` values('3','3','346','143');");

require("../../inc/footer.php");
?>