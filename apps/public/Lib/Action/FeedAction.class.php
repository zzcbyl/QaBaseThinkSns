<?php
/**
 * 提问控制器
 * @author liuxiaoqing <liuxiaoqing@zhishisoft.com>
 * @version TS3.0
 */
class FeedAction extends Action {

	/**
	 * 获取表情操作
	 * @return json 表情相关的JSON数据
	 */
	public function getSmile() {
		exit(json_encode(model('Expression')->getAllExpression()));
	}
	/**
	 * 返回好友分组列表
	 */
	public function getFriendGroup(){
		$usergroupList = model('FollowGroup')->getGroupList($this->mid);
		$grouplist = array();
		foreach ( $usergroupList as $g ){
			$group['gid'] = $g['follow_group_id'];
			$group['title'] = $g['title'];
			$grouplist[] = $group;
		}
// 		//相互关注
// 		$mutualusers = model('Follow')->getFriendsData($this->mid);
		//未分组
		$nogroupusers = model('FollowGroup')->getDefaultGroupByAll($this->mid);
		//其他分组
// 		$groupusers = array();
// 		if( $grouplist ){
// 			foreach ( $grouplist as $v ){
// 				$groupinfo = model('FollowGroup')->getUsersByGroup( $this->mid , $v['gid'] );
// 				$groupusers['group'.$v['gid']] = $groupinfo;
// 			}
// 		}
// 		$groupusers['group-1'] = getSubByKey( $mutualusers , 'fid');
		$groupusers['group-2'] = getSubByKey( $nogroupusers , 'fid' );
		$groups = array(array('gid'=>-2, 'title'=>'未分组'));
		//关注列表
		$grouplist && $groups = array_merge( $groups , $grouplist);
		$users = array();
		foreach ($groupusers as &$gu){
			foreach ( $gu as $k=>$u){
				$gu[$k] = model('User')->getUserInfoForSearch( $u , 'uid,uname');
			}
		}
		$this->assign('groups' , $groups);
		$this->assign('groupusers' , $groupusers);
		$this->display();
	}
	public function changGroup(){
		$gid = intval( $_POST['gid'] );
		$groupinfo = model('FollowGroup')->getUsersByGroup( $this->mid , $gid );
		
		$groupuser = array();
		foreach ($groupinfo as $gu){
			$groupuser[] = model('User')->getUserInfoForSearch( $gu , 'uid,uname');
		}
		$res = '<ul id="group'.$gid.'">';
		foreach ( $groupuser as $u ){
			$res .= '<li onclick=\'core.at.insertUser("'.$u['uname'].'")\'><a href="javascript:void(0);"><img alt="'.$u['uname'].'" src="'.$u['avatar_small'].'">'.$u['uname'].'</a></li>';
		}
		$res .= '</ul>';
		exit($res);
	}
	/**
	 * 发布提问操作，用于AJAX
	 * @return json 发布提问后的结果信息JSON数据
	 */
	public function PostFeed()
	{	
		// 返回数据格式
		$return = array('status'=>1, 'data'=>'');
		// 用户发送内容
		$d['content'] = isset($_POST['content']) ? filter_keyword(h($_POST['content'])) : '';
		// 原始数据内容
		$d['body'] = filter_keyword($_POST['body']);
		// 原始问题描述
		$d['description'] = filter_keyword($_POST['description']);		
		// 问题ID
		$d['questionid'] = filter_keyword($_POST['questionid']);		
		// 是否追问
		$d['isadd'] = filter_keyword($_POST['addask']);	
		$d['inviteid'] = 0;
		if(intval($_POST['inviteid']) > 0)
		{
			$d['inviteid'] = intval($_POST['inviteid']);
		}
	
		// 安全过滤
		foreach($_POST as $key => $val) {
			$_POST[$key] = t($_POST[$key]);
		}
		$d['source_url'] = urldecode($_POST['source_url']);  //应用分享到提问，原资源链接
		// 滤掉话题两端的空白
		$d['body'] = preg_replace("/#[\s]*([^#^\s][^#]*[^#^\s])[\s]*#/is",'#'.trim("\${1}").'#',$d['body']);	
		// 附件信息
		$d['attach_id'] = trim(t($_POST['attach_id']), "|");
		if ( !empty($d['attach_id']) ){
			$d['attach_id'] = explode('|', $d['attach_id']);
			array_map( 'intval' , $d['attach_id'] );
		}
		// 发送提问的类型
		$type = t($_POST['type']);
		// 所属应用名称
		$app = isset($_POST['app_name']) ? t($_POST['app_name']) : APP_NAME;			// 当前动态产生所属的应用
		if(!$data = model('Feed')->put($this->uid, $app, $type, $d)) {
			$return = array('status'=>0,'data'=>model('Feed')->getError());
			exit(json_encode($return));
		}
		
		//分享到新浪微博
		if($_POST['ShareSina'] == "1")
		{
			$WBTxt = $data['body'].'　'.$data['description'];
			$description = $WBTxt;
			if(strlen($WBTxt) > 200)
				$description = utf_substr($WBTxt, 200).'...';
			
			$WBcontent = $description.' @知心姐姐卢勤 '.SITE_URL.'/index.php%3Fapp=public%26mod=Passport%26act=newquestion%26feed_id='.$data['feed_id'];
			$this->shareWeiBo($this->mid, $data['feed_id'], $WBcontent, 'sina');
			
			//$contentTxt = utf_substr($data['description'], 180).'...　请 @卢勤问答网站 @知心姐姐卢勤 来帮帮我。'.SITE_URL.'/index.php%3Fapp=public%26mod=Passport%26act=newquestion%26feed_id='.$data['feed_id'];
			//$this->shareWeiBo($data['uid'], $data['feed_id'], $contentTxt);
		}
		
		//分享到腾讯微博
		if($_POST['ShareQQ'] == "1")
		{
			$WBTxt = $data['body'].'　'.$data['description'];
			$description = $WBTxt;
			if(strlen($WBTxt) > 200)
				$description = utf_substr($WBTxt, 200).'...';
			
			$WBcontent = $description.' @卢勤 '.SITE_URL.'/index.php%3Fapp=public%26mod=Passport%26act=newquestion%26feed_id='.$data['feed_id'];
			$this->shareWeiBo($this->mid, $data['feed_id'], $WBcontent, 'qzone');
			
			//$contentTxt = utf_substr($data['description'], 180).'...　请 @卢勤问答网站 @知心姐姐卢勤 来帮帮我。'.SITE_URL.'/index.php%3Fapp=public%26mod=Passport%26act=newquestion%26feed_id='.$data['feed_id'];
			//$this->shareWeiBo($data['uid'], $data['feed_id'], $contentTxt);
		}

		
		// 发布邮件之后添加积分
		model ( 'Credit' )->setUserCredit ( $this->uid, 'add_weibo' );
		// 提问来源设置
		$data ['from'] = getFromClient ( $data ['from'], $data ['app'] );
		$this->assign ( $data );
		// 提问配置
		$weiboSet = model ( 'Xdata' )->get ( 'admin_Config:feed' );
		$this->assign ( 'weibo_premission', $weiboSet ['weibo_premission'] );
		
		if($_POST['questionid']!=null&&$_POST['questionid']!="0")
		{
			$return ['data'] = $this->fetch('answer');
		}
		else{
			$return ['data'] = $this->fetch();
		}
		
		//填充邀请回答表中的答案ID字段
		if(intval($_POST['inviteid']) > 0)
		{
			$InviteMap['answerid'] = $data ['feed_id'];
			model('InviteAnswer')->where('invite_answer_id = '.$_POST['inviteid'])->save($InviteMap);
		}
		
		// 提问ID
		$return ['feedId'] = $data ['feed_id'];
		$return ['is_audit'] = $data ['is_audit'];
		// 添加话题
		model ( 'FeedTopic' )->addTopic ( html_entity_decode ( $d ['body'], ENT_QUOTES ), $data ['feed_id'], $type );
		// 更新用户最后发表的提问
		$last ['last_feed_id'] = $data ['feed_id'];
		$last ['last_post_time'] = $_SERVER ['REQUEST_TIME'];
		model ( 'User' )->where ( 'uid=' . $this->uid )->save ( $last );
		
		
		$isOpenChannel = model ( 'App' )->isAppNameOpen ( 'channel' );
		if (! $isOpenChannel) {
			exit ( json_encode ( $return ) );
		}
		// 添加提问到投稿数据中
		$channelId = t ( $_POST ['channel_id'] );
		
		// 绑定用户
		$bindUserChannel = D ( 'Channel', 'channel' )->getCategoryByUserBind ( $this->mid );
		if (! empty ( $bindUserChannel )) {
			$channelId = array_merge ( $bindUserChannel, explode ( ',', $channelId ) );
			$channelId = array_filter ( $channelId );
			$channelId = array_unique ( $channelId );
			$channelId = implode ( ',', $channelId );
		}
		// 绑定话题
		$content = html_entity_decode ( $d ['body'], ENT_QUOTES );
		$content = str_replace ( "＃", "#", $content );
		preg_match_all ( "/#([^#]*[^#^\s][^#]*)#/is", $content, $topics );
		$topics = array_unique ( $topics [1] );
		foreach ( $topics as &$topic ) {
			$topic = trim ( preg_replace ( "/#/", '', t ( $topic ) ) );
		}
		$bindTopicChannel = D ( 'Channel', 'channel' )->getCategoryByTopicBind ( $topics );
		if (! empty ( $bindTopicChannel )) {
			$channelId = array_merge ( $bindTopicChannel, explode ( ',', $channelId ) );
			$channelId = array_filter ( $channelId );
			$channelId = array_unique ( $channelId );
			$channelId = implode ( ',', $channelId );
		}
		if (! empty ( $channelId )) {
			D ( 'Channel', 'channel' )->setChannel ( $data ['feed_id'], $channelId, false );
		}			

		exit(json_encode($return));
	}
	
