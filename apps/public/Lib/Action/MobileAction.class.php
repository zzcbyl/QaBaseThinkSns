<?php
/**
 * 手机页面
 * @author zhangzc
 * @version TS3.0
 */
class MobileAction extends Action
{
	/**
	* _initialize 模块初始化
	* 
	* @return void
	*/
	protected function _initialize() {
		// 短域名判断
		if (! isset ( $_GET ['uid'] ) || empty ( $_GET ['uid'] )) {
			$this->uid = $this->mid;
		} elseif (is_numeric ( $_GET ['uid'] )) {
			$this->uid = intval ( $_GET ['uid'] );
		} else {
			$map ['domain'] = t ( $_GET ['uid'] );
			$this->uid = model ( 'User' )->where ( $map )->getField ( 'uid' );
		}
		$this->assign ( 'uid', $this->uid );
	}
	
	public function all()
	{
		// 安全过滤
		$d['type'] = t($_GET['type']) ? t($_GET['type']) : 'all';
		$d['feed_type'] = t($_GET['feed_type']) ? t($_GET['feed_type']) : '';
		$d['feed_key'] = t($_GET['feed_key']) ? t($_GET['feed_key']) : '';
		// 设置标题与关键字信息
		switch($d['type']) {
			case 'question':
				$this->setTitle('我的问题');
				$this->setKeywords('我的问题');
				break;
			case 'answer':
				$this->setTitle('我的答案');
				$this->setKeywords('我的答案');
				break;
			case 'following':
				$this->setTitle('我的关注');
				$this->setKeywords('我的关注');
				break;
			case 'channel':
				$this->setTitle('我关注的频道');
				$this->setKeywords('我关注的频道');
				break;
			case 'inviteme':
				$this->setTitle('邀请我的');
				$this->setKeywords('邀请我的');
				break;
			default:
				$this->setTitle('我的首页');
				$this->setKeywords('我的首页');
		}
		$this->assign($d);
		
		$this->display();
	}
	
	/**
	* 获取指定用户的某条动态
	* 
	* @return void
	*/
	public function feed() {
		
		$feed_id = intval ( $_GET ['feed_id'] );
		
		if (empty($feed_id)) {
			$this->error( L ( 'PUBLIC_INFO_ALREADY_DELETE_TIPS' ) );
		}
		
		$invite_id = intval($_GET['invite_id']);
		$this->assign('inviteid', $invite_id);
		
		//增加浏览数
		model ('Feed')->UpdatePV($feed_id);
		
		//获取提问信息
		$feedInfo = model ( 'Feed' )->get ( $feed_id );

		if (!$feedInfo){
			$this->error ( '该提问不存在或已被删除' );
			exit();
		}
		
		if ($feedInfo ['is_audit'] == '0' && $feedInfo ['uid'] != $this->mid) {
			$this->error ( '此提问正在审核' );
			exit();
		}

		if ($feedInfo ['is_del'] == '1') {
			$this->error ( L ( 'PUBLIC_NO_RELATE_WEIBO' ) );
			exit();
		}
		
		//判断用户是否已经回答过
		$feedlist = model ( 'Feed' )->getAnswerList('feed_questionid='.$feed_id.' and uid='.$GLOBALS['ts']['mid'].' and is_del = 0 and (is_audit=1 OR is_audit=0)');
		if((is_array($feedlist) && is_array($feedlist['data']) && count($feedlist['data'])>0) || ($feedInfo['uid']==$this->mid))
		{
			$this->assign ( 'hasAnswer', '1' );
		}

		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $feedInfo['uid'] );
		
