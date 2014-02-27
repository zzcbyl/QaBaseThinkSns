<?php
/**
 * PassportAction 通行证模块
 * @author  liuxiaoqing <liuxiaoqing@zhishisoft.com>
 * @version TS3.0
 */
class PassportAction extends Action 
{
	var $passport;

	/**
	 * 模块初始化
	 * @return void
	 */
	protected function _initialize() {
		$this->passport = model('Passport');
	}

	/**
	 * 通行证首页
	 * @return void
	 */
	public function index() {
		
		// 如果设置了登录前的默认应用
		// U('welcome','',true);
		// 如果没设置
		$this->login();
	}

	/**
	 * 默认登录页
	 * @return void
	 */
	public function login(){
		// 添加样式
		$this->appCssList[] = 'login.css';
		if(model('Passport')->isLogged()){
			U('public/Index/index','',true);
		}

		// 获取邮箱后缀
		$registerConf = model('Xdata')->get('admin_Config:register');
		$this->assign('emailSuffix', explode(',', $registerConf['email_suffix']));
		$this->assign( 'register_type' , $registerConf['register_type']);
		$data= model('Xdata')->get("admin_Config:seo_login");
        !empty($data['title']) && $this->setTitle($data['title']);
        !empty($data['keywords']) && $this->setKeywords($data['keywords']);
        !empty($data['des'] ) && $this->setDescription ( $data ['des'] );
		
		$login_bg = getImageUrlByAttachId( $this->site ['login_bg'] );
		if(empty($login_bg))
			$login_bg = APP_PUBLIC_URL . '/image/body-bg2.jpg';
        
        $this->assign('login_bg', $login_bg);
		
		//最新问题
		$Qwhere=' is_del = 0 AND feed_questionid=0 AND add_feedid=0 AND (is_audit=1 OR is_audit=0) ';
		$newfeedList = model('feed')->getQuestionList($Qwhere,3);
		//print_r($newfeedList);
		$this->assign('newfeedList', $newfeedList);
		
		//最新用户
		$NewUserList = model('User')->getList(array(),12,'uid', 'ctime desc');
		$uids = getSubByKey($NewUserList, 'uid');
		$NewUserInfoList = model('User')->getUserInfoByUids($uids);
		$this->assign('NewUserList', $NewUserInfoList);
        
		$this->display('login');
	}
	
	/**
	 * 快速登录
	 */
	public function quickLogin(){
		$registerConf = model('Xdata')->get('admin_Config:register');
		$this->assign( 'register_type' , $registerConf['register_type']);
		$this->display();
	}

	/**
	 * 用户登录
	 * @return void
	 */
	public function doLogin() {
		$login 		= t($_POST['login_email']);
		$password 	= trim($_POST['login_password']);
		$remember	= intval($_POST['login_remember']);
		if($login == '用户名')
			$login = '';
		if($password == '******')
			$password = '';
		
		$result 	= $this->passport->loginLocal($login,$password,$remember);
		if(!$result){
			$status = 0; 
			$info	= $this->passport->getError();
			$data 	= 0;
		}else{
			$status = 1;
			$info 	= $this->passport->getSuccess();
			$data 	= ($GLOBALS['ts']['site']['home_url'])?$GLOBALS['ts']['site']['home_url']:0;
		}
		$this->ajaxReturn($data,$info,$status);
	}	
	
	/**
	 * 注销登录
	 * @return void
	 */
	public function logout() {
		$this->passport->logoutLocal();
		$this->assign('jumpUrl',U('public/Passport/login'));
		$this->success('退出成功！');
	}

	/**
	 * 找回密码页面
	 * @return void
	 */
	public function findPassword() {

		// 添加样式
		$this->appCssList[] = 'login.css';

		$this->display();
	}

	/**
	 * 通过安全问题找回密码
	 * @return void
	 */
	public function doFindPasswordByQuestions() {
		$this->display();
	}

	/**
	 * 通过Email找回密码
	 */
	public function doFindPasswordByEmail() {
		$_POST["email"]	= t($_POST["email"]);
		if(!$this->_isEmailString($_POST['email'])) {
			$this->error(L('PUBLIC_EMAIL_TYPE_WRONG'));
		}

		$user =	model("User")->where('`email`="'.$_POST["email"].'"')->find();
        if(!$user) {
        	$this->error('找不到该邮箱注册信息');
        } 

        $result = $this->_sendPasswordEmail($user);
		if($result){
			$this->success('发送成功，请注意查收邮件');
		}else{
			$this->error('操作失败，请重试');
		}
	}

