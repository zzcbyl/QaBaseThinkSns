<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_attach`;");
E_C("CREATE TABLE `wb_attach` (
  `attach_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `app_name` char(15) DEFAULT 'attach' COMMENT '应用名称',
  `table` char(15) DEFAULT NULL COMMENT '表名',
  `row_id` int(11) DEFAULT NULL COMMENT '管理的内容ID',
  `attach_type` varchar(20) NOT NULL COMMENT '附件所属类型',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户UID',
  `ctime` int(11) NOT NULL COMMENT '上传时间',
  `name` varchar(255) DEFAULT NULL COMMENT '附件名称',
  `type` varchar(255) DEFAULT NULL COMMENT '附件格式',
  `size` varchar(20) DEFAULT NULL COMMENT '附件大小',
  `extension` varchar(20) DEFAULT NULL COMMENT '附件扩展名',
  `hash` varchar(32) DEFAULT NULL COMMENT '附件哈希值',
  `private` tinyint(1) DEFAULT '0' COMMENT '是否私有（即对其他人不可见）',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '统一的is_del',
  `save_path` varchar(255) DEFAULT NULL COMMENT '保存路径',
  `save_name` varchar(255) DEFAULT NULL COMMENT '保存名称',
  `save_domain` tinyint(3) DEFAULT '0' COMMENT '附件保存的域ID（用于拆分附件存储到不同的服务器）',
  `from` tinyint(3) NOT NULL COMMENT '来源类型，0：网站；1：手机网页版；2：android；3：iphone',
  PRIMARY KEY (`attach_id`),
  KEY `userId` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8");