		// 个人空间头部
		//$this->_top ();
		
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {
			//$this->_sidebar ();	
			
			$weiboSet = model ( 'Xdata' )->get ( 'admin_Config:feed' );
			$a ['initNums'] = $weiboSet ['weibo_nums'];
			$a ['weibo_type'] = $weiboSet ['weibo_type'];
			$a ['weibo_premission'] = $weiboSet ['weibo_premission'];
			$this->assign ( $a );
			switch ($feedInfo ['app']) {
				case 'weiba' :
					$feedInfo ['from'] = getFromClient ( 0, $feedInfo ['app'], '微吧' );
					break;
				default :
					$feedInfo ['from'] = getFromClient ( $from, $feedInfo ['app'] );
					break;
			}
			// $feedInfo['from'] = getFromClient( $feedInfo['from'] , $feedInfo['app']);
			$this->assign ( 'feedInfo', $feedInfo );
		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		
		//认证专家
		$uids = model('UserGroupLink')->getUserByGroupID(8);
		$user_count = model ( 'UserData' )->getUserDataByUids ($uids);
		$authenticateExpert = model('user')->getUserInfoByUids($uids);
		//print_r($authenticateExpert);
		$this->assign ( 'authenticateExpert_UserCount', $user_count );
		$this->assign('authenticateExpert',$authenticateExpert);	
		
		//获取追问信息
		$addFeed =	model('feed')->getQuestionList('add_feedid = '.$feed_id.' and is_del = 0 and (is_audit=1 OR is_audit=0)');
		$this->assign('addquestionlist', $addFeed['data']);
		
		$loginData = model('Login')->get($GLOBALS['ts']['mid'], 'sina');
		if($loginData['oauth_token'] != '')
		{
			$this->assign('token', '1');
		}
		
		$loginData = model('Login')->get($GLOBALS['ts']['mid'], 'qzone');
		if($loginData['oauth_token'] != '')
		{
			$this->assign('qqtoken', '1');
		}
		
		$this->setTitle($feedInfo['body']);
		$this->setKeywords($feedInfo['body']);
		
		$this->display ();
	}
	
	/**
	 * 隐私设置
	 */
	public function privacy($uid) {
		if ($this->mid != $uid) {
			$privacy = model ( 'UserPrivacy' )->getPrivacy ( $this->mid, $uid );
			return $privacy;
		} else {
			return true;
		}
	}
	
	/**
	* 获取用户的档案信息和资料配置信息
	* 
	* @param
	*        	mix uids 用户uid
	* @return void
	*/
	private function _assignUserProfile($uids) {
		$data ['user_profile'] = model ( 'UserProfile' )->getUserProfileByUids ( $uids );
		$data ['user_profile_setting'] = model ( 'UserProfile' )->getUserProfileSetting ( array (
			'visiable' => 1 
			) );
		// 用户选择处理 uid->uname
		foreach ( $data ['user_profile_setting'] as $k => $v ) {
			if ($v ['form_type'] == 'selectUser') {
				$field_ids [] = $v ['field_id'];
			}
			if ($v ['form_type'] == 'selectDepart') {
				$field_departs [] = $v ['field_id'];
			}
		}
		foreach ( $data ['user_profile'] as $ku => &$uprofile ) {
			foreach ( $uprofile as $key => $val ) {
				if (in_array ( $val ['field_id'], $field_ids )) {
					$user_info = model ( 'User' )->getUserInfo ( $val ['field_data'] );
					$uprofile [$key] ['field_data'] = $user_info ['uname'];
				}
				if (in_array ( $val ['field_id'], $field_departs )) {
					$depart_info = model ( 'Department' )->getDepartment ( $val ['field_data'] );
					$uprofile [$key] ['field_data'] = $depart_info ['title'];
				}
			}
		}
		$this->assign ( $data );
	}
	
	/**
	* 批量获取用户的相关信息加载
	* 
	* @param string|array $uids
	*        	用户ID
	*/
	private function _assignUserInfo($uids, $tplname='user_info') {
		! is_array ( $uids ) && $uids = explode ( ',', $uids );
		$user_info = model ( 'User' )->getUserInfoByUids ( $uids );
		$this->assign ( $tplname, $user_info );
		// dump($user_info);exit;
	}
	
	/**
	* 批量获取用户uid与一群人fids的彼此关注状态
	* 
	* @param array $fids
	*        	用户uid数组
	* @return void
	*/
	private function _assignFollowState($fids = null) {
		// 批量获取与当前登录用户之间的关注状态
		$follow_state = model ( 'Follow' )->getFollowStateByFids ( $this->mid, $fids );
		$this->assign ( 'follow_state', $follow_state );
		// dump($follow_state);exit;
	}
	
	/**
	* 批量获取多个用户的统计数目
	* 
	* @param array $uids
	*        	用户uid数组
	* @return void
	*/
	private function _assignUserCount($uids) {
		$user_count = model ( 'UserData' )->getUserDataByUids ( $uids );
		$this->assign ( 'user_count', $user_count );
	}
	