	/**
	 * 找回密码页面
	 */
	private function _sendPasswordEmail($user) {
		if($user['uid']) {
	    	$this->appCssList[] = 'login.css';		// 添加样式
	        $code = md5($user["uid"].'+'.$user["password"].'+'.rand(1111,9999));
	        $config['reseturl'] = U('public/Passport/resetPassword', array('code'=>$code));
	        $config['login'] = $user['login'];
	        //设置旧的code过期
	        D('FindPassword')->where('uid='.$user["uid"])->setField('is_used',1);
	        //添加新的修改密码code
	        $add['uid'] = $user['uid'];
	        $add['email'] = $user['email'];
	        $add['code'] = $code;
	        $add['is_used'] = 0;
	        $result = D('FindPassword')->add($add);
	        if($result){
	    		model('Notify')->sendNotify($user['uid'], 'password_reset', $config);
				return true;
			}else{
				return false;
			}
	    }
	}

	public function doFindPasswordByEmailAgain(){
		$_POST["email"]	= t($_POST["email"]);
		$user =	model("User")->where('`email`="'.$_POST["email"].'"')->find();		
        if(!$user) {
        	$this->error('找不到该邮箱注册信息');
        } 

        $result = $this->_sendPasswordEmail($user);
		if($result){
			$this->success('发送成功，请注意查收邮件');
		}else{
			$this->error('操作失败，请重试');
		}
	}

	/**
	 * 通过手机短信找回密码
	 * @return void
	 */
	public function doFindPasswordBySMS() {
		$this->display();
	}

	/**
	 * 重置密码页面
	 * @return void
	 */
	public function resetPassword() {
		$code = t($_GET['code']);
		$this->_checkResetPasswordCode($code);
		$this->assign('code', $code);
		$this->display();
	}

	/**
	 * 执行重置密码操作
	 * @return void
	 */
	public function doResetPassword() {
		$code = t($_POST['code']);
		$user_info = $this->_checkResetPasswordCode($code);

		$password = trim($_POST['password']);
		$repassword = trim($_POST['repassword']);
		if(!model('Register')->isValidPassword($password, $repassword)){
			$this->error(model('Register')->getLastError());
		}

		$map['uid'] = $user_info['uid'];
		$data['login_salt'] = rand(10000,99999);
		$data['password']   = md5(md5($password) . $data['login_salt']);
		$res = model('User')->where($map)->save($data);
		if ($res) {
			D('find_password')->where('uid='.$user_info['uid'])->setField('is_used',1);
			model('User')->cleanCache($user_info['uid']);
			$this->assign('jumpUrl', U('public/Passport/login'));
			//$config['newpass'] = $password;
			$config['login'] = $user_info['login'];
			model('Notify')->sendNotify($user_info['uid'],'password_setok',$config);
			//$mail = model('Mail')->sendSysEmail($user_info['email'],'resetPassOk',array(),array('newpass'=>$password));
			$this->success(L('PUBLIC_PASSWORD_RESET_SUCCESS'));
		} else {
			$this->error(L('PUBLIC_PASSWORD_RESET_FAIL'));
		}
	}

	/**
	 * 检查重置密码的验证码操作
	 * @return void
	 */
	private function _checkResetPasswordCode($code) {
		$map['code'] = $code;
		$map['is_used'] = 0;
		$uid = D('find_password')->where($map)->getField('uid');
		if(!$uid){
			$this->assign('jumpUrl',U('public/Passport/findPassword'));
			$this->error('重置密码链接已失效，请重新找回');
		}
		$user_info = model('User')->where("`uid`={$uid}")->find();

		if (!$user_info) {
			$this->redirect = U('public/Passport/login');
		}

		return $user_info;
	}

	/*
	 * 验证安全邮箱
	 * @return void
	 */
	public function doCheckEmail() {
		$email = t($_POST['email']);
		if($this->_isEmailString($email)){
			die(1);			
		}else{
			die(0);
		}
	}

	/*
	 * 正则匹配，验证邮箱格式
	 * @return integer 1=成功 ""=失败
	 */
	private function _isEmailString($email) {
		return preg_match("/[_a-zA-Z\d\-\.]+@[_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+$/i", $email) !== 0;
	}
	
	
	
