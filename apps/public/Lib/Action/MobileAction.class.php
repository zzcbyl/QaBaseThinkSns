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
}