	/**
	* 个人主页头部数据
	* 
	* @return void
	*/
	public function _top() {
		// 获取用户组信息
		$userGroupData = model ( 'UserGroupLink' )->getUserGroupData ( $this->uid );
		$this->assign ( 'userGroupData', $userGroupData );
		// 获取用户积分信息
		$userCredit = model ( 'Credit' )->getUserCredit ( $this->uid );
		$this->assign ( 'userCredit', $userCredit );
		// 加载用户关注信息
		($this->mid != $this->uid) && $this->_assignFollowState ( $this->uid );
		// 获取用户统计信息
		$userData = model ( 'UserData' )->getUserData ( $this->uid );
		$this->assign ( 'userData', $userData );
	}
	
	/**
	* 个人档案展示页面
	*/
	public function personal() {
		$followState = 0; //没关系
		if($this->uid == $this->mid)
		{
			$followState=1; //自己
		}
		$state = model('Follow')->getFollowState($this->mid, $this->uid);
		if($state['follower']==1)
		{
			$followState=2;//关注的人
		}
		
		$this->assign ( 'followstate', $followState );
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
		$this->_assignUserInfo ( $this->uid );
		
		$this->assign ( 'userPrivacy', $userPrivacy );
		
		$this->setTitle ( $user_info ['uname'] . '的资料' );
		$this->setKeywords ( $user_info ['uname'] . '的资料' );
		$user_tag = model ( 'Tag' )->setAppName ( 'User' )->setAppTable ( 'user' )->getAppTags ( array (
			$this->uid 
			) );
		$this->setDescription ( t ( $user_category . $user_info ['location'] . ',' . implode ( ',', $user_tag [$this->uid] ) . ',' . $user_info ['intro'] ) );
		$this->display ();
	}
	
	
	/**
	* 获取指定用户的某条评论
	* 
	* @return void
	*/
	public function comment() {
		$feed_id = intval ( $_GET ['feed_id'] );
		
		if (empty($feed_id)) {
			$this->error( L ( 'PUBLIC_INFO_ALREADY_DELETE_TIPS' ) );
		}

		//获取提问信息
		$feedInfo = model ( 'Feed' )->get ( $feed_id );

		if (!$feedInfo){
			$this->error ( '该提问不存在或已被删除' );
			exit();
		}
		
		if ($feedInfo ['is_audit'] == '0' && $feedInfo ['uid'] != $this->mid) {
			$this->error ( '此提问正在审核' );
			exit();
		}

		if ($feedInfo ['is_del'] == '1') {
			$this->error ( L ( 'PUBLIC_NO_RELATE_WEIBO' ) );
			exit();
		}
		//print_r($feedInfo);
		$this->assign ( 'feedInfo', $feedInfo );
		
		$this->setTitle('评论－'.$feedInfo['body']);
		$this->setKeywords('评论－'.$feedInfo['body']);
		
		$this->display ();
	}
	
	
	/**
	* 获取指定用户的某条赞同评论
	* 
	* @return void
	*/
	public function agree() {
		$feed_id = intval ( $_GET ['feed_id'] );
		
		if (empty($feed_id)) {
			$this->error( L ( 'PUBLIC_INFO_ALREADY_DELETE_TIPS' ) );
		}

		//获取提问信息
		$feedInfo = model ( 'Feed' )->get ( $feed_id );

		if (!$feedInfo){
			$this->error ( '该提问不存在或已被删除' );
			exit();
		}
		
		if ($feedInfo ['is_audit'] == '0' && $feedInfo ['uid'] != $this->mid) {
			$this->error ( '此提问正在审核' );
			exit();
		}

		if ($feedInfo ['is_del'] == '1') {
			$this->error ( L ( 'PUBLIC_NO_RELATE_WEIBO' ) );
			exit();
		}
		//print_r($feedInfo);
		$this->assign ( 'feedInfo', $feedInfo );
		
		$this->setTitle('赞同评论－'.$feedInfo['body']);
		$this->setKeywords('赞同评论－'.$feedInfo['body']);
		
		$this->display ();
	}
	