	/**
	* 广场
	* @return void
	*/
	public function square(){
		//热门问题
		$where =' (`is_audit`=1 OR `is_audit`=0) AND `is_del` = 0 AND `feed_questionid`=0 AND `add_feedid` = 0  ';
		$list = model('Feed')->getList($where,10,'answer_count desc, publish_time desc');
		$HotTopicList = $list['data'];
		$this->assign('HotTopicList', $HotTopicList);
		
		//热心用户
		$map['key'] = 'answer_count';
		$list = model('UserData')->getlist($map,' `value` desc,`uid` desc ');
		$userinfo=array();
		foreach($list as $k => $v)
		{
			$user = model('user')->getUserInfo($v['uid']);
			$user['userdata'] = $v;
			$userinfo[$k] = $user;
		}
		$this->assign('HotUserList', $userinfo);
		//print_r($userinfo);
		
		//最新问题
		$NewQuestion = model('Feed')->getQuestionList($where, 10);
		$this->assign('NewQuestion', $NewQuestion['data']);
		//print_r($NewQuestion['data']);
		
		//今日十大关注问题
		$FQWhere = ' (`is_audit`=1 OR `is_audit`=0) AND `is_del` = 0 AND `feed_questionid`=0 AND `add_feedid` = 0  ';
		$FollowingQuestion = model('Feed')->getQuestionList($where, 10, '`publish_time` desc, `feed_pv` desc, `feed_id` desc');
		$this->assign('FollowingQuestion', $FollowingQuestion['data']);
		//print_r($FollowingQuestion['data']);
		
		//最新用户
		$userwhere='is_del = 0 and is_audit = 1 and is_active = 1 and is_init = 1';
		$NewUserList = model('User')->getList($userwhere,6,'uid');
		$uids = getSubByKey($NewUserList, 'uid');
		$NewUserInfoList = model('User')->getUserInfoByUids($uids);
		$this->assign('NewUserList', $NewUserInfoList);
		//print_r($NewUserInfoList);
		
		//得到邀请最多的用户
		$invitemap['key'] = 'be_invited_count';
		$InviteUserList = model('UserData')->getlist($invitemap,' `value` desc', 10);
		$InviteUserInfoList=array();
		foreach($InviteUserList as $k=>$v)
		{
			$v['userinfo'] =model('User')->getUserInfoByUids($v['uid']);
			$InviteUserInfoList[$k] = $v;
		}
		$this->assign('InviteUserInfoList', $InviteUserInfoList);
		//print_r($InviteUserInfoList);
		
		//得到赞同最多的用户
		$map['key'] = 'comment_agree_count';
		$AgreeUserList = model('UserData')->getlist($map,' `value` desc', 10);
		$AgreeUserInfoList=array();
		foreach($AgreeUserList as $k=>$v)
		{
			$v['userinfo'] =model('User')->getUserInfoByUids($v['uid']);
			$AgreeUserInfoList[$k] = $v;
		}
		$this->assign('AgreeUserInfoList', $AgreeUserInfoList);
		//print_r($AgreeUserInfoList);
		
		//得到感谢最多的用户
		$Thankmap['key'] = 'tothanked_count';
		$ThankUserList = model('UserData')->getlist($Thankmap,' `value` desc', 10);
		$ThankUserInfoList=array();
		foreach($ThankUserList as $k=>$v)
		{
			$v['userinfo'] =model('User')->getUserInfoByUids($v['uid']);
			$ThankUserInfoList[$k] = $v;
		}
		$this->assign('ThankUserInfoList', $ThankUserInfoList);
		//print_r($ThankUserInfoList);
		
		//最新专家点评
		$Euids = model('UserGroupLink')->getUserByGroupID(8, 6);
		$struid = implode(',',$Euids);
		$answerWhere =' is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND (is_audit=1 OR is_audit=0) AND uid in ('.$struid.')';
		$answerList = model('Feed')->getAnswerList($answerWhere, 4,' publish_time desc');
		$this->assign('answerList',$answerList);
		//print_r($answerList);
		
		$this->setTitle ('广场' );
		$this->setKeywords ('广场');
		$this->display();
	}
	