	/**
	 * 分享到新浪微博(js)
	 *
	 * @return mixed This is the return value description
	 *
	 */
	public function shareSina()
	{
		$feedid = $_POST['feed_id'];
		if(empty($feedid)||$feedid<=0)
		{
			$return = array('status'=>0,'data'=>'分享失败');
			exit(json_encode($return));
		}
		$feedData = model('feed')->getFeeds(array($feedid));
		if(!empty($feedData))
		{
			$WBTxt = $feedData[0]['body'].'　'.$feedData[0]['description'];
			$description = $WBTxt;
			if(strlen($WBTxt) > 200)
				$description = utf_substr($WBTxt, 200).'...';
			
			$WBcontent = $description.' @知心姐姐卢勤 '.SITE_URL.'/index.php%3Fapp=public%26mod=Passport%26act=newquestion%26feed_id='.$feedData[0]['feed_id'];
			$this->shareWeiBo($this->mid, $feedData[0]['feed_id'], $WBcontent);
			$return = array('status'=>1,'data'=>'分享成功');
		}
		exit(json_encode($return));
	}
	
	//分享到新浪微博
	public function shareWeiBo($uid, $feed_id, $contentTxt)
	{
		
		$loginData = model('Login')->get($uid);
		if($loginData['oauth_token'] != '')
		{
			$urlPar = 'http://sync.luqinwenda.cn/sync.aspx?oriid='.$feed_id.'&token='.$loginData['oauth_token'].'&content='.urlencode($contentTxt);
			
			// 初始化一个 cURL 对象
			$curl = curl_init();
			// 设置你需要抓取的URL
			curl_setopt($curl, CURLOPT_URL, $urlPar);
			// 设置header
			curl_setopt($curl, CURLOPT_HEADER, 1);
			// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			// 运行cURL，请求网页
			$dhtml = curl_exec($curl);
			// 关闭URL请求
			curl_close($curl);
		}
	}
	
	
	
