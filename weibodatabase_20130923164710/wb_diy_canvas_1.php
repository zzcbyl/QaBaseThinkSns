<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_diy_canvas`;");
E_C("CREATE TABLE `wb_diy_canvas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT 'DIY画布标题',
  `canvas_name` varchar(255) DEFAULT NULL COMMENT 'DIY画布名称--对应模板文件名',
  `data` text COMMENT '模板数据',
  `description` varchar(500) DEFAULT NULL COMMENT '画布说明',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8");
E_D("replace into `wb_diy_canvas` values('1','首页','index.html','PGluY2x1ZGUgZmlsZT0iX19USEVNRV9fL3B1YmxpY19oZWFkZXIiIC8+DQo8ZGl2IHN0eWxlPSJoZWlnaHQ6NjBweCI+PC9kaXY+DQo8aW5jbHVkZSBmaWxlPSJfX1RIRU1FX18vZGl5X2hlYWRlciIgLz4NCjxsaW5rIGhyZWY9Il9fQVBQX18vUHVibGljL2Nzcy9kaXlfYWRhcHRhYmxlLmNzcyIgcmVsPSJzdHlsZXNoZWV0IiB0eXBlPSJ0ZXh0L2NzcyIgLz4NCjxsaW5rIGhyZWY9Il9fQVBQX18vUHVibGljL2Nzcy9pbmRleC5jc3MiIHJlbD0ic3R5bGVzaGVldCIgdHlwZT0idGV4dC9jc3MiIC8+DQo8bGluayBocmVmPSJfX0FQUF9fL1B1YmxpYy9jc3MvcG9wX3VwLmNzcyIgcmVsPSJzdHlsZXNoZWV0IiB0eXBlPSJ0ZXh0L2NzcyIgLz4NCjxkaXYgY2xhc3M9ImRpeV9jb250ZW50IGJnX2RpeSBib3hTaGFkb3ciIHN0eWxlPSJ3aWR0aDo5NjBweCI+DQp7JGRhdGF9DQogICAgPGRpdiBjbGFzcz0iQyI+DQogICAgPC9kaXY+DQoNCg0KPC9kaXY+DQoNCjxpbmNsdWRlIGZpbGU9Il9fVEhFTUVfXy9wdWJsaWNfZm9vdGVyIiAvPg==','首页');");

require("../../inc/footer.php");
?>