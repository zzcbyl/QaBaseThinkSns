<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_credit_type`;");
E_C("CREATE TABLE `wb_credit_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '积分名',
  `alias` varchar(50) NOT NULL COMMENT '积分中文名',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8");
E_D("replace into `wb_credit_type` values('1','experience','经验');");
E_D("replace into `wb_credit_type` values('6','score','财富');");

require("../../inc/footer.php");
?>