<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_pic_show`;");
E_C("CREATE TABLE `wb_pic_show` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL COMMENT '照片URL地址',
  `title` varchar(255) DEFAULT NULL COMMENT '照片标题',
  `desc` varchar(255) DEFAULT NULL COMMENT '照片摘要',
  `target` varchar(20) DEFAULT NULL COMMENT '打开方式',
  `ctime` int(11) NOT NULL COMMENT '创建时间',
  `attachId` int(10) DEFAULT NULL COMMENT '照片ID',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

require("../../inc/footer.php");
?>