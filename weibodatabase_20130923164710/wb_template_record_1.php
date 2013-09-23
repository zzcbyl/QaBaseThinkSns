<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_template_record`;");
E_C("CREATE TABLE `wb_template_record` (
  `tpl_record_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '模板使用者UID',
  `tpl_name` varchar(255) NOT NULL DEFAULT '' COMMENT '模板名',
  `tpl_alias` varchar(255) DEFAULT NULL COMMENT '模板别名',
  `type` varchar(255) DEFAULT NULL COMMENT '模板类型',
  `type2` varchar(255) DEFAULT NULL COMMENT '模板类型2',
  `data` text COMMENT '记录模板变量数据',
  `ctime` int(11) DEFAULT NULL COMMENT '模板调用时间戳',
  PRIMARY KEY (`tpl_record_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>