	/**
	 * 分享/转发提问操作，需要传入POST的值
	 * @return json 分享/转发提问后的结果信息JSON数据
	 */
	public function shareFeed()
	{
		// 获取传入的值
		$post = $_POST;
		// 安全过滤
		foreach($post as $key => $val) {
			$post[$key] = t($post[$key]);
		}
		// 过滤内容值
		$post['body'] = filter_keyword($post['body']);
				
		// 判断资源是否删除
		if(empty($post['curid'])) {
			$map['feed_id'] = $post['sid'];
		} else {
			$map['feed_id'] = $post['curid'];
		}
		$map['is_del'] = 0;
		$isExist = model('Feed')->where($map)->count();
		if($isExist == 0) {
			$return['status'] = 0;
			$return['data'] = '内容已被删除，转发失败';
			exit(json_encode($return));
		}

		// 进行分享操作
		$return = model('Share')->shareFeed($post, 'share');
		if($return['status'] == 1) {
			$app_name = $post['app_name'];

			// 添加积分
			if($app_name == 'public'){
				model('Credit')->setUserCredit($this->uid,'forward_weibo');
				//提问被转发
				$suid =  model('Feed')->where($map)->getField('uid');
				model('Credit')->setUserCredit($suid,'forwarded_weibo');
			}
			if($app_name == 'weiba'){
				model('Credit')->setUserCredit($this->uid,'forward_topic');
				//提问被转发
				$suid =  D('Feed')->where('feed_id='.$map['feed_id'])->getField('uid');
				model('Credit')->setUserCredit($suid,'forwarded_topic');
			}
			
			$this->assign($return['data']);
			// 提问配置
			$weiboSet = model('Xdata')->get('admin_Config:feed');
			$this->assign('weibo_premission', $weiboSet['weibo_premission']);
			$return['data'] =  $this->fetch('PostFeed');
		}
		exit(json_encode($return));
	}

