<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_medal`;");
E_C("CREATE TABLE `wb_medal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '勋章名称',
  `desc` varchar(255) DEFAULT NULL COMMENT '勋章描述',
  `src` varchar(255) NOT NULL COMMENT '大图地址',
  `small_src` varchar(255) DEFAULT NULL COMMENT '小图地址',
  `type` int(11) DEFAULT NULL COMMENT '勋章类型',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=104 DEFAULT CHARSET=utf8");
E_D("replace into `wb_medal` values('81','签到达人','连续签到20次以上后获得的奖励勋章','49|2013/0121/10/50fcaf2264740.png','50|2013/0121/10/50fcaf256df86.png',NULL);");
E_D("replace into `wb_medal` values('72','微博达人','完成达人任务后获得的奖励勋章','67|2013/0121/11/50fcb08b08634.png','68|2013/0121/11/50fcb08f3d1e4.png',NULL);");
E_D("replace into `wb_medal` values('71','小有进步','完成进阶任务后获得的奖励勋章','69|2013/0121/11/50fcb0c2a4fa2.png','70|2013/0121/11/50fcb0c77c4b6.png',NULL);");
E_D("replace into `wb_medal` values('70','新手上路','完成新手任务后获得的奖励勋章','71|2013/0121/11/50fcb0d64a404.png','72|2013/0121/11/50fcb0da765d8.png',NULL);");
E_D("replace into `wb_medal` values('89','微博劳模','发表1000条以上原创微博的用户获得的奖励勋章','37|2013/0121/10/50fcae1a844aa.png','38|2013/0121/10/50fcae2007d83.png',NULL);");
E_D("replace into `wb_medal` values('88','微名远扬','拥有1000个以上粉丝的用户获得的奖励勋章','31|2013/0121/10/50fcad5c83400.png','32|2013/0121/10/50fcad60d607d.png',NULL);");
E_D("replace into `wb_medal` values('87','签到牛人','连续签到130次以上获得的奖励勋章','35|2013/0121/10/50fcadc03ac71.png','36|2013/0121/10/50fcadc31f48c.png',NULL);");
E_D("replace into `wb_medal` values('86','微吧达人','在微吧有1篇以上的精华帖子获得的勋章奖励','39|2013/0121/10/50fcae42c6f85.png','40|2013/0121/10/50fcae45c50bc.png',NULL);");
E_D("replace into `wb_medal` values('85','频道先锋','向频道投稿并有2条以上被收录','41|2013/0121/10/50fcae64a3461.png','42|2013/0121/10/50fcae675c040.png',NULL);");
E_D("replace into `wb_medal` values('84','微博控','发表原创微博超过100条用户获得的奖励勋章','43|2013/0121/10/50fcae87315e3.png','44|2013/0121/10/50fcae8c2c0d9.png',NULL);");
E_D("replace into `wb_medal` values('83','最佳人缘','与30以上的用户互相关注获得的奖励勋章','45|2013/0121/10/50fcaeb6a33e1.png','46|2013/0121/10/50fcaebb8beb5.png',NULL);");
E_D("replace into `wb_medal` values('82','小有名气','拥有100个以上粉丝的用户获得的奖励勋章','47|2013/0121/10/50fcaef801623.png','48|2013/0121/10/50fcaefc44e85.png',NULL);");
E_D("replace into `wb_medal` values('95','万人迷','拥有10000个以上粉丝的用户获得的奖励勋章','17|2013/0121/10/50fcac509f57a.png','18|2013/0121/10/50fcac539dd7d.png',NULL);");
E_D("replace into `wb_medal` values('94','签到神人','连续签到365次以上获得的奖励勋章','19|2013/0121/10/50fcac7c7d014.png','20|2013/0121/10/50fcac7fce2cf.png',NULL);");
E_D("replace into `wb_medal` values('93','微吧牛人','在微吧至少有10篇以上的精华帖子用户获得奖励勋章','21|2013/0121/10/50fcaca99174e.png','22|2013/0121/10/50fcacac7ffec.png',NULL);");
E_D("replace into `wb_medal` values('92','频道劳模','向频道投稿并有100条以上的微博被收录','23|2013/0121/10/50fcacd15426a.png','24|2013/0121/10/50fcacd4a8dd4.png',NULL);");
E_D("replace into `wb_medal` values('78','我爱签到','连续签到3次以上获得的奖励勋章','55|2013/0121/11/50fcaf9e58432.png','56|2013/0121/11/50fcafa273e94.png',NULL);");
E_D("replace into `wb_medal` values('79','魅力初现','拥有30个以上粉丝的用户获得的奖励勋章','53|2013/0121/11/50fcaf710b89e.png','54|2013/0121/11/50fcaf740bcd8.png',NULL);");
E_D("replace into `wb_medal` values('80','微吧先锋','在微吧发表1篇帖子后获得的奖励勋章','51|2013/0121/11/50fcaf4b75701.png','52|2013/0121/11/50fcaf50bb581.png',NULL);");
E_D("replace into `wb_medal` values('91','焦点人物','单条微博被评论100次以上的用户获得的奖励勋章','25|2013/0121/10/50fcacf5dc2af.png','26|2013/0121/10/50fcacf9323bc.png',NULL);");
E_D("replace into `wb_medal` values('77','微博先锋','首次发布微博并@提到好友后获得的奖励勋章','57|2013/0121/11/50fcafc30a9bc.png','58|2013/0121/11/50fcafc7153df.png',NULL);");
E_D("replace into `wb_medal` values('90','意见领袖','单条微博被转发100次以上的用户获得的奖励勋章','27|2013/0121/10/50fcad1be6612.png','28|2013/0121/10/50fcad21073db.png',NULL);");
E_D("replace into `wb_medal` values('73','武林高手','完成高手任务后获得的奖励勋章','65|2013/0121/11/50fcb062ed874.png','66|2013/0121/11/50fcb0670fb6e.png',NULL);");
E_D("replace into `wb_medal` values('74','独孤求败','完成终极任务后获得的奖励勋章','63|2013/0121/11/50fcb0426d7df.png','64|2013/0121/11/50fcb04563a81.png',NULL);");
E_D("replace into `wb_medal` values('75','有头有脸','上传头像后获得的奖励勋章','61|2013/0121/11/50fcb01ce9d19.png','62|2013/0121/11/50fcb020af27a.png',NULL);");
E_D("replace into `wb_medal` values('76','签到先锋','首次签到后获得的奖励勋章','59|2013/0121/11/50fcafefac4bd.png','60|2013/0121/11/50fcaff50a604.png',NULL);");
E_D("replace into `wb_medal` values('96','微博至尊','发表原创微博超过100000条用户获得的奖励勋章','15|2013/0121/10/50fcac2d8c0c1.png','16|2013/0121/10/50fcac3064ff3.png',NULL);");
E_D("replace into `wb_medal` values('97','操盘手','单条微博被转发1000次以上的用户获得的奖励勋章','13|2013/0121/10/50fcac074d87a.png','14|2013/0121/10/50fcac0a3cc8f.png',NULL);");
E_D("replace into `wb_medal` values('98','最焦点','单条微博被评论1000次以上的用户获得的奖励勋章','11|2013/0121/10/50fcabe10ec23.png','12|2013/0121/10/50fcabe3b5ec9.png',NULL);");
E_D("replace into `wb_medal` values('99','御用发言人','向频道投稿并有1000条以上的微博被收录','9|2013/0121/10/50fcabbe9dae4.png','10|2013/0121/10/50fcabc1832a5.png',NULL);");
E_D("replace into `wb_medal` values('100','微吧神人','在微吧至少有100篇以上的精华帖子用户获得奖励勋章','7|2013/0121/10/50fcab9980cfb.png','8|2013/0121/10/50fcab9c94476.png',NULL);");
E_D("replace into `wb_medal` values('101','新年快乐','限时发放“新年快乐”勋章，限量100枚','5|2013/0121/10/50fcab55c5b49.png','6|2013/0121/10/50fcab58c2985.png','3');");
E_D("replace into `wb_medal` values('102','感谢有你','感谢所有ThinkSNS用户四年的相伴','3|2013/0121/10/50fcab0cb9b30.png','4|2013/0121/10/50fcab0fee736.png','3');");
E_D("replace into `wb_medal` values('103','突出贡献','特别颁发给为ThinkSNS的发展做出了突出贡献的用户','1|2013/0121/10/50fcaad40bc68.png','2|2013/0121/10/50fcaad74c194.png',NULL);");

require("../../inc/footer.php");
?>