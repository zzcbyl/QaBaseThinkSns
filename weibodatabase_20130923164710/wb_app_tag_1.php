<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_app_tag`;");
E_C("CREATE TABLE `wb_app_tag` (
  `app` char(15) NOT NULL COMMENT '所属应用',
  `table` char(15) NOT NULL COMMENT '所属表名',
  `row_id` int(11) DEFAULT '0' COMMENT '所属应用的内容的编号或者用户编号',
  `tag_id` int(11) NOT NULL COMMENT 'Tag 编号',
  UNIQUE KEY `app` (`table`,`row_id`,`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
E_D("replace into `wb_app_tag` values('public','user','3','1');");
E_D("replace into `wb_app_tag` values('public','user','3','2');");

require("../../inc/footer.php");
?>