E_D("replace into `wb_attach` values('1','','','0','','1','1358736084','突出贡献100.png','image/png','17071','png','5ce3f245de5b835bcc968f94274e1f3b','0','0','2013/0121/10/','50fcaad40bc68.png','0','0');");
E_D("replace into `wb_attach` values('2','','','0','','1','1358736087','突出贡献30.png','image/png','3500','png','0284befe8d300742dc3e046869c18823','0','0','2013/0121/10/','50fcaad74c194.png','0','0');");
E_D("replace into `wb_attach` values('3','','','0','','1','1358736140','感谢有你100.png','image/png','14541','png','0ddbf7479a45e4bddea1721e70b2efdf','0','0','2013/0121/10/','50fcab0cb9b30.png','0','0');");
E_D("replace into `wb_attach` values('4','','','0','','1','1358736143','感谢有你30.png','image/png','3299','png','bdbcb9550f92773a9211d818e96104c9','0','0','2013/0121/10/','50fcab0fee736.png','0','0');");
E_D("replace into `wb_attach` values('5','','','0','','1','1358736213','新年快乐100.png','image/png','18203','png','c46874625444098eeba858cb56c2a4c4','0','0','2013/0121/10/','50fcab55c5b49.png','0','0');");
E_D("replace into `wb_attach` values('6','','','0','','1','1358736216','新年快乐30.png','image/png','3607','png','8d1223afda7b4b36569b52753916d92a','0','0','2013/0121/10/','50fcab58c2985.png','0','0');");
E_D("replace into `wb_attach` values('7','','','0','','1','1358736281','微吧神人100.png','image/png','16008','png','13a76b2cb737199f826cad5a4e9b8bbc','0','0','2013/0121/10/','50fcab9980cfb.png','0','0');");
E_D("replace into `wb_attach` values('8','','','0','','1','1358736284','微吧神人30.png','image/png','3169','png','fe0f00fe6604c223796cc3344f6eb84b','0','0','2013/0121/10/','50fcab9c94476.png','0','0');");
E_D("replace into `wb_attach` values('9','','','0','','1','1358736318','御用发言人100.png','image/png','14853','png','3059ba1ff2a91310b58e57ba56a6c1bf','0','0','2013/0121/10/','50fcabbe9dae4.png','0','0');");
E_D("replace into `wb_attach` values('10','','','0','','1','1358736321','御用发言人30.png','image/png','3211','png','1aa57937a546ef93f6fd224527734854','0','0','2013/0121/10/','50fcabc1832a5.png','0','0');");
E_D("replace into `wb_attach` values('11','','','0','','1','1358736353','最焦点100.png','image/png','15900','png','cdc60d619b8fa9c6b3e2a7546db60052','0','0','2013/0121/10/','50fcabe10ec23.png','0','0');");
E_D("replace into `wb_attach` values('12','','','0','','1','1358736355','最焦点30.png','image/png','3263','png','79456eecc305b3108676fabf0d1a0268','0','0','2013/0121/10/','50fcabe3b5ec9.png','0','0');");
E_D("replace into `wb_attach` values('13','','','0','','1','1358736391','操盘手100.png','image/png','16033','png','11bc172ce7a06afd864258f81b93fba6','0','0','2013/0121/10/','50fcac074d87a.png','0','0');");
E_D("replace into `wb_attach` values('14','','','0','','1','1358736394','操盘手30.png','image/png','3411','png','058ec862e61b1e5aabb3aabd9622fbb6','0','0','2013/0121/10/','50fcac0a3cc8f.png','0','0');");
E_D("replace into `wb_attach` values('15','','','0','','1','1358736429','微博至尊100.png','image/png','17007','png','2bd3f7d0cf57135713ccdaf53e1377af','0','0','2013/0121/10/','50fcac2d8c0c1.png','0','0');");
E_D("replace into `wb_attach` values('16','','','0','','1','1358736432','微博至尊30.png','image/png','3258','png','a851928ea80d2c8e2b1470fff2265611','0','0','2013/0121/10/','50fcac3064ff3.png','0','0');");
E_D("replace into `wb_attach` values('17','','','0','','1','1358736464','万人迷100.png','image/png','14775','png','f8a7b93f9cab6f644538789a59ee1b3a','0','0','2013/0121/10/','50fcac509f57a.png','0','0');");
E_D("replace into `wb_attach` values('18','','','0','','1','1358736467','万人迷30.png','image/png','3174','png','b907a256e6d9d4475e4fbf3abac1d944','0','0','2013/0121/10/','50fcac539dd7d.png','0','0');");
E_D("replace into `wb_attach` values('19','','','0','','1','1358736508','签到神人100.png','image/png','16181','png','391065bbf4d1a8257f6e86581524b7b8','0','0','2013/0121/10/','50fcac7c7d014.png','0','0');");
E_D("replace into `wb_attach` values('20','','','0','','1','1358736511','签到神人30.png','image/png','3209','png','f48bdb2546a94cdb20e56c10d70452e7','0','0','2013/0121/10/','50fcac7fce2cf.png','0','0');");
E_D("replace into `wb_attach` values('21','','','0','','1','1358736553','微吧牛人100.png','image/png','16012','png','4223b3a4693af7ad8a2691722756338c','0','0','2013/0121/10/','50fcaca99174e.png','0','0');");
E_D("replace into `wb_attach` values('22','','','0','','1','1358736556','微吧牛人30.png','image/png','3285','png','d4550944cc8e6dff0585f85092c7bfc0','0','0','2013/0121/10/','50fcacac7ffec.png','0','0');");
E_D("replace into `wb_attach` values('23','','','0','','1','1358736593','频道劳模100.png','image/png','14537','png','035db6c56a4066fd5e0b21101e1da23d','0','0','2013/0121/10/','50fcacd15426a.png','0','0');");
E_D("replace into `wb_attach` values('24','','','0','','1','1358736596','频道劳模30.png','image/png','3292','png','87636309060c3a11c251642fc414bc96','0','0','2013/0121/10/','50fcacd4a8dd4.png','0','0');");
E_D("replace into `wb_attach` values('25','','','0','','1','1358736629','焦点人物100.png','image/png','16549','png','65962b61dcefd39e9b97d97480a20ab4','0','0','2013/0121/10/','50fcacf5dc2af.png','0','0');");
E_D("replace into `wb_attach` values('26','','','0','','1','1358736633','焦点人物30.png','image/png','3253','png','5630a1742c187845880de2be825ccde4','0','0','2013/0121/10/','50fcacf9323bc.png','0','0');");
E_D("replace into `wb_attach` values('27','','','0','','1','1358736667','意见领袖100.png','image/png','14177','png','35ff66e290911c1cc8c71c7bfc175996','0','0','2013/0121/10/','50fcad1be6612.png','0','0');");
E_D("replace into `wb_attach` values('28','','','0','','1','1358736673','意见领袖30.png','image/png','3018','png','1a4dede1dfbbb3caad9092def6a1db63','0','0','2013/0121/10/','50fcad21073db.png','0','0');");
E_D("replace into `wb_attach` values('29','','','0','','1','1358736705','微名远扬100.png','image/png','14394','png','477cbdf2d7c33e3f45cdda8238f0e8cf','0','0','2013/0121/10/','50fcad41d21e4.png','0','0');");
E_D("replace into `wb_attach` values('30','','','0','','1','1358736710','微名远扬30.png','image/png','3173','png','b574768536b539c1f6054f59099b8e01','0','0','2013/0121/10/','50fcad466e156.png','0','0');");
E_D("replace into `wb_attach` values('31','','','0','','1','1358736732','微名远扬100.png','image/png','14394','png','477cbdf2d7c33e3f45cdda8238f0e8cf','0','0','2013/0121/10/','50fcad5c83400.png','0','0');");
E_D("replace into `wb_attach` values('32','','','0','','1','1358736736','微名远扬30.png','image/png','3173','png','b574768536b539c1f6054f59099b8e01','0','0','2013/0121/10/','50fcad60d607d.png','0','0');");
E_D("replace into `wb_attach` values('33','','','0','','1','1358736755','频道劳模100.png','image/png','14537','png','035db6c56a4066fd5e0b21101e1da23d','0','0','2013/0121/10/','50fcad73071a7.png','0','0');");
E_D("replace into `wb_attach` values('34','','','0','','1','1358736758','频道劳模30.png','image/png','3292','png','87636309060c3a11c251642fc414bc96','0','0','2013/0121/10/','50fcad7653b3e.png','0','0');");
E_D("replace into `wb_attach` values('35','','','0','','1','1358736832','签到牛人100.png','image/png','15821','png','4b841ba7db505c7cc82f8d1295fa99c6','0','0','2013/0121/10/','50fcadc03ac71.png','0','0');");
E_D("replace into `wb_attach` values('36','','','0','','1','1358736835','签到牛人30.png','image/png','3247','png','02e276ded1db6fd68893964368f755c5','0','0','2013/0121/10/','50fcadc31f48c.png','0','0');");
E_D("replace into `wb_attach` values('37','','','0','','1','1358736922','微博劳模100.png','image/png','14985','png','c47947f11a9165fb8dae7c21eebbedf6','0','0','2013/0121/10/','50fcae1a844aa.png','0','0');");
E_D("replace into `wb_attach` values('38','','','0','','1','1358736928','微博劳模30.png','image/png','3018','png','1a4dede1dfbbb3caad9092def6a1db63','0','0','2013/0121/10/','50fcae2007d83.png','0','0');");
E_D("replace into `wb_attach` values('39','','','0','','1','1358736962','微吧达人100.png','image/png','15872','png','3bbac1df642fceeb9c6dc88b4f73fdf8','0','0','2013/0121/10/','50fcae42c6f85.png','0','0');");
E_D("replace into `wb_attach` values('40','','','0','','1','1358736965','微吧达人30.png','image/png','3245','png','55eb39468fe298b439e3450a8999a343','0','0','2013/0121/10/','50fcae45c50bc.png','0','0');");
E_D("replace into `wb_attach` values('41','','','0','','1','1358736996','频道先锋100.png','image/png','14479','png','1b766f225396182bd1b627313a0ba603','0','0','2013/0121/10/','50fcae64a3461.png','0','0');");
E_D("replace into `wb_attach` values('42','','','0','','1','1358736999','频道先锋30.png','image/png','3284','png','32be175f35a8264728010dc01f94dccc','0','0','2013/0121/10/','50fcae675c040.png','0','0');");
E_D("replace into `wb_attach` values('43','','','0','','1','1358737031','微博控100.png','image/png','13810','png','bef6b7df24bdfd4f2b1426812a360473','0','0','2013/0121/10/','50fcae87315e3.png','0','0');");
E_D("replace into `wb_attach` values('44','','','0','','1','1358737036','微博控30.png','image/png','3062','png','48868feefe614e555addc7f23b6f5ff6','0','0','2013/0121/10/','50fcae8c2c0d9.png','0','0');");
E_D("replace into `wb_attach` values('45','','','0','','1','1358737078','最佳人缘100.png','image/png','17214','png','1ffc50eb528935c6c4f28b1864756832','0','0','2013/0121/10/','50fcaeb6a33e1.png','0','0');");
E_D("replace into `wb_attach` values('46','','','0','','1','1358737083','最佳人缘30.png','image/png','3378','png','2b4c8d9ab1508a3354bcd763190f56f1','0','0','2013/0121/10/','50fcaebb8beb5.png','0','0');");
E_D("replace into `wb_attach` values('47','','','0','','1','1358737144','小有名气100.png','image/png','14564','png','6fcff8fb4aa470e0732cf4e80c866b93','0','0','2013/0121/10/','50fcaef801623.png','0','0');");
E_D("replace into `wb_attach` values('48','','','0','','1','1358737148','小有名气30.png','image/png','3114','png','41e255fea2079f4bfc41d16901d40727','0','0','2013/0121/10/','50fcaefc44e85.png','0','0');");
E_D("replace into `wb_attach` values('49','','','0','','1','1358737186','签到达人100.png','image/png','15755','png','0eb8888c1546f506d42ae950cbdcb850','0','0','2013/0121/10/','50fcaf2264740.png','0','0');");
E_D("replace into `wb_attach` values('50','','','0','','1','1358737189','签到达人30.png','image/png','3218','png','4570359d04aed10ecd4ab716518732ca','0','0','2013/0121/10/','50fcaf256df86.png','0','0');");
E_D("replace into `wb_attach` values('51','','','0','','1','1358737227','微吧先锋100.png','image/png','15568','png','dbfd7610b788ae6141667d32be594cc4','0','0','2013/0121/11/','50fcaf4b75701.png','0','0');");
E_D("replace into `wb_attach` values('52','','','0','','1','1358737232','微吧先锋30.png','image/png','3186','png','214c81d43dfaf28f516f36fe3ee0aa16','0','0','2013/0121/11/','50fcaf50bb581.png','0','0');");
E_D("replace into `wb_attach` values('53','','','0','','1','1358737265','魅力初现100.png','image/png','14308','png','d72e21505f62770e754d762901e49164','0','0','2013/0121/11/','50fcaf710b89e.png','0','0');");
E_D("replace into `wb_attach` values('54','','','0','','1','1358737268','魅力初现30.png','image/png','3115','png','a15347ae308354318366915b8e571ae1','0','0','2013/0121/11/','50fcaf740bcd8.png','0','0');");
E_D("replace into `wb_attach` values('55','','','0','','1','1358737310','我爱签到100.png','image/png','15931','png','010d94c1a424e72c0dc0d1793fef5915','0','0','2013/0121/11/','50fcaf9e58432.png','0','0');");
E_D("replace into `wb_attach` values('56','','','0','','1','1358737314','我爱签到30.png','image/png','3233','png','916a7b33f565af066749d3d082823ea1','0','0','2013/0121/11/','50fcafa273e94.png','0','0');");
E_D("replace into `wb_attach` values('57','','','0','','1','1358737347','微博先锋100.png','image/png','16929','png','fb34eb7451f41acbf072d4f0e398a3ff','0','0','2013/0121/11/','50fcafc30a9bc.png','0','0');");
E_D("replace into `wb_attach` values('58','','','0','','1','1358737351','微博先锋30.png','image/png','3070','png','82623f339c1ad7dde568fa37cc16849a','0','0','2013/0121/11/','50fcafc7153df.png','0','0');");
E_D("replace into `wb_attach` values('59','','','0','','1','1358737391','签到先锋100.png','image/png','15699','png','6f2ac6156305508577bea2c8830fd04e','0','0','2013/0121/11/','50fcafefac4bd.png','0','0');");
E_D("replace into `wb_attach` values('60','','','0','','1','1358737397','签到先锋30.png','image/png','3239','png','018904fe547c25491aa24ec86d948cd7','0','0','2013/0121/11/','50fcaff50a604.png','0','0');");
E_D("replace into `wb_attach` values('61','','','0','','1','1358737436','有头有脸100.png','image/png','13781','png','3efd604658256fd551379b21b286149e','0','0','2013/0121/11/','50fcb01ce9d19.png','0','0');");
E_D("replace into `wb_attach` values('62','','','0','','1','1358737440','有头有脸30.png','image/png','3075','png','1156bd22a8a1cc2b731f63967dc0202c','0','0','2013/0121/11/','50fcb020af27a.png','0','0');");
E_D("replace into `wb_attach` values('63','','','0','','1','1358737474','独孤求败100.png','image/png','16835','png','cf9fb5e52b7e2c274b24d9b562018c39','0','0','2013/0121/11/','50fcb0426d7df.png','0','0');");
E_D("replace into `wb_attach` values('64','','','0','','1','1358737477','独孤求败30.png','image/png','3222','png','7af7e22e00525b2f06c1230cf8f18c59','0','0','2013/0121/11/','50fcb04563a81.png','0','0');");
E_D("replace into `wb_attach` values('65','','','0','','1','1358737506','武林高手100.png','image/png','17209','png','1f9cea9b8c4c574ce557807b2d8fb935','0','0','2013/0121/11/','50fcb062ed874.png','0','0');");
E_D("replace into `wb_attach` values('66','','','0','','1','1358737511','武林高手30.png','image/png','3302','png','d0ed0a36b65371a951812c02d9a17f02','0','0','2013/0121/11/','50fcb0670fb6e.png','0','0');");
E_D("replace into `wb_attach` values('67','','','0','','1','1358737547','微博达人100.png','image/png','14966','png','d16dc4877a6a4f3ec62ac4f93a421769','0','0','2013/0121/11/','50fcb08b08634.png','0','0');");
E_D("replace into `wb_attach` values('68','','','0','','1','1358737551','微博达人30.png','image/png','3217','png','afb9b889dc85b7b46aef6166d2401446','0','0','2013/0121/11/','50fcb08f3d1e4.png','0','0');");
E_D("replace into `wb_attach` values('69','','','0','','1','1358737602','小有进步100.png','image/png','14843','png','5d852275a079b320bab5441120a8152e','0','0','2013/0121/11/','50fcb0c2a4fa2.png','0','0');");
E_D("replace into `wb_attach` values('70','','','0','','1','1358737607','小有进步30.png','image/png','3261','png','faff46a317353cd6aa1fd3d1fc372a2e','0','0','2013/0121/11/','50fcb0c77c4b6.png','0','0');");
E_D("replace into `wb_attach` values('71','','','0','','1','1358737622','新手上路100.png','image/png','14881','png','02d326dd1218002916a8d87e524aa6c7','0','0','2013/0121/11/','50fcb0d64a404.png','0','0');");
E_D("replace into `wb_attach` values('72','','','0','','1','1358737626','新手上路30.png','image/png','3139','png','85196d60385399a4e985c46e5dd495e5','0','0','2013/0121/11/','50fcb0da765d8.png','0','0');");

require("../../inc/footer.php");
?>