	/**
	 * 专家页面
	 *
	 * @return 
	 *
	 */	
	public function expert()
	{
		//顶级专家
		$expertUid = 1901;
		$TopExpert = model('user')->getUserInfo($expertUid);
		$user_count = model ( 'UserData' )->getUserDataByUids ( array($expertUid) );
		$this->assign ( 'TopExpert_UserCount', $user_count );
		$this->assign('TopExpert',$TopExpert);
		
		//主要作品(顶级专家)
		$worksWhere = '`uid` ='.$expertUid.' and `type`=1';
		$WorksList = model('NewsData')->getNewDataList($worksWhere, 10);
		$this->assign('WorksList',$WorksList);
		
		//相关新闻
		$newsWhere = '`type`=2';
		$NewsList = model('NewsData')->getNewDataList($newsWhere, 4);
		$this->assign('NewsList',$NewsList);
		
		//专家问答
		$ExpertWhere = '(is_audit=1 OR is_audit=0) AND uid='.$expertUid.' AND is_del = 0 AND feed_questionid !=0 AND add_feedid=0 ';
		$QAList = model('feed')->getAnswerList($ExpertWhere, 10);
		//print_r($QAList['data']);
		$this->assign('ExpertQA',$QAList);
		
		//认证专家
		$uids = model('UserGroupLink')->getUserByGroupID(8);
		$user_count = model ( 'UserData' )->getUserDataByUids ($uids);
		$authenticateExpert = model('user')->getUserInfoByUids($uids);
		//print_r($authenticateExpert);
		$this->assign ( 'authenticateExpert_UserCount', $user_count );
		$this->assign('authenticateExpert',$authenticateExpert);
		
		$struid = implode(',',$uids);
		$answerWhere =' is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND (is_audit=1 OR is_audit=0) AND uid in ('.$struid.')';
		$answerList = model('Feed')->getAnswerList($answerWhere, 8);
		$this->assign('answerList',$answerList);
		
		$this->setTitle ('专家' );
		$this->setKeywords ('专家');
		$this->display();
	}
	
	/**
	* 最新单个问题页
	* @return void
	*/
	public function newquestion(){
		$feed_id = intval ( $_GET ['feed_id'] );
		
		if (empty($feed_id)) {
			$this->error( L ( 'PUBLIC_INFO_ALREADY_DELETE_TIPS' ) );
		}
		
		//增加浏览数
		model('Feed')->UpdatePV($feed_id);
		
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

		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $feedInfo['uid'] );
		
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
		$this->assign ( 'feedInfo', $feedInfo );

		$this->setTitle($feedInfo['body']);
		$this->setKeywords($feedInfo['body']);
		
