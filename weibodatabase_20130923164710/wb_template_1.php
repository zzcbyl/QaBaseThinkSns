<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_template`;");
E_C("CREATE TABLE `wb_template` (
  `tpl_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '模板名，使用英文，保证唯一性。格式建议：“类型_动作”，如“blog_add”或“credit_blog_add”',
  `alias` varchar(255) DEFAULT NULL COMMENT '模板别名，如“发表博客”',
  `title` text COMMENT '标题模板，使用“{”和“}”包含变量名，如“{actor}做了{action}”',
  `body` text COMMENT '内容模板，使用“{”和“}”包含变量名，如“{actor}做了{action}增加了{volume}个{credit_type}”',
  `lang` varchar(255) NOT NULL DEFAULT 'zh' COMMENT '语言，与全局语言包一致，如“en”、“zh”等，目前只支持“zh”',
  `type` varchar(255) DEFAULT NULL COMMENT '模板类型，如blog,credit等',
  `type2` varchar(255) DEFAULT NULL COMMENT '模板类型2，备用类型，可留空。如“credit_blog”等',
  `is_cache` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否使用默认的模板缓存系统进行缓存，0：否；1：是',
  `ctime` int(11) DEFAULT NULL COMMENT '模板建立时间戳',
  PRIMARY KEY (`tpl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>