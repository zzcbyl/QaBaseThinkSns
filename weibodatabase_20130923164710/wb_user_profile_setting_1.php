<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_user_profile_setting`;");
E_C("CREATE TABLE `wb_user_profile_setting` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '数据类型：1、分组，2、字段',
  `field_key` varchar(120) NOT NULL COMMENT '字段键值',
  `field_name` varchar(120) NOT NULL COMMENT '字段名称',
  `field_type` int(5) NOT NULL DEFAULT '0' COMMENT '字段类型ID，值为上一级字段ID，值为0时代表根分类',
  `visiable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否空间展示：默认1=可展示',
  `editable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可修改：默认1=可修改',
  `required` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否必填项：默认0=非必填',
  `privacy` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：所有人，1：好友， 2：互相关注，3：关注我的，4：我关注的',
  `display_order` int(11) NOT NULL DEFAULT '0' COMMENT '字段排序符号值',
  `form_type` varchar(120) DEFAULT NULL COMMENT '字段表单类型：input、textarea、select、radio、checkbox、timeinput',
  `form_default_value` text COMMENT '默认选择的数据项',
  `validation` varchar(120) DEFAULT NULL COMMENT '前台表单验证的方法名',
  `tips` varchar(255) DEFAULT NULL COMMENT '提示说明',
  `is_system` int(2) NOT NULL DEFAULT '0' COMMENT '是否系统配置0不是，1是，系统的配置项不能删除，不能改key',
  PRIMARY KEY (`field_id`),
  KEY `type` (`type`,`field_key`,`display_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>