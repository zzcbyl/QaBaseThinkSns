<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_diy_widget`;");
E_C("CREATE TABLE `wb_diy_widget` (
  `widgetId` int(11) NOT NULL AUTO_INCREMENT COMMENT '模块ID',
  `uid` int(11) NOT NULL COMMENT '用户UID',
  `pluginId` varchar(255) NOT NULL COMMENT 'DIY中关联的ID',
  `pageId` int(11) NOT NULL COMMENT '页面ID',
  `channelId` int(11) NOT NULL COMMENT '频道ID',
  `taglib` text COMMENT '标签栏内容',
  `content` text COMMENT '模块内容',
  `ext` text COMMENT '模块参数',
  `cache` text COMMENT '缓存内容',
  `cacheTime` int(11) NOT NULL DEFAULT '0' COMMENT '缓存时间',
  `cTime` int(11) DEFAULT NULL COMMENT '创建时间',
  `mTime` int(11) DEFAULT NULL COMMENT '修改时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`widgetId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>