		$this->display ();
	}
	
	/**
	 * 最新个人页
	*/
	public function newperson()
	{
		
		$uid = intval ( $_GET ['uid'] );
		
		if (empty($uid)) {
			$this->error( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ($uid);
		$this->_tab_menu($uid);
		

		// 加载提问筛选信息
		$d ['feed_type'] = t ( $_REQUEST ['feed_type'] ) ? t ( $_REQUEST ['feed_type'] ) : '';
		$d ['feed_key'] = t ( $_REQUEST ['feed_key'] ) ? t ( $_REQUEST ['feed_key'] ) : '';
		$d ['type'] = t ( $_REQUEST ['type'] ) ? t ( $_REQUEST ['type'] ) : 'following';
		$this->assign ( $d );
			
		! is_array ( $uid ) && $uids = explode ( ',', $uid );
		$user_info = model ( 'User' )->getUserInfoByUids ( $uids );
		$this->assign ( 'user_info', $user_info );

		$this->setTitle ( $user_info [$uid]['uname'] . '的主页' );
		$this->setKeywords ( $user_info [$uid]['uname'] . '的主页' );
		
		$this->display ();
	}
	
	/**
	 * 个人主页头部数据
	 * 
	 * @return void
	 */
	public function _top($uid) {
		// 获取用户组信息
		$userGroupData = model ( 'UserGroupLink' )->getUserGroupData ( $uid );
		$this->assign ( 'userGroupData', $userGroupData );
		// 获取用户积分信息
		$userCredit = model ( 'Credit' )->getUserCredit ( $uid );
		$this->assign ( 'userCredit', $userCredit );
		// 加载用户关注信息
		//($this->mid != $this->uid) && $this->_assignFollowState ( $uid );
		// 获取用户统计信息
		$userData = model ( 'UserData' )->getUserData ( $uid );
		$this->assign ( 'userData', $userData );
	}
	/**
	 * 个人主页标签导航
	 *
	 * @return void
	 */
	public function _tab_menu($uid) {
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
	 *个人档案的问题列表
	 */
	public function question()
	{
		$uid = intval ( $_GET ['uid'] );
		
		if (empty($uid)) {
			$this->error( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ($uid);
		$this->_tab_menu($uid);
		

		// 加载提问筛选信息
		$d ['feed_type'] = t ( $_REQUEST ['feed_type'] ) ? t ( $_REQUEST ['feed_type'] ) : '';
		$d ['feed_key'] = t ( $_REQUEST ['feed_key'] ) ? t ( $_REQUEST ['feed_key'] ) : '';
		$this->assign ( $d );
			
		! is_array ( $uid ) && $uids = explode ( ',', $uid );
		$user_info = model ( 'User' )->getUserInfoByUids ( $uids );
		$this->assign ( 'user_info', $user_info );

		$this->setTitle ( $user_info [$uid]['uname'] . '的提问' );
		$this->setKeywords ( $user_info [$uid]['uname'] . '的提问' );
		$this->display ();
	}
	/**
	 *个人档案的回答列表
	 */
	public function answer()
	{
		$uid = intval ( $_GET ['uid'] );
		
		if (empty($uid)) {
			$this->error( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ($uid);
		$this->_tab_menu($uid);
		
		// 加载提问筛选信息
		$d ['feed_type'] = t ( $_REQUEST ['feed_type'] ) ? t ( $_REQUEST ['feed_type'] ) : '';
		$d ['feed_key'] = t ( $_REQUEST ['feed_key'] ) ? t ( $_REQUEST ['feed_key'] ) : '';
		$this->assign ( $d );
		
		! is_array ( $uid ) && $uids = explode ( ',', $uid );
		$user_info = model ( 'User' )->getUserInfoByUids ( $uids );
		$this->assign ( 'user_info', $user_info );
		
		$this->setTitle ( $user_info [$uid]['uname'] . '的回答' );
		$this->setKeywords ( $user_info [$uid]['uname'] . '的回答' );
		$this->display ();
	}
	
	/**
	 * 获取用户关注列表
	 * 
	 * @return void
	 */
	public function following() {
		$uid = intval ( $_GET ['uid'] );
		
		if (empty($uid)) {
			$this->error( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ($uid);
		
		$following_list = model ( 'Follow' )->getFollowingList ( $uid, t ( $_GET ['gid'] ), 20 );

		
		$fids = getSubByKey ( $following_list ['data'], 'fid' );
		if ($fids) {
			$uids = array_merge ( $fids, array (
				$uid
				) );
		} else {
			$uids = array (
				$uid
				);
		}
		// 获取用户组信息
		$userGroupData = model ( 'UserGroupLink' )->getUserGroupData ( $uids );
		//$this->assign ( 'userGroupData', $userGroupData );
		//$this->_assignFollowState ( $uids );
		//$this->_assignUserInfo ( $uids );
		$this->_assignUserProfile ( $uids );
		$this->_assignUserTag ( $uids );
		$this->_assignUserCount ( $fids );
		// 关注分组
		($this->mid == $this->uid) && $this->_assignFollowGroup ( $fids );
		
		$this->assign ( 'following_list', $following_list );
		
		$user_info = model ( 'User' )->getUserInfoByUids ( $uids );
		$this->assign ( 'user_info', $user_info );
		
		
		$this->setTitle ( $user_info[$uid]['uname'].'的关注' );
		$this->setKeywords ($user_info[$uid]['uname'].'的关注');
		$this->display ();
	}
	
	/**
	 * 获取用户粉丝列表
	 * 
	 * @return void
	 */
	public function follower() {
		$uid = intval ( $_GET ['uid'] );
		
		if (empty($uid)) {
			$this->error( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ($uid);

		$follower_list = model ( 'Follow' )->getFollowerList ( $uid, 20 );

		$fids = getSubByKey ( $follower_list ['data'], 'fid' );
		if ($fids) {
			$uids = array_merge ( $fids, array (
				$uid
				) );
		} else {
			$uids = array (
				$uid
				);
		}
		// 获取用户用户组信息
		$userGroupData = model ( 'UserGroupLink' )->getUserGroupData ( $uids );
		//$this->assign ( 'userGroupData', $userGroupData );
		//$this->_assignFollowState ( $uids );
		//$this->_assignUserInfo ( $uids );
		$this->_assignUserProfile ( $uids );
		$this->_assignUserTag ( $uids );
		$this->_assignUserCount ( $fids );
		
		$this->assign ( 'follower_list', $follower_list );
		
		$user_info = model ( 'User' )->getUserInfoByUids ( $uids );
		$this->assign ( 'user_info', $user_info );
		
		$this->setTitle ( $user_info[$uid]['uname'].'的粉丝' );
		$this->setKeywords ($user_info[$uid]['uname'].'的粉丝');
		$this->display ();
	}
	
	
	
	/**
	* 获取用户好友列表
	* 
	* @return void
	*/
	public function friend() {
		$uid = intval ( $_GET ['uid'] );
		
		if (empty($uid)) {
			$this->error( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ($uid);
		

		$following_list = model ( 'Follow' )->getFriendList ( $uid, 20 );

		$fids = getSubByKey ( $following_list ['data'], 'fid' );
		if ($fids) {
			$uids = array_merge ( $fids, array (
				$uid
				) );
		} else {
			$uids = array (
				$uid
				);
		}
		// 获取用户组信息
		$userGroupData = model ( 'UserGroupLink' )->getUserGroupData ( $uids );
		//$this->assign ( 'userGroupData', $userGroupData );
		//$this->_assignFollowState ( $uids );
		//$this->_assignUserInfo ( $uids );
		$this->_assignUserProfile ( $uids );
		$this->_assignUserTag ( $uids );
		$this->_assignUserCount ( $fids );
		// 关注分组
		//($this->mid == $this->uid) && $this->_assignFollowGroup ( $fids );
		$this->assign ( 'following_list', $following_list );
		
		$user_info = model ( 'User' )->getUserInfoByUids ( $uids );
		$this->assign ( 'user_info', $user_info );
		
		$this->setTitle ( $user_info[$uid]['uname'].'的好友' );
		$this->setKeywords ($user_info[$uid]['uname'].'的好友');
		$this->display ();
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
	 * 获取用户详细资料
	 * 
	 * @return void
	 */
	public function data() {
		$uid = intval ( $_GET ['uid'] );
		
		if (empty($uid)) {
			$this->error( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		
		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $uid );
		// 用户为空，则跳转用户不存在
		if (empty ( $user_info )) {
			$this->error ( L ( 'PUBLIC_USER_NOEXIST' ) );
		}
		// 个人空间头部
		$this->_top ($uid);
		$this->_tab_menu($uid);
		// 档案类型
		$ProfileType = model ( 'UserProfile' )->getCategoryList ();
		$this->assign ( 'ProfileType', $ProfileType );
		// 个人资料
		$this->_assignUserProfile ( $uid );
		// 获取用户职业信息
		$userCategory = model ( 'UserCategory' )->getRelatedUserInfo ( $uid );
		if (! empty ( $userCategory )) {
			foreach ( $userCategory as $value ) {
				$user_category .= '<a href="#" class="link btn-cancel"><span>' . $value ['title'] . '</span></a>&nbsp;&nbsp;';
			}
		}
		$this->assign ( 'user_category', $user_category );
		
		! is_array ( $uid ) && $uids = explode ( ',', $uid );
		$user_info = model ( 'User' )->getUserInfoByUids ( $uids );
		$this->assign ( 'user_info', $user_info );
		
		$this->setTitle ( $user_info [$uid]['uname'] . '的资料' );
		$this->setKeywords ( $user_info [$uid]['uname'] . '的资料' );
		$user_tag = model ( 'Tag' )->setAppName ( 'User' )->setAppTable ( 'user' )->getAppTags ( array (
			$this->uid 
			) );
		$this->setDescription ( t ( $user_category . $user_info ['location'] . ',' . implode ( ',', $user_tag [$this->uid] ) . ',' . $user_info ['intro'] ) );
		$this->display ();
	}
	
	/**
	 * 小网页
	 *
	 * @return void
	 *
	 */	
	public function mobilepage()
	{
		$feed_id = intval ( $_GET ['feed_id'] );
		
		if (empty($feed_id)) {
			$this->assign('error','该提问不存在或已被删除');
		}
		
		//获取提问信息
		$feedInfo = model ( 'Feed' )->get ( $feed_id );
		if (!$feedInfo){
			$this->assign('error','该提问不存在或已被删除');
			exit();
		}
		if ($feedInfo ['is_audit'] == '0' && $feedInfo ['uid'] != $this->mid) {
			$this->assign ('error', '此提问正在审核' );
			exit();
		}
		if ($feedInfo ['is_del'] == '1') {
			$this->assign('error','该提问已被删除');
			exit();
		}

		// 获取用户信息
		$user_info = model ( 'User' )->getUserInfo ( $feedInfo['uid'] );
		
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
		$this->assign ( 'feedInfo', $feedInfo );

		$this->setTitle($feedInfo['body']);
		$this->setKeywords($feedInfo['body']);
		
		$this->display ();	
	}
}
?>