<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_atme`;");
E_C("CREATE TABLE `wb_atme` (
  `atme_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，@我的编号',
  `app` char(15) NOT NULL COMMENT '所属应用',
  `table` char(15) NOT NULL COMMENT '存储应用内容的表名',
  `row_id` int(11) NOT NULL COMMENT '应用含有@的内容的编号',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '被@的用户的编号',
  PRIMARY KEY (`atme_id`),
  KEY `app_2` (`uid`,`table`),
  KEY `app_3` (`table`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>