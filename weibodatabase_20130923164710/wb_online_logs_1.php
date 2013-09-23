<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_online_logs`;");
E_C("CREATE TABLE `wb_online_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `day` date NOT NULL COMMENT '日期',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `uname` varchar(50) NOT NULL COMMENT '用户名称',
  `action` varchar(255) NOT NULL COMMENT '访问地址',
  `refer` text NOT NULL COMMENT '来源页面',
  `isGuest` tinyint(3) NOT NULL COMMENT '是否游客',
  `isIntranet` tinyint(3) NOT NULL COMMENT '是否内部用户',
  `ip` varchar(20) NOT NULL COMMENT 'IP',
  `agent` varchar(50) NOT NULL COMMENT '浏览器',
  `ext` varchar(20) NOT NULL COMMENT '扩展字段',
  `statsed` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否已经统计过',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=193 DEFAULT CHARSET=utf8");
E_D("replace into `wb_online_logs` values('29','2013-09-22','0','guest','public/Register/index','http://localhost/index.php?app=public&mod=Register&act=index','1','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('26','2013-09-22','2','ceshi','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('25','2013-09-22','2','ceshi','public/Register/step4','http://localhost/index.php?app=public&mod=Register&act=step4','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('23','2013-09-22','2','ceshi','public/Register/step2','http://localhost/index.php?app=public&mod=Register&act=step2','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('22','2013-09-22','0','guest','public/Register/doStep1','http://localhost/index.php?app=public&mod=Register&act=doStep1','1','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('18','2013-09-22','1','管理员','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('16','2013-09-22','0','guest','public/Passport/login','http://localhost/','1','0','::1','Firefox','','1');");
E_D("replace into `wb_online_logs` values('17','2013-09-22','1','管理员','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('28','2013-09-22','0','guest','public/Passport/login','http://localhost/index.php?app=public&mod=Passport&act=login','1','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('27','2013-09-22','2','ceshi','public/Passport/logout','http://localhost/index.php?app=public&mod=Passport&act=logout','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('24','2013-09-22','2','ceshi','public/Register/step3','http://localhost/index.php?app=public&mod=Register&act=step3','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('21','2013-09-22','0','guest','public/Register/index','http://localhost/index.php?app=public&mod=Register&act=index','1','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('20','2013-09-22','0','guest','public/Passport/login','http://localhost/index.php?app=public&mod=Passport&act=login','1','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('19','2013-09-22','1','管理员','public/Passport/logout','http://localhost/index.php?app=public&mod=Passport&act=logout','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('30','2013-09-22','0','guest','public/Register/doStep1','http://localhost/index.php?app=public&mod=Register&act=doStep1','1','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('31','2013-09-22','3','ceshi1','public/Register/step2','http://localhost/index.php?app=public&mod=Register&act=step2','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('32','2013-09-22','3','ceshi1','public/Register/step3','http://localhost/index.php?app=public&mod=Register&act=step3','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('33','2013-09-22','3','ceshi1','public/Register/step4','http://localhost/index.php?app=public&mod=Register&act=step4','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('34','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('35','2013-09-22','3','ceshi1','public/Search/index','http://localhost/index.php?app=public&mod=Search&t=2&a=public&k=ceshi','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('36','2013-09-22','3','ceshi1','public/Search/index','http://localhost/index.php?app=public&mod=Search&act=index&t=1&a=public&k=ceshi','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('37','2013-09-22','3','ceshi1','public/Passport/logout','http://localhost/index.php?app=public&mod=Passport&act=logout','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('38','2013-09-22','0','guest','public/Passport/login','http://localhost/index.php?app=public&mod=Passport&act=login','1','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('39','2013-09-22','2','ceshi','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('40','2013-09-22','2','ceshi','public/Index/follower','http://localhost/index.php?app=public&mod=Index&act=follower&uid=2','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('41','2013-09-22','2','ceshi','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('42','2013-09-22','2','ceshi','public/Passport/logout','http://localhost/index.php?app=public&mod=Passport&act=logout','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('43','2013-09-22','0','guest','public/Passport/login','http://localhost/index.php?app=public&mod=Passport&act=login','1','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('44','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('45','2013-09-22','3','ceshi1','public/Index/follower','http://localhost/index.php?app=public&mod=Index&act=follower&uid=3','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('46','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('47','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('48','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index&type=channel','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('49','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('50','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index&type=all','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('51','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('52','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index&type=channel','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('53','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('54','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('55','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index&type=all','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('56','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index&type=channel','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('57','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('58','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('59','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('60','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('61','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index&type=all','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('62','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('63','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index&type=all','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('64','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('65','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('66','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('67','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('68','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('69','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('70','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('71','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('72','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('73','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('74','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('75','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('76','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('77','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('78','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('79','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('80','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('81','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('82','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('83','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('84','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('85','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('86','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('87','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('88','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('89','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('90','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('91','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('92','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('93','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('94','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('95','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('96','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('97','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('98','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('99','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('100','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('101','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('102','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('103','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('104','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('105','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('106','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('107','2013-09-22','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('108','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('109','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('110','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('111','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('112','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('113','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('114','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('115','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('116','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('117','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('118','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('119','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('120','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('121','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('122','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('123','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('124','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('125','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('126','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('127','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('128','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('129','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('130','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('131','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('132','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('133','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('134','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('135','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('136','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('137','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('138','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('139','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('140','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('141','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('142','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('143','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('144','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('145','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('146','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('147','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('148','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('149','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('150','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('151','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('152','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('153','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('154','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('155','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('156','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('157','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('158','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('159','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('160','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('161','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('162','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('163','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('164','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('165','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('166','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('167','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('168','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('169','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('170','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('171','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('172','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('173','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('174','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('175','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('176','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('177','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('178','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('179','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('180','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('181','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('182','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('183','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('184','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('185','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('186','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('187','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('188','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('189','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('190','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('191','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");
E_D("replace into `wb_online_logs` values('192','2013-09-23','3','ceshi1','public/Index/index','http://localhost/index.php?app=public&mod=Index&act=index','0','0','::1','Firefox','','0');");

require("../../inc/footer.php");
?>