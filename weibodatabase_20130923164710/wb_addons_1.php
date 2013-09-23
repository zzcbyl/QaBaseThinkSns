<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_addons`;");
E_C("CREATE TABLE `wb_addons` (
  `addonId` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '插件id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '插件文件夹名',
  `pluginName` varchar(255) NOT NULL DEFAULT '' COMMENT '插件在后台显示的名字',
  `author` varchar(255) NOT NULL DEFAULT '' COMMENT '插件作者',
  `info` tinytext COMMENT '插件信息',
  `version` varchar(50) DEFAULT NULL COMMENT '插件版本',
  `status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '插件状态。0为未启用，1为启用',
  `lastupdate` varchar(255) DEFAULT '' COMMENT '最后更新时间',
  `site` varchar(255) DEFAULT NULL COMMENT '插件作者的网站',
  `tsVersion` varchar(11) NOT NULL DEFAULT '2.5' COMMENT '依赖ts的版本。预留。必填',
  PRIMARY KEY (`addonId`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8");
E_D("replace into `wb_addons` values('1','SpaceStyle','空间换肤 - 官方优化版','智士软件','用户自定义风格官方优化版','2.0','1','','','3.0');");
E_D("replace into `wb_addons` values('2','Weather','天气预报','程序_小时代','天气预报，根据IP获取该城市3天内天气信息','2.0','0','','','3.0');");
E_D("replace into `wb_addons` values('3','RelatedUser','可能感兴趣的人','t3','根据当前用户推荐可能感兴趣的人','3.0','0','','','3.0');");
E_D("replace into `wb_addons` values('4','Login','微博同步V3','智士软件','第三方账号登录插件','3.0','0','','','3.0');");

require("../../inc/footer.php");
?>