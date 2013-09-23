<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_search_key`;");
E_C("CREATE TABLE `wb_search_key` (
  `kid` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL COMMENT '搜索的关键词',
  `searchCount` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '搜索次数',
  `resultCount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结果数',
  `suggest` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示在建议列表中',
  `data` text COMMENT '扩展字段',
  `mtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`kid`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>