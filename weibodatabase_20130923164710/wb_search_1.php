<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_search`;");
E_C("CREATE TABLE `wb_search` (
  `doc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app` varchar(30) DEFAULT NULL COMMENT '应用名',
  `type` varchar(50) DEFAULT NULL COMMENT '搜索类型',
  `string01` varchar(255) DEFAULT NULL COMMENT '文本扩展字段',
  `string02` varchar(255) DEFAULT NULL COMMENT '文本扩展字段',
  `string03` varchar(255) DEFAULT NULL COMMENT '文本扩展字段',
  `string04` varchar(255) DEFAULT NULL COMMENT '文本扩展字段',
  `string05` varchar(255) DEFAULT NULL COMMENT '文本扩展字段',
  `int01` int(11) DEFAULT NULL COMMENT '数字扩展字段',
  `int02` int(11) DEFAULT NULL COMMENT '数字扩展字段',
  `int03` int(11) DEFAULT NULL COMMENT '数字扩展字段',
  `int04` int(11) DEFAULT NULL COMMENT '数字扩展字段',
  `int05` int(11) DEFAULT NULL COMMENT '数字扩展字段',
  `int06` int(11) NOT NULL COMMENT '数字扩展字段',
  `int07` int(11) NOT NULL COMMENT '数字扩展字段',
  `int08` int(11) NOT NULL COMMENT '数字扩展字段',
  `int09` int(11) NOT NULL COMMENT '数字扩展字段',
  `int10` int(11) NOT NULL COMMENT '数字扩展字段',
  `file_path` varchar(255) DEFAULT NULL COMMENT '附件路径',
  `content` text COMMENT '搜索内容',
  `mtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `data` text NOT NULL COMMENT '序列化存储的数据',
  PRIMARY KEY (`doc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>