<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_app`;");
E_C("CREATE TABLE `wb_user_app` (
  `user_app_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '键主ID',
  `app_id` int(11) NOT NULL COMMENT '用应ID',
  `uid` int(11) NOT NULL COMMENT '安装者UID',
  `display_order` int(5) NOT NULL DEFAULT '0' COMMENT '装安的应用排序',
  `ctime` int(11) DEFAULT NULL COMMENT '装安时间戳',
  `type` varchar(100) DEFAULT NULL COMMENT '用应分类',
  `oauth_token` varchar(255) DEFAULT NULL COMMENT 'API的口令',
  `oauth_token_secret` varchar(255) DEFAULT NULL COMMENT 'API的密钥',
  `inweb` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否网页端，1是，0不是',
  PRIMARY KEY (`user_app_id`),
  KEY `app_id` (`app_id`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>