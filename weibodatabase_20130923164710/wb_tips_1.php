<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_tips`;");
E_C("CREATE TABLE `wb_tips` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `source_id` int(10) NOT NULL COMMENT '资源ID',
  `source_table` varchar(20) NOT NULL COMMENT '资源所在表',
  `uid` int(10) NOT NULL COMMENT '用户ID',
  `type` tinyint(2) NOT NULL COMMENT '类型（0表示支持。1表示反对）',
  `ctime` int(11) NOT NULL COMMENT '添加时间',
  `ip` varchar(20) NOT NULL COMMENT '操作者IP',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>