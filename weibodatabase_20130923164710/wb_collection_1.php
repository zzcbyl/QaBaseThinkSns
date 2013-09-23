<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_collection`;");
E_C("CREATE TABLE `wb_collection` (
  `collection_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(5) NOT NULL COMMENT '用户ID',
  `source_id` int(11) NOT NULL COMMENT '资源ID\r\n',
  `source_table_name` varchar(255) NOT NULL COMMENT '资源所在表',
  `source_app` varchar(255) NOT NULL COMMENT '资源所在应用',
  `ctime` int(11) NOT NULL COMMENT '收藏时间',
  PRIMARY KEY (`collection_id`),
  UNIQUE KEY `cacheId` (`uid`,`source_id`,`source_table_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>