	/**
	* 获取指定用户的某条反对评论
	* 
	* @return void
	*/
	public function oppose() {
		$feed_id = intval ( $_GET ['feed_id'] );
		
		if (empty($feed_id)) {
			$this->error( L ( 'PUBLIC_INFO_ALREADY_DELETE_TIPS' ) );
		}

		//获取提问信息
		$feedInfo = model ( 'Feed' )->get ( $feed_id );

		if (!$feedInfo){
			$this->error ( '该提问不存在或已被删除' );
			exit();
		}
		
		if ($feedInfo ['is_audit'] == '0' && $feedInfo ['uid'] != $this->mid) {
			$this->error ( '此提问正在审核' );
			exit();
		}

		if ($feedInfo ['is_del'] == '1') {
			$this->error ( L ( 'PUBLIC_NO_RELATE_WEIBO' ) );
			exit();
		}
		//print_r($feedInfo);
		$this->assign ( 'feedInfo', $feedInfo );
		
		$this->setTitle('反对评论－'.$feedInfo['body']);
		$this->setKeywords('反对评论－'.$feedInfo['body']);
		
		$this->display ();
	}
	
	
	/**
	* 个人问题页面
	*/
	public function questionlist() {
		$followState = 0; //没关系
		if($this->uid == $this->mid)
		{
			$followState=1; //自己
		}
		$state = model('Follow')->getFollowState($this->mid, $this->uid);
		if($state['follower']==1)
		{
			$followState=2;//关注的人
		}
		
		$this->assign ( 'followstate', $followState );
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
		$this->_assignUserInfo ( $this->uid );
		
		$this->assign ( 'userPrivacy', $userPrivacy );
		
		$this->setTitle ( $user_info ['uname'] . '的提问' );
		$this->setKeywords ( $user_info ['uname'] . '的提问' );
		$user_tag = model ( 'Tag' )->setAppName ( 'User' )->setAppTable ( 'user' )->getAppTags ( array (
			$this->uid 
			) );
		$this->setDescription ( t ( $user_category . $user_info ['location'] . ',' . implode ( ',', $user_tag [$this->uid] ) . ',' . $user_info ['intro'] ) );
		$this->display ();
	}
	
	/**
	* 个人回答页面
	*/
	public function answerlist() {
		$followState = 0; //没关系
		if($this->uid == $this->mid)
		{
			$followState=1; //自己
		}
		$state = model('Follow')->getFollowState($this->mid, $this->uid);
		if($state['follower']==1)
		{
			$followState=2;//关注的人
		}
		
		$this->assign ( 'followstate', $followState );
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
		$this->_assignUserInfo ( $this->uid );
		
		$this->assign ( 'userPrivacy', $userPrivacy );
		
		$this->setTitle ( $user_info ['uname'] . '的回答' );
		$this->setKeywords ( $user_info ['uname'] . '的回答' );
		$user_tag = model ( 'Tag' )->setAppName ( 'User' )->setAppTable ( 'user' )->getAppTags ( array (
			$this->uid 
			) );
		$this->setDescription ( t ( $user_category . $user_info ['location'] . ',' . implode ( ',', $user_tag [$this->uid] ) . ',' . $user_info ['intro'] ) );
		$this->display ();
	}
	
