<?php
/**
 * ProfileAction 个人档案模块
 * @author  liuxiaoqing <liuxiaoqing@zhishisoft.com>
 * @version TS3.0
 */
class ProfileAction extends Action {
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
	 * 个人档案展示页面
	 */
	public function index() {
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
		$this->_tab_menu();
		
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {
			$this->_sidebar ();
			// 加载微博筛选信息
			$d ['feed_type'] = t ( $_REQUEST ['feed_type'] ) ? t ( $_REQUEST ['feed_type'] ) : '';
			$d ['feed_key'] = t ( $_REQUEST ['feed_key'] ) ? t ( $_REQUEST ['feed_key'] ) : '';
			$d ['type'] = t ( $_REQUEST ['type'] ) ? t ( $_REQUEST ['type'] ) : 'following';
			$this->assign ( $d );
		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		
		// 添加积分
		model ( 'Credit' )->setUserCredit ( $this->uid, 'space_access' );
		
		$this->assign ( 'userPrivacy', $userPrivacy );
		// seo
		$seo = model ( 'Xdata' )->get ( "admin_Config:seo_user_profile" );
		$replace ['uname'] = $user_info ['uname'];
		if ($feed_id = model ( 'Feed' )->where ( 'uid=' . $this->uid )->order ( 'publish_time desc' )->limit ( 1 )->getField ( 'feed_id' )) {
			$replace ['lastFeed'] = D ( 'feed_data' )->where ( 'feed_id=' . $feed_id )->getField ( 'feed_content' );
		}
		$replaces = array_keys ( $replace );
		foreach ( $replaces as &$v ) {
			$v = "{" . $v . "}";
		}
		$seo ['title'] = str_replace ( $replaces, $replace, $seo ['title'] );
		$seo ['keywords'] = str_replace ( $replaces, $replace, $seo ['keywords'] );
		$seo ['des'] = str_replace ( $replaces, $replace, $seo ['des'] );
		! empty ( $seo ['title'] ) && $this->setTitle ( $seo ['title'] );
		! empty ( $seo ['keywords'] ) && $this->setKeywords ( $seo ['keywords'] );
		! empty ( $seo ['des'] ) && $this->setDescription ( $seo ['des'] );
		$this->display ();
	}
	
	/**
	 *个人档案的回答列表
	 */
	public function answer()
	{
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
		$this->_tab_menu();
		
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {
			$this->_sidebar ();
			// 加载微博筛选信息
			$d ['feed_type'] = t ( $_REQUEST ['feed_type'] ) ? t ( $_REQUEST ['feed_type'] ) : '';
			$d ['feed_key'] = t ( $_REQUEST ['feed_key'] ) ? t ( $_REQUEST ['feed_key'] ) : '';
			$this->assign ( $d );
		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		
		// 添加积分
		model ( 'Credit' )->setUserCredit ( $this->uid, 'space_access' );
		
		$this->assign ( 'userPrivacy', $userPrivacy );
		// seo
		$seo = model ( 'Xdata' )->get ( "admin_Config:seo_user_profile" );
		$replace ['uname'] = $user_info ['uname'];
		if ($feed_id = model ( 'Feed' )->where ( 'uid=' . $this->uid )->order ( 'publish_time desc' )->limit ( 1 )->getField ( 'feed_id' )) {
			$replace ['lastFeed'] = D ( 'feed_data' )->where ( 'feed_id=' . $feed_id )->getField ( 'feed_content' );
		}
		$replaces = array_keys ( $replace );
		foreach ( $replaces as &$v ) {
			$v = "{" . $v . "}";
		}
		$seo ['title'] = str_replace ( $replaces, $replace, $seo ['title'] );
		$seo ['keywords'] = str_replace ( $replaces, $replace, $seo ['keywords'] );
		$seo ['des'] = str_replace ( $replaces, $replace, $seo ['des'] );
		! empty ( $seo ['title'] ) && $this->setTitle ( $seo ['title'] );
		! empty ( $seo ['keywords'] ) && $this->setKeywords ( $seo ['keywords'] );
		! empty ( $seo ['des'] ) && $this->setDescription ( $seo ['des'] );
		$this->display ();
	}
	
	
	/**
	 *个人档案的问题列表
	 */
	public function question()
	{
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
		$this->_tab_menu();
		
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {
			$this->_sidebar ();
			// 加载微博筛选信息
			$d ['feed_type'] = t ( $_REQUEST ['feed_type'] ) ? t ( $_REQUEST ['feed_type'] ) : '';
			$d ['feed_key'] = t ( $_REQUEST ['feed_key'] ) ? t ( $_REQUEST ['feed_key'] ) : '';
			$this->assign ( $d );
		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		
		// 添加积分
		model ( 'Credit' )->setUserCredit ( $this->uid, 'space_access' );
		
		$this->assign ( 'userPrivacy', $userPrivacy );
		// seo
		$seo = model ( 'Xdata' )->get ( "admin_Config:seo_user_profile" );
		$replace ['uname'] = $user_info ['uname'];
		if ($feed_id = model ( 'Feed' )->where ( 'uid=' . $this->uid )->order ( 'publish_time desc' )->limit ( 1 )->getField ( 'feed_id' )) {
			$replace ['lastFeed'] = D ( 'feed_data' )->where ( 'feed_id=' . $feed_id )->getField ( 'feed_content' );
		}
		$replaces = array_keys ( $replace );
		foreach ( $replaces as &$v ) {
			$v = "{" . $v . "}";
		}
		$seo ['title'] = str_replace ( $replaces, $replace, $seo ['title'] );
		$seo ['keywords'] = str_replace ( $replaces, $replace, $seo ['keywords'] );
		$seo ['des'] = str_replace ( $replaces, $replace, $seo ['des'] );
		! empty ( $seo ['title'] ) && $this->setTitle ( $seo ['title'] );
		! empty ( $seo ['keywords'] ) && $this->setKeywords ( $seo ['keywords'] );
		! empty ( $seo ['des'] ) && $this->setDescription ( $seo ['des'] );
		$this->display ();
	}
	
	
	function appList() {
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
		$this->_assignUserInfo ( $this->uid );
		
		$appArr = $this->_tab_menu();
		$type = t ( $_GET ['type'] );
		if (! isset ( $appArr [$type] )) {
			$this->error ( '参数出错！！' );
		}
		$this->assign('type', $type);
		$className = ucfirst ( $type ) . 'Protocol';
		$content = D ( $className, $type )->profileContent ( $this->uid );
		if(empty($content)){
			$content = '暂无内容';
		}
		$this->assign ( 'content', $content );
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {
			$this->_sidebar ();
			// 档案类型
			$ProfileType = model ( 'UserProfile' )->getCategoryList ();
			$this->assign ( 'ProfileType', $ProfileType );
			// 个人资料
			$this->_assignUserProfile ( $this->uid );
			// 获取用户职业信息
			$userCategory = model ( 'UserCategory' )->getRelatedUserInfo ( $this->uid );
			if (! empty ( $userCategory )) {
				foreach ( $userCategory as $value ) {
					$user_category .= '<a href="#" class="link btn-cancel"><span>' . $value ['title'] . '</span></a>&nbsp;&nbsp;';
				}
			}
			$this->assign ( 'user_category', $user_category );
		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		$this->assign ( 'userPrivacy', $userPrivacy );
		$this->setTitle ( $user_info ['uname'] . '的'.L ( 'PUBLIC_APPNAME_' . $type ) );
		$this->setKeywords ( $user_info ['uname'] . '的'.L ( 'PUBLIC_APPNAME_' . $type ) );
		$user_tag = model ( 'Tag' )->setAppName ( 'User' )->setAppTable ( 'user' )->getAppTags ( array (
				$this->uid
		) );
		$this->setDescription ( t ( $user_category . $user_info ['location'] . ',' . implode ( ',', $user_tag [$this->uid] ) . ',' . $user_info ['intro'] ) );
		
		
		$this->display ();
	}
	
	/**
	 * 获取指定应用的信息
	 * 
	 * @return void
	 */
	public function appprofile() {
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		
		$d ['widgetName'] = ucfirst ( t ( $_GET ['appname'] ) ) . 'Profile';
		foreach ( $_GET as $k => $v ) {
			$d ['widgetAttr'] [$k] = t ( $v );
		}
		$d ['widgetAttr'] ['widget_appname'] = t ( $_GET ['appname'] );
		$this->assign ( $d );
		
		$this->_assignUserInfo ( array (
				$this->uid 
		) );
		($this->mid != $this->uid) && $this->_assignFollowState ( $this->uid );
		$this->display ();
	}
	
	/**
	 * 获取用户详细资料
	 * 
	 * @return void
	 */
	public function data() {
		/*if (! CheckPermission ( 'core_normal', 'read_data' ) && $this->uid != $this->mid) {
			$this->error ( '对不起，您没有权限浏览该内容!' );
		}*/
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
		/*print('<br /><br /><br /><br />');
		print_r($state);*/
		
		$this->assign ( 'followstate', $followState );
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ();
		$this->_tab_menu();
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {
			$this->_sidebar ();
			// 档案类型
			$ProfileType = model ( 'UserProfile' )->getCategoryList ();
			$this->assign ( 'ProfileType', $ProfileType );
			// 个人资料
			$this->_assignUserProfile ( $this->uid );
			// 获取用户职业信息
			$userCategory = model ( 'UserCategory' )->getRelatedUserInfo ( $this->uid );
			if (! empty ( $userCategory )) {
				foreach ( $userCategory as $value ) {
					$user_category .= '<a href="#" class="link btn-cancel"><span>' . $value ['title'] . '</span></a>&nbsp;&nbsp;';
				}
			}
			$this->assign ( 'user_category', $user_category );
		} else {
			$this->_assignUserInfo ( $this->uid );
		}
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
	 * 获取指定用户的某条动态
	 * 
	 * @return void
	 */
	public function feed() {
		
		$feed_id = intval ( $_GET ['feed_id'] );
		
		if (empty ( $feed_id )) {
			$this->error ( L ( 'PUBLIC_INFO_ALREADY_DELETE_TIPS' ) );
		}
	
		//获取微博信息
		$feedInfo = model ( 'Feed' )->get ( $feed_id );

		if (!$feedInfo){
			$this->error ( '该微博不存在或已被删除' );
			exit();
		}
			
		if ($feedInfo ['is_audit'] == '0' && $feedInfo ['uid'] != $this->mid) {
			$this->error ( '此微博正在审核' );
			exit();
		}

		if ($feedInfo ['is_del'] == '1') {
			$this->error ( L ( 'PUBLIC_NO_RELATE_WEIBO' ) );
			exit();
		}
		
		//判断用户是否已经回答过
		$feedlist = model ( 'Feed' )->getAnswerList('feed_questionid='.$feed_id.' and uid='.$GLOBALS['ts']['mid'].' and is_del = 0 and (is_audit=1 OR is_audit=0)');
		if(is_array($feedlist) && is_array($feedlist['data']) && count($feedlist['data'])>0)
		{
			$this->assign ( 'hasAnswer', '1' );
		}

		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $feedInfo['uid'] );
		
		// 个人空间头部
		$this->_top ();
		
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {
			$this->_sidebar ();		
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
		// seo
		$feedContent = unserialize ( $feedInfo ['feed_data'] );
		$seo = model ( 'Xdata' )->get ( "admin_Config:seo_feed_detail" );
		$replace ['content'] = $feedContent ['content'];
		$replace ['uname'] = $feedInfo ['user_info'] ['uname'];
		$replaces = array_keys ( $replace );
		foreach ( $replaces as &$v ) {
			$v = "{" . $v . "}";
		}
		$seo ['title'] = str_replace ( $replaces, $replace, $seo ['title'] );
		$seo ['keywords'] = str_replace ( $replaces, $replace, $seo ['keywords'] );
		$seo ['des'] = str_replace ( $replaces, $replace, $seo ['des'] );
		! empty ( $seo ['title'] ) && $this->setTitle ( $seo ['title'] );
		! empty ( $seo ['keywords'] ) && $this->setKeywords ( $seo ['keywords'] );
		! empty ( $seo ['des'] ) && $this->setDescription ( $seo ['des'] );
		$this->assign ( 'userPrivacy', $userPrivacy );
		// 赞功能
		$diggArr = model ( 'FeedDigg' )->checkIsDigg ( $feed_id, $this->mid );
		$this->assign ( 'diggArr', $diggArr );
		
		$this->display ();
	}
	
	/**
	 * 获取用户关注列表
	 * 
	 * @return void
	 */
	public function following() {
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
			$this->_sidebar ();
			
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
			$this->_assignUserTag ( $uids );
			$this->_assignUserCount ( $fids );
			// 关注分组
			($this->mid == $this->uid) && $this->_assignFollowGroup ( $fids );
			
			$this->assign ( 'following_list', $following_list );
		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		$this->assign ( 'userPrivacy', $userPrivacy );
		
		$this->setTitle ( L ( 'PUBLIC_TA_FOLLOWING', array (
				'user' => $GLOBALS ['ts'] ['_user'] ['uname'] 
		) ) );
		$this->setKeywords ( L ( 'PUBLIC_TA_FOLLOWING', array (
				'user' => $GLOBALS ['ts'] ['_user'] ['uname'] 
		) ) );
		$this->display ();
	}
	
	/**
	 * 获取用户粉丝列表
	 * 
	 * @return void
	 */
	public function follower() {
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
			$this->_sidebar ();
			
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
			$this->_assignUserTag ( $uids );
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
		
		$this->setTitle ( L ( 'PUBLIC_TA_FOLLWER', array (
				'user' => $GLOBALS ['ts'] ['_user'] ['uname'] 
		) ) );
		$this->setKeywords ( L ( 'PUBLIC_TA_FOLLWER', array (
				'user' => $GLOBALS ['ts'] ['_user'] ['uname'] 
		) ) );
		$this->display ();
	}
	
	
	/**
	* 获取用户好友列表
	* 
	* @return void
	*/
	public function friend() {
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
			$this->_sidebar ();
			
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
			$this->_assignUserTag ( $uids );
			$this->_assignUserCount ( $fids );
			// 关注分组
			($this->mid == $this->uid) && $this->_assignFollowGroup ( $fids );
			$this->assign ( 'following_list', $following_list );
		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		$this->assign ( 'userPrivacy', $userPrivacy );
		
		$this->setTitle ( L ( 'PUBLIC_TA_FOLLOWING', array (
			'user' => $GLOBALS ['ts'] ['_user'] ['uname'] 
			) ) );
		$this->setKeywords ( L ( 'PUBLIC_TA_FOLLOWING', array (
			'user' => $GLOBALS ['ts'] ['_user'] ['uname'] 
			) ) );
		$this->display ();
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
	 * 根据指定应用和表获取指定用户的标签
	 * 
	 * @param
	 *        	array uids 用户uid数组
	 * @return void
	 */
	private function _assignUserTag($uids) {
		$user_tag = model ( 'Tag' )->setAppName ( 'User' )->setAppTable ( 'user' )->getAppTags ( $uids );
		$this->assign ( 'user_tag', $user_tag );
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
	 * 获取用户最后一条微博数据
	 * 
	 * @param
	 *        	mix uids 用户uid
	 * @param
	 *        	void
	 */
	private function _assignUserLastFeed($uids) {
		return true; // 目前不需要这个功能
		$last_feed = model ( 'Feed' )->getLastFeed ( $uids );
		$this->assign ( 'last_feed', $last_feed );
	}
	
	/**
	 * 调整分组列表
	 * 
	 * @param array $fids
	 *        	指定用户关注的用户列表
	 * @return void
	 */
	private function _assignFollowGroup($fids) {
		$follow_group_list = model ( 'FollowGroup' )->getGroupList ( $this->mid );
		// 调整分组列表
		if (! empty ( $follow_group_list )) {
			$group_count = count ( $follow_group_list );
			for($i = 0; $i < $group_count; $i ++) {
				if ($follow_group_list [$i] ['follow_group_id'] != $data ['gid']) {
					$follow_group_list [$i] ['title'] = (strlen ( $follow_group_list [$i] ['title'] ) + mb_strlen ( $follow_group_list [$i] ['title'], 'UTF8' )) / 2 > 8 ? getShort ( $follow_group_list [$i] ['title'], 3 ) . '...' : $follow_group_list [$i] ['title'];
				}
				if ($i < 2) {
					$data ['follow_group_list_1'] [] = $follow_group_list [$i];
				} else {
					if ($follow_group_list [$i] ['follow_group_id'] == $data ['gid']) {
						$data ['follow_group_list_1'] [2] = $follow_group_list [$i];
						continue;
					}
					$data ['follow_group_list_2'] [] = $follow_group_list [$i];
				}
			}
			if (empty ( $data ['follow_group_list_1'] [2] ) && ! empty ( $data ['follow_group_list_2'] [0] )) {
				$data ['follow_group_list_1'] [2] = $data ['follow_group_list_2'] [0];
				unset ( $data ['follow_group_list_2'] [0] );
			}
		}
		
		$data ['follow_group_status'] = model ( 'FollowGroup' )->getGroupStatusByFids ( $this->mid, $fids );
		
		$this->assign ( $data );
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
	 * 个人主页标签导航
	 *
	 * @return void
	 */
	public function _tab_menu() {
		// 取全部APP信息
		$appList = model ( 'App' )->where ( 'status=1' )->field ( 'app_name' )->findAll ();
		foreach ( $appList as $app ) {
			$appName = strtolower ( $app ['app_name'] );
			$className = ucfirst ( $appName );
			
			$dao = D ( $className . 'Protocol', strtolower($className), false );
			if (method_exists ( $dao, 'profileContent' )) {
				$appArr [$appName] = L ( 'PUBLIC_APPNAME_' . $appName );
			}
			unset ( $dao );
		}
		$this->assign ( 'appArr', $appArr );
		
		return $appArr;
	}	
	
	/**
	 * 个人主页右侧
	 * 
	 * @return void
	 */
	public function _sidebar() {
		// 判断用户是否已认证
		$isverify = D ( 'user_verified' )->where ( 'verified=1 AND uid=' . $this->uid )->find ();
		if ($isverify) {
			$this->assign ( 'verifyInfo', $isverify ['info'] );
		}
		// 加载用户标签信息
		$this->_assignUserTag ( array (
				$this->uid 
		) );
		// 加载好友列表
		$sidebar_following_list = model ( 'Follow' )->getFriendList ( $this->uid, 12 );
		$fids=array();
		$i=0;
		foreach($sidebar_following_list['data'] as $v => $vv)
		{
			$fids[$i]=$vv['fid'];
			$i++;
		}
		$this->_assignUserInfo ( $fids, 'following_user_info' );		
		$this->assign ( 'sidebar_following_list', $sidebar_following_list );
		// dump($sidebar_following_list);exit;
		// 加载共同关注列表
		$sidebar_follower_list = model ( 'Follow' )->getCommonFollowingList ($this->mid, $this->uid, 12 );
		$fids=array();
		$i=0;
		foreach($sidebar_follower_list['data'] as $v => $vv)
		{
			$fids[$i]=$vv['fid'];
			$i++;
		}
		$this->_assignUserInfo ( $fids, 'follower_user_info' );
		$this->assign ( 'sidebar_follower_list', $sidebar_follower_list );
		// 加载用户信息
		$uids = array (
				$this->uid 
		);

		$followingfids = getSubByKey ( $sidebar_following_list ['data'], 'fid' );
		$followingfids && $uids = array_merge ( $uids, $followingfids );
		
		$followerfids = getSubByKey ( $sidebar_follower_list ['data'], 'fid' );
		$followerfids && $uids = array_merge ( $uids, $followerfids );

		$this->_assignUserInfo ( $uids );
		
		$where =" `uid` = ".$this->uid." AND `feed_questionid` != 0 and `comment_count` > 0";
		$list = model('Feed')->getFeedListByComment($where, 10);
		$this->assign ( 'aggreelist', $list );
		/*print('<br /><br /><br /><br />');
		print_r($list);*/
		
	}
	
	/**
	 * 统计数据
	 *
	 * @return void
	 *
	 */	
	public function _countdata()
	{
		// 获取用户统计信息
		$userData = model ( 'UserData' )->getUserData ( $this->uid );
		$this->assign ( 'userData', $userData );
	}
	
	/**
	 * 收藏列表
	 *
	 * @return void
	 *	
	*/
	public function collection()
	{
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
			$this->_sidebar ();

		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		$this->display ();
	}
	
	/**
	* 新答案列表
	*
	* @return void
	*	
	*/
	public function newanswerinfo()
	{
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->mid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		
		// 清空新回答提醒数字
		if($this->uid == $this->mid){
			$udata = model('UserData')->getUserData($this->mid);
			$this->assign ( 'new_answer_count', $udata['new_answer_count']);
			$udata['new_answer_count'] > 0 && model('UserData')->setKeyValue($this->mid,'new_answer_count',0);	
		}
		
		// 个人空间头部
		$this->_top ();
		
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {
			$this->_sidebar ();

		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		$this->display ();
	}
	
	/**
	* 新评论列表
	*
	* @return void
	*	
	*/
	public function newcommentinfo()
	{
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		
		// 清空新评论提醒数字
		if($this->uid == $this->mid){
			$udata = model('UserData')->getUserData($this->mid);
			$this->assign ( 'new_comment_count', $udata['new_comment_count']);
			$udata['new_comment_count'] > 0 && model('UserData')->setKeyValue($this->mid,'new_comment_count',0);	
		}
		
		// 个人空间头部
		$this->_top ();
		
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {
			$this->_sidebar ();

		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		$this->display ();
	}
	
	
	/**
	* 新赞同列表
	*
	* @return void
	*	
	*/
	public function newagreeinfo()
	{
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		
		// 清空新赞同提醒数字
		if($this->uid == $this->mid){
			$udata = model('UserData')->getUserData($this->mid);
			$this->assign ( 'new_comment_agree_count', $udata['new_comment_agree_count']);
			$udata['new_comment_agree_count'] > 0 && model('UserData')->setKeyValue($this->mid,'new_comment_agree_count',0);	
		}
		
		// 个人空间头部
		$this->_top ();
		
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {
			$this->_sidebar ();

		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		$this->display ();
	}
	
	
	/**
	* 新反对列表
	*
	* @return void
	*	
	*/
	public function newopposeinfo()
	{
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $this->uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		
		// 清空新反对提醒数字
		if($this->uid == $this->mid){
			$udata = model('UserData')->getUserData($this->mid);
			$this->assign ( 'new_comment_oppose_count', $udata['new_comment_oppose_count']);
			$udata['new_comment_oppose_count'] > 0 && model('UserData')->setKeyValue($this->mid,'new_comment_oppose_count',0);	
		}
		
		// 个人空间头部
		$this->_top ();
		
		// 判断隐私设置
		$userPrivacy = $this->privacy ( $this->uid );
		if ($userPrivacy ['space'] !== 1) {
			$this->_sidebar ();

		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		$this->display ();
	}
	
	/**
	* 活动信息列表
	*
	* @return void
	*	
	*/
	public function newactivityinfo()
	{
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
			$this->_sidebar ();

		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		$this->display ();
	}
	/**
	* 新增粉丝列表
	*
	* @return void
	*	
	*/
	public function newfollower()
	{
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
			$this->_sidebar ();

		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		
		// 清空新粉丝提醒数字
		$newCount=0;
		if($this->uid == $this->mid){
			$udata = model('UserData')->getUserData($this->mid);
			$newCount=$udata['new_folower_count'];
			$udata['new_folower_count'] > 0 && model('UserData')->setKeyValue($this->mid,'new_folower_count',0);	
		}
		// 获取用户的粉丝列表
		$followerList = model('Follow')->getFollowerList($this->mid, 20);
		$fids = getSubByKey($followerList['data'], 'fid');
		// 获取用户信息
		$followerUserInfo = model('User')->getUserInfoByUids($fids);
		// 获取用户统计数目
		$userData = model('UserData')->getUserDataByUids($fids);
		// 获取用户标签
		$this->_assignUserTag($fids);
		// 获取用户用户组信息
		$userGroupData = model('UserGroupLink')->getUserGroupData($fids);
		$this->assign('userGroupData',$userGroupData);
		// 获取用户的最后微博数据
		//$lastFeedData = model('Feed')->getLastFeed($fids);
		// 获取用户的关注信息状态
		$followState = model('Follow')->getFollowStateByFids($this->mid, $fids);
		// 组装数据
		$index=0;
		foreach($followerList['data'] as $key => $value) {
			$followerList['data'][$key] = array_merge($followerList['data'][$key], $followerUserInfo[$value['fid']]);
			$followerList['data'][$key] = array_merge($followerList['data'][$key], $userData[$value['fid']]);
			$followerList['data'][$key] = array_merge($followerList['data'][$key], array('feedInfo'=>$lastFeedData[$value['fid']]));
			$followerList['data'][$key] = array_merge($followerList['data'][$key], array('followState'=>$followState[$value['fid']]));
			if($index<$newCount)
			{
				$followerList['data'][$key] = array_merge($followerList['data'][$key], array('newCount'=>1));
				$index++;
			}
		}
		//print_r($followerList);
		$this->assign($followerList);
		
		$this->display ();
	}
	/**
	* 新增好友列表
	*
	* @return void
	*	
	*/
	public function newfriend()
	{
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
			$this->_sidebar ();

		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		
		// 清空新好友提醒数字
		$newCount=0;
		if($this->uid == $this->mid){
			$udata = model('UserData')->getUserData($this->mid);
			$newCount=$udata['new_friend_count'];
			$udata['new_friend_count'] > 0 && model('UserData')->setKeyValue($this->mid,'new_friend_count',0);	
		}
		// 获取用户的好友列表
		$followerList = model('Follow')->getFriendList($this->mid, 20);
		$fids = getSubByKey($followerList['data'], 'fid');
		// 获取用户信息
		$followerUserInfo = model('User')->getUserInfoByUids($fids);
		// 获取用户统计数目
		$userData = model('UserData')->getUserDataByUids($fids);
		// 获取用户标签
		$this->_assignUserTag($fids);
		// 获取用户用户组信息
		$userGroupData = model('UserGroupLink')->getUserGroupData($fids);
		$this->assign('userGroupData',$userGroupData);
		// 获取用户的最后微博数据
		//$lastFeedData = model('Feed')->getLastFeed($fids);
		// 获取用户的关注信息状态
		$followState = model('Follow')->getFollowStateByFids($this->mid, $fids);
		// 组装数据
		$index=0;
		foreach($followerList['data'] as $key => $value) {
			$followerList['data'][$key] = array_merge($followerList['data'][$key], $followerUserInfo[$value['fid']]);
			$followerList['data'][$key] = array_merge($followerList['data'][$key], $userData[$value['fid']]);
			$followerList['data'][$key] = array_merge($followerList['data'][$key], array('feedInfo'=>$lastFeedData[$value['fid']]));
			$followerList['data'][$key] = array_merge($followerList['data'][$key], array('followState'=>$followState[$value['fid']]));
			if($index<$newCount)
			{
				$followerList['data'][$key] = array_merge($followerList['data'][$key], array('newCount'=>1));
				$index++;
			}
		}
		$this->assign($followerList);
		
		
		$this->display ();
	}
	
	/**
	* 邀请我的列表
	*
	* @return void
	*	
	*/
	public function invite()
	{
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
			$this->_sidebar ();

		} else {
			$this->_assignUserInfo ( $this->uid );
		}
		$this->display ();
	}
}