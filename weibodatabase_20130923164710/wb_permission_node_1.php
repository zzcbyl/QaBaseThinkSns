<?php
require("../../inc/header.php");

/*
		SoftName : EmpireBak Version 2010
		Author   : wm_chief
		Copyright: Powered by www.phome.net
*/

DoSetDbChar('utf8');
E_D("DROP TABLE IF EXISTS `wb_permission_node`;");
E_C("CREATE TABLE `wb_permission_node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appname` varchar(50) NOT NULL COMMENT '应用名称',
  `appinfo` varchar(50) NOT NULL COMMENT '应用说明',
  `module` varchar(50) NOT NULL COMMENT '模块名称',
  `rule` varchar(50) NOT NULL COMMENT '权限类型',
  `ruleinfo` varchar(50) NOT NULL COMMENT '权限名称',
  PRIMARY KEY (`id`),
  KEY `rule` (`rule`)
) ENGINE=MyISAM AUTO_INCREMENT=433 DEFAULT CHARSET=utf8");
E_D("replace into `wb_permission_node` values('2','core','核心','normal','feed_post','发表微博');");
E_D("replace into `wb_permission_node` values('3','core','核心','normal','feed_comment','评论微博');");
E_D("replace into `wb_permission_node` values('4','core','核心','normal','feed_report','举报微博');");
E_D("replace into `wb_permission_node` values('5','core','核心','normal','feed_share','分享微博');");
E_D("replace into `wb_permission_node` values('6','core','核心','admin','feed_del','删除微博');");
E_D("replace into `wb_permission_node` values('7','core','核心','admin','comment_del','删除评论');");
E_D("replace into `wb_permission_node` values('8','core','核心','admin','message_del','删除私信');");
E_D("replace into `wb_permission_node` values('9','core','核心','admin','admin_login','登录后台');");
E_D("replace into `wb_permission_node` values('415','core','核心','normal','feed_del','前台删除微博');");
E_D("replace into `wb_permission_node` values('416','core','核心','normal','comment_del','删除评论');");
E_D("replace into `wb_permission_node` values('417','core','核心','normal','search_info','大搜索');");
E_D("replace into `wb_permission_node` values('418','core','核心','normal','send_message','发私信');");
E_D("replace into `wb_permission_node` values('419','core','核心','normal','read_data','浏览资料');");
E_D("replace into `wb_permission_node` values('420','core','核心','normal','invite_user','邀请用户');");
E_D("replace into `wb_permission_node` values('421','weiba','微吧','normal','weiba_post','微吧发帖');");
E_D("replace into `wb_permission_node` values('422','weiba','微吧','normal','weiba_reply','微吧回帖');");
E_D("replace into `wb_permission_node` values('423','weiba','微吧','normal','weiba_del','微吧删帖');");
E_D("replace into `wb_permission_node` values('424','weiba','微吧','normal','weiba_del_reply','微吧删除回帖');");
E_D("replace into `wb_permission_node` values('425','weiba','微吧','normal','weiba_edit','微吧编辑帖子');");
E_D("replace into `wb_permission_node` values('426','weiba','微吧','normal','weiba_apply_manage','申请吧主');");
E_D("replace into `wb_permission_node` values('427','weiba','微吧','admin','weiba_recommend','微吧推荐帖子');");
E_D("replace into `wb_permission_node` values('428','weiba','微吧','admin','weiba_top','微吧置顶');");
E_D("replace into `wb_permission_node` values('429','weiba','微吧','admin','weiba_marrow','微吧精华');");
E_D("replace into `wb_permission_node` values('430','weiba','微吧','admin','weiba_global_top','微吧全局置顶');");
E_D("replace into `wb_permission_node` values('431','weiba','微吧','admin','weiba_del','微吧删除帖子');");
E_D("replace into `wb_permission_node` values('432','weiba','微吧','admin','weiba_edit','微吧编辑帖子');");
E_D("replace into `wb_permission_node` values('10','channel','频道','admin','channel_recommend','推荐频道');");
E_D("replace into `wb_permission_node` values('11','core','核心','normal','feed_audit','先审后发');");

require("../../inc/footer.php");
?>