	/**
	* 个人赞同页面
	*/
	public function agreelist() {
		$followState = 0; //没关系
		if($this->uid == $this->mid)
		{
			$followState=1; //自己
		}
		$state = model('Follow')->getFollowState($this->mid, $this->uid);
		if($state['follower']==1)
		{
			$followState=2;//关注的人
		}
		
		$this->assign ( 'followstate', $followState );
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
		$this->_assignUserInfo ( $this->uid );
		
		$this->assign ( 'userPrivacy', $userPrivacy );
		
		$this->setTitle ( $user_info ['uname'] . '获得的赞同' );
		$this->setKeywords ( $user_info ['uname'] . '获得的赞同' );
		$user_tag = model ( 'Tag' )->setAppName ( 'User' )->setAppTable ( 'user' )->getAppTags ( array (
			$this->uid 
			) );
		$this->setDescription ( t ( $user_category . $user_info ['location'] . ',' . implode ( ',', $user_tag [$this->uid] ) . ',' . $user_info ['intro'] ) );
		$this->display ();
	}
	
	
	/**
	* 个人反对页面
	*/
	public function opposelist() {
		$followState = 0; //没关系
		if($this->uid == $this->mid)
		{
			$followState=1; //自己
		}
		$state = model('Follow')->getFollowState($this->mid, $this->uid);
		if($state['follower']==1)
		{
			$followState=2;//关注的人
		}
		
		$this->assign ( 'followstate', $followState );
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
		$this->_assignUserInfo ( $this->uid );
		
		$this->assign ( 'userPrivacy', $userPrivacy );
		
		$this->setTitle ( $user_info ['uname'] . '获得的反对' );
		$this->setKeywords ( $user_info ['uname'] . '获得的反对' );
		$user_tag = model ( 'Tag' )->setAppName ( 'User' )->setAppTable ( 'user' )->getAppTags ( array (
			$this->uid 
			) );
		$this->setDescription ( t ( $user_category . $user_info ['location'] . ',' . implode ( ',', $user_tag [$this->uid] ) . ',' . $user_info ['intro'] ) );
		$this->display ();
	}
	
	
	/**
	* 个人感谢页面
	*/
	public function thanklist() {
		$followState = 0; //没关系
		if($this->uid == $this->mid)
		{
			$followState=1; //自己
		}
		$state = model('Follow')->getFollowState($this->mid, $this->uid);
		if($state['follower']==1)
		{
			$followState=2;//关注的人
		}
		
		$this->assign ( 'followstate', $followState );
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
		$this->_assignUserInfo ( $this->uid );
		
		$this->assign ( 'userPrivacy', $userPrivacy );
		
		$this->setTitle ( $user_info ['uname'] . '获得的感谢' );
		$this->setKeywords ( $user_info ['uname'] . '获得的感谢' );
		$user_tag = model ( 'Tag' )->setAppName ( 'User' )->setAppTable ( 'user' )->getAppTags ( array (
			$this->uid 
			) );
		$this->setDescription ( t ( $user_category . $user_info ['location'] . ',' . implode ( ',', $user_tag [$this->uid] ) . ',' . $user_info ['intro'] ) );
		$this->display ();
	}
	
	
	/**
	* 关注列表
	*/
	public function followlist() {
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {			
			$following_list = model ( 'Follow' )->getFollowingList ( $this->uid, t ( $_GET ['gid'] ), 20 );

			$fids = getSubByKey ( $following_list ['data'], 'fid' );
			if ($fids) {
				$uids = array_merge ( $fids, array (
					$this->uid 
					) );
			} else {
				$uids = array (
					$this->uid 
					);
			}
			// 获取用户组信息
			$userGroupData = model ( 'UserGroupLink' )->getUserGroupData ( $uids );
			$this->assign ( 'userGroupData', $userGroupData );
			$this->_assignFollowState ( $uids );
			$this->_assignUserInfo ( $uids );
			$this->_assignUserProfile ( $uids );
			//$this->_assignUserTag ( $uids );
			$this->_assignUserCount ( $fids );
			// 关注分组
			//($this->mid == $this->uid) && $this->_assignFollowGroup ( $fids );
			
			$this->assign ( 'following_list', $following_list );
		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		$this->assign ( 'userPrivacy', $userPrivacy );
		
		$this->setTitle ( $user_info['uname'].'的关注' );
		$this->setKeywords ($user_info['uname'].'的关注');
		$this->display ();
	}
	
	
	
	/**
	* 粉丝列表
	*/
	public function followerlist() {
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {
			$follower_list = model ( 'Follow' )->getFollowerList ( $this->uid, 20 );
			$fids = getSubByKey ( $follower_list ['data'], 'fid' );
			if ($fids) {
				$uids = array_merge ( $fids, array (
						$this->uid 
				) );
			} else {
				$uids = array (
						$this->uid 
				);
			}
			// 获取用户用户组信息
			$userGroupData = model ( 'UserGroupLink' )->getUserGroupData ( $uids );
			$this->assign ( 'userGroupData', $userGroupData );
			$this->_assignFollowState ( $uids );
			$this->_assignUserInfo ( $uids );
			$this->_assignUserProfile ( $uids );
			//$this->_assignUserTag ( $uids );
			$this->_assignUserCount ( $fids );
			// 更新查看粉丝时间
			if ($this->uid == $this->mid) {
				$t = time () - intval ( $GLOBALS ['ts'] ['_userData'] ['view_follower_time'] ); // 避免服务器时间不一致
				model ( 'UserData' )->setUid ( $this->mid )->updateKey ( 'view_follower_time', $t, true );
			}
			$this->assign ( 'follower_list', $follower_list );
		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		$this->assign ( 'userPrivacy', $userPrivacy );
		
		$this->setTitle ( $user_info['uname'].'的粉丝' );
		$this->setKeywords ($user_info['uname'].'的粉丝');
		$this->display ();
	}
	
	/**
	* 好友列表
	*/
	public function friendlist() {
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
	
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {
			$following_list = model ( 'Follow' )->getFriendList ( $this->uid, 20 );
			$fids = getSubByKey ( $following_list ['data'], 'fid' );
			if ($fids) {
				$uids = array_merge ( $fids, array (
					$this->uid 
					) );
			} else {
				$uids = array (
					$this->uid 
					);
			}
			// 获取用户组信息
			$userGroupData = model ( 'UserGroupLink' )->getUserGroupData ( $uids );
			$this->assign ( 'userGroupData', $userGroupData );
			$this->_assignFollowState ( $uids );
			$this->_assignUserInfo ( $uids );
			$this->_assignUserProfile ( $uids );
			//$this->_assignUserTag ( $uids );
			$this->_assignUserCount ( $fids );
			// 关注分组
			//($this->mid == $this->uid) && $this->_assignFollowGroup ( $fids );
			
			//print_r($following_list);
			$this->assign ( 'following_list', $following_list );
		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		$this->assign ( 'userPrivacy', $userPrivacy );
		
		$this->setTitle ( $user_info['uname'].'的好友' );
		$this->setKeywords ($user_info['uname'].'的好友');
		$this->display ();
	}
	
	public function object_array($array) {  
		if(is_object($array)) {  
			$array = (array)$array;  
		} if(is_array($array)) {  
			foreach($array as $key=>$value) {  
				$array[$key] = $this->object_array($value);  
			}  
		}  
		return $array;  
	} 
	
	public function curls($url, $timeout = '1000')
	{
		// 1. 初始化
		$ch = curl_init();
		// 2. 设置选项，包括URL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// 3. 执行并获取HTML文档内容
		$info = curl_exec($ch);
		// 4. 释放curl句柄
		curl_close($ch);

		return $info;
	}
	
	/**
	* 解析json串
	* @param type $json_str
	* @return type
	*/
	function analyJson($json_str) {
		$json_str = str_replace('＼＼', '', $json_str);
		$out_arr = array();
		preg_match('/{.*}/', $json_str, $out_arr);
		if (!empty($out_arr)) {
			$result = json_decode($out_arr[0], TRUE);
		} else {
			return FALSE;
		}
		return $result;
	}
	
	/** 搜索页面
	*/
	public function search()
	{
		$idlist = '';
		if($_POST['keywork'])
		{
			$url = 'http://api.luqinwenda.com/s.aspx?key='.$_POST['keywork'];
			$Result = $this->curls($url);

			$jsonArr = $this->analyJson($Result);
			
			if(intval($jsonArr['count'])>0)
			{
				for($i = 0; $i < count($jsonArr['items']); $i++)
				{
					$idlist .= $jsonArr['items'][$i]['_id'].',';
				}
			}
			$this->assign ( 'word', $_POST['keywork'] );
		}
		$this->assign ( 'idlist', $idlist );
		$this->display ();
	}
	
	/** 朋友圈添加主题
	*/
	public function topicadd()
	{
		if($_POST['upload']==1)
		{
			$ImgURL='';
			if(empty($_POST['content']) && empty($_FILES['fileselect']['name'][0]))
			{
				$this->assign('errorInfo','发布内容不能为空');
				$this->display();
				return;
			}
			else
			{
				$this->assign('errorInfo','');
				
				if(is_array($_FILES['fileselect']) && !empty($_FILES['fileselect']['name'][0]))
				{
					$imgArr = $_FILES['fileselect'];
					$errorInfo = '';
					foreach($imgArr['size'] as $key=>$value)
					{

						if(strpos($imgArr['type'][$key],'image') === false)
						{
							$errorInfo .= '文件"' . $imgArr['name'][$key] . '"不是图片<br />';
						}
						if($value>500000)
						{
							$errorInfo .= '您这张"' . $imgArr['name'][$key] . '"图片大小过大，应小于500k<br />';
						}
					}
					
					if($errorInfo!='')
					{
						$this->assign('errorInfo',$errorInfo);
						$this->display();
						return;
					}

					foreach($imgArr['name'] as $key=>$value)
					{
						$exname=strtolower(substr($imgArr['name'][$key],(strrpos($imgArr['name'][$key],'.')+1)));

						$uploadfile = $this->getname($exname); 
						$ImgURL .= $uploadfile.';';
						move_uploaded_file($imgArr['tmp_name'][$key], SITE_PATH . C('PYQ_IMAGE').$uploadfile);
						
						$original_file_name ='/pyqimage/'.$uploadfile;
						$thumbInfo = getThumbImage($original_file_name,50,50,true,true);
					}
				}
				
				$map['topic_content'] = $_POST['content'];// str_replace(array("\r\n", "\r", "\n"),"<br />",$_POST['content']);
				$map['topic_dt'] = time();
				$map['topic_img'] = $ImgURL;
				$map['topic_uid'] = $this->mid;
				$map['topic_state'] = 1;
				
				$data = model('Topic')->add($map);
				//print_r($data);
				$this->redirect('public/Mobile/topiclist');
			}
		}
		
		$this->display();
	}
	
	public function getname($exname){
		$fileName = time().rand(1,10000000).'.'.$exname;
		$dir = SITE_PATH . C('PYQ_IMAGE');
		if(!is_dir($dir)){
			mkdir($dir,0777);
		}
		while(true){
			if(!is_file($dir.$fileName)){
				$name=$fileName;
				break;
			}
			$fileName = time().rand(1,10000000).'.'.$exname;
		}
		return $name;
	}
	
	public function mytopic()
	{
		$userid = $this->mid;
		if(!empty($_GET['uid']))
		{
			$userid	 = $_GET['uid'];
		}
		$this->assign('userid',$userid);
		$this->display();
	}
	
	public function topiccomment()
	{
		if(empty($_GET['topicid']))
		{
			$this->error ( '参数错误' );
		}
		$topicid = $_GET['topicid'];
		//朋友圈内容
		$data = model('topic')->getTopicByID($topicid);
		$this->assign('topicData',$data['data'][0]);
		//评论内容
		$where['comment_topicid'] = $topicid;
		$where['comment_state'] = 1;
		$commentList = model('TopicComment')->getCommentList($where, 20);
		$this->assign('commentList',$commentList);
		$this->display();
		
	}
	
	public function addPYQComment()
	{
		// 返回数据格式
		$return = array('status'=>1, 'data'=>'');
		$topicid = $_GET['topicID'];
		$content = $_GET['commentContent'];
		$parentid = $_GET['parentID'];
		if(empty($topicid)||empty($content))
		{
			$return = array('status'=>0,'data'=>'参数错误');
			exit(json_encode($return));
		}
		
		$map['comment_content'] = $content;
		$map['comment_uid'] = $this->mid;
		$map['comment_topicid'] = $topicid;
		$map['comment_parentid'] = $parentid;
		$map['comment_dt'] = time();
		$map['comment_state'] = 1;
		
		$data = model('TopicComment')->add($map);
		//修改评论数
		model('topic')->where('topic_id='.$topicid)->setInc('topic_commentcount');
		//获取最新评论的数据
		$where['comment_id'] = $data;
		$commentData = model('TopicComment')->getCommentList($where);
		$this->assign ( 'commentvl', $commentData['data'][0] );
		$return ['data'] = $this->fetch();
		//$return = array('status'=>1,'data'=>$commentData);
		exit(json_encode($return));
	}
	
	public function delPYQComment()
	{
		// 返回数据格式
		$return = array('status'=>1, 'data'=>'');
		$commentid = $_GET['commentID'];
		if(empty($commentid))
		{
			$return = array('status'=>0,'data'=>'参数错误');
			exit(json_encode($return));
		}
		
		$map['comment_state'] = 2;
		$data = model('TopicComment')->where('`comment_id`='.$commentid.' and `comment_uid` = '.$GLOBALS['ts']['mid'])->save($map);
		if($data>0)
		{
			$return['data'] = $data;
		}
		else
		{
			$return = array('status'=>0,'data'=>'删除失败');
		}
		
		exit(json_encode($return));
	}
}