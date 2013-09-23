<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user`;");
E_C("CREATE TABLE `wb_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键UID',
  `login` varchar(255) DEFAULT NULL COMMENT '登录emial',
  `password` varchar(255) DEFAULT NULL COMMENT '用户密码的md5摘要',
  `login_salt` char(5) DEFAULT NULL COMMENT '10000 到 99999之间的随机数，加密密码时使用',
  `uname` varchar(255) DEFAULT NULL COMMENT '用户名',
  `email` varchar(255) DEFAULT NULL COMMENT '用户email',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别 1：男、2：女',
  `location` varchar(255) DEFAULT NULL COMMENT '所在省市的字符串',
  `is_audit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否通过审核：0-未通过，1-已通过',
  `is_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已激活 1：激活、0：未激活',
  `is_init` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否初始化用户资料 1：初始化、0：未初始化',
  `ctime` int(11) DEFAULT NULL COMMENT '注册时间',
  `identity` tinyint(1) NOT NULL DEFAULT '1' COMMENT '身份标识（1：用户，2：组织）',
  `api_key` varchar(255) DEFAULT NULL COMMENT '用户的api_key用于移动端',
  `domain` char(80) NOT NULL COMMENT '保留字段，用于用户分表',
  `province` mediumint(6) NOT NULL DEFAULT '0' COMMENT '省ID、关联ts_area表',
  `city` int(5) NOT NULL COMMENT '城市ID，关联ts_area表',
  `area` int(5) NOT NULL COMMENT '地区ID，关联ts_area表',
  `reg_ip` varchar(64) DEFAULT '127.0.0.1' COMMENT '册注IP',
  `lang` varchar(64) DEFAULT 'zh-cn' COMMENT '言语',
  `timezone` varchar(10) DEFAULT 'PRC' COMMENT '时区',
  `is_del` tinyint(2) NOT NULL COMMENT '是否禁用，0不禁用，1：禁用',
  `first_letter` char(1) DEFAULT NULL COMMENT '用户名称的首字母',
  `intro` varchar(255) DEFAULT NULL COMMENT '户用简介',
  `last_login_time` int(11) DEFAULT '0' COMMENT '户用最后一次登录时间',
  `last_feed_id` int(11) DEFAULT '0' COMMENT '户用最后发表的微博ID',
  `last_post_time` int(11) NOT NULL DEFAULT '0' COMMENT '户用最后发表微博的时间',
  `search_key` varchar(500) DEFAULT NULL COMMENT '搜索字段',
  `invite_code` varchar(120) DEFAULT NULL COMMENT '邀请注册码',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `email` (`email`),
  KEY `login` (`login`),
  KEY `uname` (`uname`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8");
E_D("replace into `wb_user` values('1','admin@admin.com','7e09fefdeb8540fade2d921b5714f6e3','11111','管理员','admin@admin.com','1','北京市 北京市 海淀区','1','1','1','1379476156','1','','','110000','110100','110108','127.0.0.1','zh-cn','PRC','0','G','','1379813904','0','0','管理员 guanliyuan','');");
E_D("replace into `wb_user` values('2','ceshi@ceshi.com','cf976f39ad1111f7b0e6e7d2635ad3e6','24744','ceshi','ceshi@ceshi.com','1','北京市 北京市 宣武区','1','1','1','1379833338','1',NULL,'','110000','110100','110104','0.0.0.0','zh-cn','PRC','0','C',NULL,'1379833430','3','1379833446','ceshi',NULL);");
E_D("replace into `wb_user` values('3','ceshi1@ceshi.com','f99fb08fc1b6d656369b5a3eb5454813','76258','ceshi1','ceshi1@ceshi.com','1','北京市 北京市 石景山区','1','1','1','1379833389','1',NULL,'','110000','110100','110107','0.0.0.0','zh-cn','PRC','0','C',NULL,'1379898248','31','1379924875','ceshi1',NULL);");

require("../../inc/footer.php");
?>