	/**
	 * 删除提问操作，用于AJAX
	 * @return json 删除提问后的结果信息JSON数据
	 */	
	public function removeFeed() {
		$return = array('status'=>0,'data'=>L('PUBLIC_DELETE_FAIL'));			// 删除失败
		$feed_id = intval($_POST['feed_id']);
		$feed = model('Feed')->getFeedInfo($feed_id);
		// 不存在时
		if(!$feed){
			exit(json_encode($return));
		}
		// 非作者时
		if($feed['uid']!=$this->mid){
			// 没有管理权限不可以删除
			if(!CheckPermission('core_admin','feed_del')){
				exit(json_encode($return));
			}
		// 是作者时
		}else{
			// 没有前台权限不可以删除
			if(!CheckPermission('core_normal','feed_del')){
				exit(json_encode($return));
			}
		}
		// 执行删除操作		
		$return = model('Feed')->doEditFeed($feed_id, 'delFeed', '',$this->mid);
		// 删除失败或删除成功的消息
		$return['data'] = ($return['status'] == 0) ? L('PUBLIC_DELETE_FAIL') : L('PUBLIC_DELETE_SUCCESS');		
		// 批注：下面的代码应该挪到FeedModel中
		// 删除话题相关信息
		$return['status'] == 1 && model('FeedTopic')->deleteWeiboJoinTopic($feed_id);
		// 删除频道关联信息
		D('Channel', 'channel')->deleteChannelLink($feed_id);
		// 删除@信息
		model('Atme')->setAppName('Public')->setAppTable('feed')->deleteAtme(null, $feed_id, null);
		//删除话题信息
		$topics = D('feed_topic_link')->where('feed_id='.$feed_id)->field('topic_id')->findAll();
		D('feed_topic_link')->where('feed_id='.$feed)->delete();
		$tpmap['topic_id'] = array( 'in' , getSubByKey( $topics , 'topic_id' ) );
		model('FeedTopic')->where($tpmap)->setDec('count');
		exit(json_encode($return));
	}
	
	function addDigg(){
		$feed_id = intval($_POST['feed_id']);
		$res = model('FeedDigg')->addDigg($feed_id, $this->mid);
		if($res){
			echo 1;
		}else{
			echo 0;
		}
		
	}
}