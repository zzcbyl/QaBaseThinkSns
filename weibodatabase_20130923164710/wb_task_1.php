<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_task`;");
E_C("CREATE TABLE `wb_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_level` int(11) DEFAULT NULL COMMENT '任务等级',
  `task_name` varchar(255) DEFAULT NULL COMMENT '任务名称',
  `task_type` int(11) DEFAULT NULL COMMENT '任务类型',
  `step_name` varchar(255) DEFAULT NULL COMMENT '任务步骤名称',
  `step_desc` varchar(500) DEFAULT NULL COMMENT '任务步骤说明',
  `condition` varchar(255) DEFAULT NULL COMMENT '任务条件',
  `action` varchar(255) DEFAULT NULL COMMENT '动作',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  `reward` varchar(255) DEFAULT NULL COMMENT '任务奖励',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=utf8");
E_D("replace into `wb_task` values('1','1','每日任务','1','发布1条原创微博','在我的首页发布1条原创微博','{\"weibopost\":1}','',NULL,'{\"exp\":5,\"score\":5,\"medal\":null}');");
E_D("replace into `wb_task` values('2','1','每日任务','1','转发1条微博','在我的首页转发1条他人的微博','{\"weiborepost\":1}','',NULL,'{\"exp\":5,\"score\":5,\"medal\":null}');");
E_D("replace into `wb_task` values('3','1','每日任务','1','评论1条微博','在我的首页评论1条他人的微博','{\"weibocomment\":1}','',NULL,'{\"exp\":5,\"score\":5,\"medal\":null}');");
E_D("replace into `wb_task` values('4','1','新手任务','2','上传头像','在帐号-设置-头像设置里上传头像','{\"uploadface\":1}','',NULL,'{\"exp\":2,\"score\":2,\"medal\":{\"id\":75,\"name\":\"\\\\u6709\\\\u5934\\\\u6709\\\\u8138\",\"src\":\"2013\\\\/0121\\\\/11\\\\/50fcb01ce9d19.png\"}}');");
E_D("replace into `wb_task` values('5','1','新手任务','2','转发1条微博','在微博列表中转发1条微博','{\"weiborepost\":1}','',NULL,'{\"exp\":2,\"score\":2,\"medal\":null}');");
E_D("replace into `wb_task` values('6','1','新手任务','2','评论1条微博','在微博列表中评论1条微博','{\"weibocomment\":1}','',NULL,'{\"exp\":2,\"score\":2,\"medal\":null}');");
E_D("replace into `wb_task` values('8','2','进阶任务','2','拥有30个以上的粉丝','将自己的粉丝数扩充到30个以上','{\"following\":30}','',NULL,'{\"exp\":5,\"score\":5,\"medal\":{\"id\":79,\"name\":\"\\\\u9b45\\\\u529b\\\\u521d\\\\u73b0\",\"src\":\"2013\\\\/0121\\\\/11\\\\/50fcaf710b89e.png\"}}');");
E_D("replace into `wb_task` values('10','1','每日任务','1','签到1次','在我的首页中签到1次','{\"checkin\":1}','',NULL,'{\"exp\":5,\"score\":5,\"medal\":{\"id\":76,\"name\":\"\\\\u7b7e\\\\u5230\\\\u5148\\\\u950b\",\"src\":\"2013\\\\/0121\\\\/11\\\\/50fcafefac4bd.png\"}}');");
E_D("replace into `wb_task` values('11','3','达人任务','2','发布100条以上的原创微博','发布100条以上的原创微博','{\"weibopost\":100}','',NULL,'{\"exp\":10,\"score\":10,\"medal\":{\"id\":84,\"name\":\"\\\\u5fae\\\\u535a\\\\u63a7\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcae87315e3.png\"}}');");
E_D("replace into `wb_task` values('12','3','达人任务','2','拥有100个以上的粉丝','拥有100个以上的粉丝','{\"following\":100}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":82,\"name\":\"\\\\u5c0f\\\\u6709\\\\u540d\\\\u6c14\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcaef801623.png\"}}');");
E_D("replace into `wb_task` values('13','4','高手任务','2','发布1000条以上原创微博','发布1000条以上原创微博','{\"weiborepost\":1000}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":89,\"name\":\"\\\\u5fae\\\\u535a\\\\u52b3\\\\u6a21\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcae1a844aa.png\"}}');");
E_D("replace into `wb_task` values('14','4','高手任务','2','拥有1000个以上的粉丝','拥有1000个以上的粉丝','{\"following\":1000}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":88,\"name\":\"\\\\u5fae\\\\u540d\\\\u8fdc\\\\u626c\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcad5c83400.png\"}}');");
E_D("replace into `wb_task` values('15','5','终极任务','2','拥有10000个以上的粉丝','拥有10000个以上的粉丝','{\"following\":10000}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":95,\"name\":\"\\\\u4e07\\\\u4eba\\\\u8ff7\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcac509f57a.png\"}}');");
E_D("replace into `wb_task` values('16','5','终极任务','2','发布10000以上的原创微博','发布10000以上的原创微博','{\"weiborepost\":10000}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":96,\"name\":\"\\\\u5fae\\\\u535a\\\\u81f3\\\\u5c0a\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcac2d8c0c1.png\"}}');");
E_D("replace into `wb_task` values('17','1','新手任务','2','完善个人资料','在帐号-设置-基本信息里完善你的个人资料','{\"userinfo\":1}','',NULL,'{\"exp\":5,\"score\":5,\"medal\":null}');");
E_D("replace into `wb_task` values('18','1','新手任务','2','关注1个感兴趣的人','在可能感兴趣的人或者找人里关注1个感兴趣的人','{\"followinterest\":1}','',NULL,'{\"exp\":2,\"score\":2,\"medal\":null}');");
E_D("replace into `wb_task` values('19','1','新手任务','2','发布1条微博并@提到你的好友','发布1条原创微博并@提到你的好友','{\"weibotofriend\":1}','',NULL,'{\"exp\":5,\"score\":5,\"medal\":{\"id\":77,\"name\":\"\\\\u5fae\\\\u535a\\\\u5148\\\\u950b\",\"src\":\"2013\\\\/0121\\\\/11\\\\/50fcafc30a9bc.png\"}}');");
E_D("replace into `wb_task` values('20','2','进阶任务','2','连续签到3次','连续签到3次以上','{\"checkin\":3}','',NULL,'{\"exp\":5,\"score\":5,\"medal\":{\"id\":78,\"name\":\"\\\\u6211\\\\u7231\\\\u7b7e\\\\u5230\",\"src\":\"2013\\\\/0121\\\\/11\\\\/50fcaf9e58432.png\"}}');");
E_D("replace into `wb_task` values('21','2','进阶任务','2','用户等级T2以上','仅限用户等级T2以上的用户领取','{\"userlevel\":2}','',NULL,'{\"exp\":3,\"score\":3,\"medal\":null}');");
E_D("replace into `wb_task` values('22','2','进阶任务','2','微博被转发5次以上','全部微博总共被转发5次以上','{\"weibotranspost\":5}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":null}');");
E_D("replace into `wb_task` values('23','2','进阶任务','2','关注1个微吧','关注1个微吧','{\"weibafollow\":1}','',NULL,'{\"exp\":5,\"score\":5,\"medal\":null}');");
E_D("replace into `wb_task` values('24','2','进阶任务','2','在微吧发表1篇帖子','在微吧发表1篇帖子','{\"weibapost\":1}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":80,\"name\":\"\\\\u5fae\\\\u5427\\\\u5148\\\\u950b\",\"src\":\"2013\\\\/0121\\\\/11\\\\/50fcaf4b75701.png\"}}');");
E_D("replace into `wb_task` values('29','3','达人任务','2','连续签到20次','连续签到20次以上，连续签到非累计签到次数','{\"checkin\":20}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":81,\"name\":\"\\\\u7b7e\\\\u5230\\\\u8fbe\\\\u4eba\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcaf2264740.png\"}}');");
E_D("replace into `wb_task` values('30','3','达人任务','2','用户等级T4以上','仅限用户等级T4以上的用户领取','{\"userlevel\":4}','',NULL,'{\"exp\":1,\"score\":1,\"medal\":null}');");
E_D("replace into `wb_task` values('31','3','达人任务','2','与30个以上的用户互相关注','与30个以上的用户互相关注','{\"followmutual\":30}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":83,\"name\":\"\\\\u6700\\\\u4f73\\\\u4eba\\\\u7f18\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcaeb6a33e1.png\"}}');");
E_D("replace into `wb_task` values('32','3','达人任务','2','微博被转发15次以上','全部微博总共被转发15次以上','{\"weibotranspost\":15}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":null}');");
E_D("replace into `wb_task` values('33','3','达人任务','2','收到15条以上的微博评论','收到15条以上的微博评论','{\"weiboreceivecomment\":15}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":null}');");
E_D("replace into `wb_task` values('34','3','达人任务','2','向频道投稿并收录2条以上','向频道投稿并至少有2条收录到频道','{\"channelcontribute\":2}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":85,\"name\":\"\\\\u9891\\\\u9053\\\\u5148\\\\u950b\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcae64a3461.png\"}}');");
E_D("replace into `wb_task` values('35','3','达人任务','2','至少有1篇精华帖子','在微吧中至少有1篇帖子被管理员设置为精华帖','{\"weibamarrow\":1}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":86,\"name\":\"\\\\u5fae\\\\u5427\\\\u8fbe\\\\u4eba\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcae42c6f85.png\"}}');");
E_D("replace into `wb_task` values('37','4','高手任务','2','连续签到130次以上','连续签到130次以上','{\"checkin\":130}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":87,\"name\":\"\\\\u7b7e\\\\u5230\\\\u725b\\\\u4eba\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcadc03ac71.png\"}}');");
E_D("replace into `wb_task` values('38','4','高手任务','2','用户等级T6以上','仅限用户等级T6以上的用户领取','{\"userlevel\":6}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":null}');");
E_D("replace into `wb_task` values('39','4','高手任务','2','通过个人认证','仅限个人认证用户领取','{\"manager\":5}','',NULL,'{\"exp\":1,\"score\":1,\"medal\":null}');");
E_D("replace into `wb_task` values('40','4','高手任务','2','全部微博共计被转发100次以上','全部微博共计被转发100次以上','{\"weiboonetranspost\":100}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":90,\"name\":\"\\\\u610f\\\\u89c1\\\\u9886\\\\u8896\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcad1be6612.png\"}}');");
E_D("replace into `wb_task` values('41','4','高手任务','2','全部微博被评论100次以上','全部微博被评论100次以上','{\"_empty_\":100}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":91,\"name\":\"\\\\u7126\\\\u70b9\\\\u4eba\\\\u7269\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcacf5dc2af.png\"}}');");
E_D("replace into `wb_task` values('42','4','高手任务','2','向频道投稿有100条以上被收录','向频道投稿并有100条以上收录到频道','{\"channelcontribute\":100}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":92,\"name\":\"\\\\u9891\\\\u9053\\\\u52b3\\\\u6a21\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcacd15426a.png\"}}');");
E_D("replace into `wb_task` values('43','4','高手任务','2','至少有10篇精华帖子','在微吧发表10篇以上的精华帖子','{\"weibamarrow\":10}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":93,\"name\":\"\\\\u5fae\\\\u5427\\\\u725b\\\\u4eba\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcaca99174e.png\"}}');");
E_D("replace into `wb_task` values('46','5','终极任务','2','连续签到365天以上','连续签到365天以上','{\"checkin\":365}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":94,\"name\":\"\\\\u7b7e\\\\u5230\\\\u795e\\\\u4eba\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcac7c7d014.png\"}}');");
E_D("replace into `wb_task` values('47','5','终极任务','2','用户等级T9以上','仅限用户等级T9以上的用户领取','{\"userlevel\":9}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":null}');");
E_D("replace into `wb_task` values('48','5','终极任务','2','通过个人认证','仅限个人认证用户领取','{\"manager\":5}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":null}');");
E_D("replace into `wb_task` values('49','5','终极任务','2','单条微博被转发1000次','单条微博被转发1000次','{\"weiboonetranspost\":1000}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":97,\"name\":\"\\\\u64cd\\\\u76d8\\\\u624b\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcac074d87a.png\"}}');");
E_D("replace into `wb_task` values('50','5','终极任务','2','单条微博被评论1000次','单条微博被评论1000次','{\"weiboonecomment\":1000}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":98,\"name\":\"\\\\u6700\\\\u7126\\\\u70b9\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcabe10ec23.png\"}}');");
E_D("replace into `wb_task` values('51','5','终极任务','2','向频道投稿被收录1000条以上','向频道投稿并有1000条以上收录到频道','{\"channelcontribute\":1000}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":99,\"name\":\"\\\\u5fa1\\\\u7528\\\\u53d1\\\\u8a00\\\\u4eba\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcabbe9dae4.png\"}}');");
E_D("replace into `wb_task` values('53','5','终极任务','2','发表100篇以上的精华帖子','在微吧发表100篇以上的精华帖子','{\"weibamarrow\":100}','',NULL,'{\"exp\":6,\"score\":6,\"medal\":{\"id\":100,\"name\":\"\\\\u5fae\\\\u5427\\\\u795e\\\\u4eba\",\"src\":\"2013\\\\/0121\\\\/10\\\\/50fcab9980cfb.png\"}}');");

require("../../inc/footer.php");
?>