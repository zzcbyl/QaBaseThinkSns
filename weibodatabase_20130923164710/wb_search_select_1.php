<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_search_select`;");
E_C("CREATE TABLE `wb_search_select` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(20) NOT NULL COMMENT '应用名称',
  `app_id` tinyint(3) NOT NULL COMMENT '应用ID',
  `type` varchar(20) NOT NULL COMMENT '类型名称',
  `type_id` tinyint(3) NOT NULL COMMENT '类型在应用内定义的ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8");
E_D("replace into `wb_search_select` values('1','public','0','用户','1');");
E_D("replace into `wb_search_select` values('2','public','0','微博','2');");

require("../../inc/footer.php");
?>