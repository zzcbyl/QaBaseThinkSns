<?php
session_start();
/**
 * RegisterAction 注册模块
 * @author  zhangzc
 * @version TS3.0
 */
class RegisterAction extends Action
{
	private $_config;					// 注册配置信息字段
	private $_register_model;			// 注册模型字段
	private $_user_model;				// 用户模型字段
	private $_invite;					// 是否是邀请注册
	private $_invite_code;				// 邀请码

	/**
	 * 模块初始化，获取注册配置信息、用户模型对象、注册模型对象、邀请注册与站点头部信息设置
	 * @return void
	 */
	protected function _initialize()
	{
		$this->_invite = false;
		// 未激活与未审核用户
		/*if($this->mid > 0 && !in_array(ACTION_NAME, array('changeActivationEmail', 'activate', 'isEmailAvailable'))) {
			$GLOBALS['ts']['user']['is_audit'] == 0 && ACTION_NAME != 'waitForAudit' && U('public/Register/waitForAudit', array('uid'=>$this->mid), true);
			$GLOBALS['ts']['user']['is_audit'] == 1 && $GLOBALS['ts']['user']['is_active'] == 0 && ACTION_NAME != 'waitForActivation' && U('public/Register/waitForActivation', array('uid'=>$this->mid), true);
		}*/
		// 登录后，将不显示注册页面
		$this->mid > 0 && $GLOBALS['ts']['user']['is_init'] == 1 && redirect($GLOBALS['ts']['site']['home_url']);

		$this->_config = model('Xdata')->get('admin_Config:register');
		$this->_user_model = model('User');
		$this->_register_model = model('Register');
		$this->setTitle(L('PUBLIC_REGISTER'));
	}
	public function code(){
		if (md5(strtoupper($_POST['verify'])) == $_SESSION['verify']) {
			echo 1;
		}else{
			echo 0;
		}
	}

	/**
	 * 第三方帐号集成 - 绑定本地帐号
	 * @return void
	 */
	public function doBindStep1(){

		
	}

	/**
	 * 第三方帐号集成 - 注册新账号
	 * @return void
	 */
	public function doOtherStep1(){	

		
	}

    public function index() {
		$this->setTitle ( '输入邀请码' );
		$this->setKeywords ( '输入邀请码' );
        $this->display();
    }
    public function terms() {

		$this->setTitle ( '卢勤问答平台服务条款' );
		$this->setKeywords ( '卢勤问答平台服务条款' );
        $this->display();
    }

    public function step2() {

        $this->assign("gender",0);
        $this->assign("pwd","lqqa123456");
        $this->assign("nick",$_SESSION['third-party-user-info']['uname']);
        $this->assign("from",$_SESSION['third-party-type']);
        $this->assign("gender",$_SESSION['third-party-user-info']['sex']);
        $this->setTitle ( '填写注册信息' );
		$this->setKeywords ( '填写注册信息' );
        $this->display();
    }

	private $_email_reg = '/[_a-zA-Z\d\-\.]+(@[_a-zA-Z\d\-\.]+\.[_a-zA-Z\d\-]+)+$/i';		// 邮箱正则规则
	private $_mobile_reg = '/^0*(13|15|18)\d{9}$/i';		// 手机号正则规则

	/**
	 * 最新注册流程第一步填写资料
	*/
	public function doRegiest()
	{
		
		$account = t($_POST['account']);
		if($account=='')
		{
			$this->_error = '帐号不能为空';
		}
		
		$res = preg_match($this->_email_reg, $account, $matches) !== 0;
		
		$res_mobile = preg_match($this->_mobile_reg, $account, $matches) !== 0;
		
		if(!$res && !$res_mobile) {
			$this->_error = '无效的帐号';
		}
		//邮箱
		$user["login"] = $account;
		if($res)
		{
			$result=$this->CheckInviteCode($_POST['yqCode']);
			if($result==0)
				$this->error('邀请码不存在');
			else if($result==2)
				$this->error('邀请码已被使用');
			else if($result==3)
				$this->error('邀请码限定次数已用完');
			
			$user["linknumber"] = t($_POST['mobile']);
			$user["invite_code"] = t($_POST['yqCode']);
			$user["email"] = $account;
		}
		
		//手机号
		if($res_mobile)
		{
			//验证收到的手机验证码
			
			
			$user["email"] = t($_POST['email']);
			$user["linknumber"] = $account;
		}
		$user["uname"] = t($_POST['uname']);
		$user["password"] = t($_POST['password_new']);
		$user["sex"] = intval($_POST['sex']);
		$user["is_active"] = 0;
		$user["is_audit"] = 1;
		
		$user["realname"] = t($_POST['realname']);
		$user["idcard"] = t($_POST['idcard']);
		$birthday = '';
		$len = strlen($user["idcard"]);
		if($len==15)
		{
			$birthday = '19'.substr($user["idcard"],6,2).'-'.substr($user["idcard"],8,2).'-'.substr($user["idcard"],10,2);
		}
		else if($len==18)
		{
			$birthday = substr($user["idcard"],6,4).'-'.substr($user["idcard"],10,2).'-'.substr($user["idcard"],12,2);
		}
		else
			$this->error('输入的身份证号格式不正确');
		$user["birthday"] = $birthday;
		
		$cityIds = t($_POST['city_ids']);
		$cityIds = explode(',', $cityIds);
		if(!$cityIds[0] || !$cityIds[1] || !$cityIds[2]) $this->error('请选择完整地区');
		isset($cityIds[0]) && $user["province"] = intval($cityIds[0]);
		isset($cityIds[1]) && $user["city"] = intval($cityIds[1]);
		isset($cityIds[2]) && $user['area'] = intval($cityIds[2]);
		$user["location"] = t($_POST['city_names']);
		$user["intro"] = t($_POST['intro']);
		
		//注册邮箱@anran.com的不做邮箱验证
		if($res_mobile || strpos($user["login"],'@anran.com') > 0)
		{
			$user["is_active"] = 1;
		}

		$uid = $this->_user_model->addUser($user);

		if($uid)
		{
			if (isset($_SESSION['third-party-type'])) {
				$user_info = $_SESSION['third-party-user-info'];
				$syncdata['uid'] = $uid;
				$syncdata['type_uid'] = $user_info['id'];
				$syncdata['type'] = $_SESSION['third-party-type'];
				$syncdata['oauth_token'] = $_SESSION [$_SESSION['third-party-type']] ['access_token'] ['oauth_token'];
				$syncdata['oauth_token_secret'] = $_SESSION [$_SESSION['third-party-type']] ['access_token'] ['oauth_token_secret'];
				if ($info = M ( 'login' )->where ( "type_uid={$userinfo['id']} AND type='" . $type . "'" )->find ()) {
					// 该新浪用户已在本站存在, 将其与当前用户关联(即原用户ID失效)
					M ( 'login' )->where ( "`login_id`={$info['login_id']}" )->save ( $syncdata );
				} else {
					// 添加同步信息
					M ( 'login' )->add ( $syncdata );
				}
			}

			// 添加积分
			model('Credit')->setUserCredit($uid,'init_default');

			// 添加至默认的用户组
			$userGroup = model('Xdata')->get('admin_Config:register');
			$userGroup = empty($userGroup['default_user_group']) ? C('DEFAULT_GROUP_ID') : $userGroup['default_user_group'];
			model('UserGroupLink')->domoveUsergroup($uid, implode(',', $userGroup));
			
			//注册邮箱@anran.com的不做邮箱验证
			if(strpos($user["login"],'@anran.com') > 0)
			{
				//$this->redirect('public/Register/step4', array('uid'=>$uid,'code'=>$_GET['code']));
				$this->redirect('public/Register/avatar');
			}
			
			if (isset($_SESSION['third-party-type']))  {
				$user_message = $_SESSION["third-party-user-info"];
				$avatar = new AvatarModel($uid);
				$avatar->saveRemoteAvatar($user_message['userface'],$uid);
			}
			
			//登录
			model('Passport')->loginLocalWhitoutPassword($account);
			
			//邮箱注册减邀请码剩余次数
			if($res)
			{
				//发送验证邮件
				$this->_register_model->sendActivationEmail($uid);
				
				if ($_SESSION["open_platform_type"] != "sina" && $_SESSION["open_platform_type"] != "qzone") {
					model('Invite')->where("code = '".$_POST['yqCode']."'")->setDec('limited_count');
				}
				
				$this->redirect('public/Register/step3', array('uid'=>$uid, 'code'=>$_POST['yqCode']));
			}
			if($res_mobile)
			{
				$this->redirect('public/Register/avatar');
			}
			
		}
		else
		{
			$this->error(L('PUBLIC_REGISTER_FAIL'));			// 注册失败
		}
	}


    /**
     * 注册流程 - 执行第二步骤
     * 填写注册信息
     */
    public function doStep2() {

		if (isset($_SESSION['third-party-type'])) {

            if(empty($_POST['email']) || empty($_POST['uname']) ){
                $this->error('参数错误');
            }
        }
		else {
			if(empty($_POST['email']) || empty($_POST['uname']) || empty($_POST['password_new']) || empty($_POST['repassword_new'])){
				$this->error('参数错误');
			}

			$result=$this->CheckInviteCode($_GET['code']);

			if($result==0)
				$this->error('邀请码不存在');
			else if($result==2)
				$this->error('邀请码已被使用');
			else if($result==3)
				$this->error('邀请码限定次数已用完');
		}

        //echo "aaaa";

        //check_invite_code($_GET["code"]);

        $user["login"] = t($_POST['email']);
        $user["uname"] = t($_POST['uname']);
        $user["password"] = t($_POST['password_new']);
        $user["email"] = t($_POST['email']);
        $user["sex"] = intval($_POST['sex']);
        $user["invite_code"] = t($_GET['code']);
        $user["is_active"] = 0;
        $user["is_audit"] = 1;
		//注册邮箱@anran.com的不做邮箱验证
		if(strpos($user["login"],'@anran.com') > 0)
		{
			$user["is_active"] = 1;
		}

        $uid = $this->_user_model->addUser($user);

        if($uid)
        {
            if (isset($_SESSION['third-party-type'])) {
                $user_info = $_SESSION['third-party-user-info'];
                $syncdata['uid'] = $uid;
                $syncdata['type_uid'] = $user_info['id'];
                $syncdata['type'] = $_SESSION['third-party-type'];
                $syncdata['oauth_token'] = $_SESSION [$_SESSION['third-party-type']] ['access_token'] ['oauth_token'];
                $syncdata['oauth_token_secret'] = $_SESSION [$_SESSION['third-party-type']] ['access_token'] ['oauth_token_secret'];
                if ($info = M ( 'login' )->where ( "type_uid={$userinfo['id']} AND type='" . $type . "'" )->find ()) {
                    // 该新浪用户已在本站存在, 将其与当前用户关联(即原用户ID失效)
                    M ( 'login' )->where ( "`login_id`={$info['login_id']}" )->save ( $syncdata );
                } else {
                    // 添加同步信息
                    M ( 'login' )->add ( $syncdata );
                }
            }
            /*
			if ($_SESSION["open_platform_type"] == "sina" || $_SESSION["open_platform_type"] == "qzone") {
				if($_SESSION["open_platform_type"] == "sina")
				{
					$syncdata['uid'] = $uid;
					$syncdata['type_uid'] = $_SESSION["sina"]["uid"];
					$syncdata['type'] = 'sina';
					$syncdata['oauth_token'] = $_SESSION ['sina'] ['access_token'] ['oauth_token'];
					$syncdata['oauth_token_secret'] = $_SESSION ['sina'] ['access_token'] ['oauth_token_secret'];
				}
				if($_SESSION["open_platform_type"] == "qzone")
				{
					$syncdata['uid'] = $uid;
					$syncdata['type_uid'] = $_SESSION["qzone"]["uid"];
					$syncdata['type'] = 'qzone';
					$syncdata['oauth_token'] = $_SESSION ['qzone'] ['access_token'] ['oauth_token'];
					$syncdata['oauth_token_secret'] = $_SESSION ['qzone'] ['access_token'] ['oauth_token_secret'];
				}
				if ($info = M ( 'login' )->where ( "type_uid={$userinfo['id']} AND type='" . $type . "'" )->find ()) {
					// 该新浪用户已在本站存在, 将其与当前用户关联(即原用户ID失效)
					M ( 'login' )->where ( "`login_id`={$info['login_id']}" )->save ( $syncdata );
				} else {
					// 添加同步信息
					M ( 'login' )->add ( $syncdata );
				}
			}
            */

			// 添加积分
			model('Credit')->setUserCredit($uid,'init_default');

			// 添加至默认的用户组
			$userGroup = model('Xdata')->get('admin_Config:register');
			$userGroup = empty($userGroup['default_user_group']) ? C('DEFAULT_GROUP_ID') : $userGroup['default_user_group'];
			model('UserGroupLink')->domoveUsergroup($uid, implode(',', $userGroup));
			
			//注册邮箱@anran.com的不做邮箱验证
			if(strpos($user["login"],'@anran.com') > 0)
			{
				$this->redirect('public/Register/step4', array('uid'=>$uid,'code'=>$_GET['code']));
			}
			//发送验证邮件
			$this->_register_model->sendActivationEmail($uid);
			
			if (isset($_SESSION['third-party-type']))  {
                $user_message = $_SESSION["third-party-user-info"];
                $avatar = new AvatarModel($uid);
                $avatar->saveRemoteAvatar($user_message['userface'],$uid);
            }
			
			//减邀请码剩余次数
			if ($_SESSION["open_platform_type"] != "sina" && $_SESSION["open_platform_type"] != "qzone") {
				model('Invite')->where("code = '".$_GET['code']."'")->setDec('limited_count');
			}
			
            $this->redirect('public/Register/step3', array('uid'=>$uid,'code'=>$_GET['code']));
        }
        else
        {
            $this->error(L('PUBLIC_REGISTER_FAIL'));			// 注册失败
        }
    }

    public function step3()
	{
		$uid = $this->mid;
		if(empty($uid)){
			$this->error('参数错误');
		}
		$user = $this->_user_model->getUserInfo($uid);
		
		/*if (!isset($_SESSION['third-party-type'])) {
			if(empty($_GET['code']))
			{
				$this->error('参数错误');
			}
			if($user['invite_code'] != $_GET['code'])
			{
				$this->error('参数错误');
			}	
			//$this->_register_model->sendActivationEmail($uid);
			$this->assign('Code',$_GET['code']);
		}*/
		
		$this->assign('User',$user);
		$this->setTitle ( '邮箱激活' );
		$this->setKeywords ( '邮箱激活' );
		$this->display();	
	}
	
	public function avatar()
	{
		$uid = $this->mid;
		if(empty($uid)){
			$this->error('参数错误');
		}
		model('User')->cleanCache($uid);
		$user = $this->_user_model->getUserInfo($uid);
		$this->assign('User',$user);


		if (isset($_SESSION['user_message'])) {
			$user_message = $_SESSION['user_message'];
			$this->assign('Weibo','http://weibo.com/u/'.$user_message['id']);
		}

		$this->setTitle ( '上传头像' );
		$this->setKeywords ( '上传头像' );
		$this->display();
	}
	
	public function step4()
	{
		if(empty($_GET['uid'])){
			$this->error('参数错误');
		}
		$uid = intval($_GET['uid']);
		
		$user = $this->_user_model->getUserInfo($uid);
		if (!isset($_SESSION['third-party-type'])) {
			if(empty($_GET['code']))
			{
				$this->error('参数错误');
			}
			if($user['invite_code'] != $_GET['code'])
			{
				$this->error('参数错误');
			}
			//$this->_register_model->sendActivationEmail($uid);
			$this->assign('Code',$_GET['code']);
		}
		
		$this->assign('User',$user);


        if (isset($_SESSION['user_message'])) {
            $user_message = $_SESSION['user_message'];
            $this->assign('Weibo','http://weibo.com/u/'.$user_message['id']);
        }

		$this->setTitle ( '完善个人资料' );
		$this->setKeywords ( '完善个人资料' );
		$this->display();
	}
	
	public function step5()
	{
		if(empty($_GET['uid'])){
			$this->error('参数错误');
		}
		$uid = intval($_GET['uid']);
		$code = $_GET['code'];
		$user = $this->_user_model->getUserInfo($uid);
		if (!isset($_SESSION['third-party-type'])) {
			if(empty($_GET['code']))
			{
				$this->error('参数错误');
			}
			if($user['invite_code'] != $code)
			{
				$this->error('参数错误');
			}
			$this->assign('code',$code);
		}
		$this->assign('uid',$uid);
		
		
		//顶级专家
		$topUserID = 1901;
		$TopExpert = model('user')->getUserInfo($topUserID);
		$user_count = model ( 'UserData' )->getUserDataByUids ( array($topUserID) );
		//print_r($user_count);
		$this->assign ( 'TopExpert_UserCount', $user_count );
		$this->assign('TopExpert',$TopExpert);
		
		//认证专家
		$uids = model('UserGroupLink')->getUserByGroupID(8);
		$user_count = model ( 'UserData' )->getUserDataByUids ($uids);
		$authenticateExpert = model('user')->getUserInfoByUids($uids);
		//print_r($authenticateExpert);
		$this->assign ( 'authenticateExpert_UserCount', $user_count );
		$this->assign('authenticateExpert',$authenticateExpert);
		//print('<br /><br /><br />');
		
		//跟你有关的
		$where = " `is_del` = 0 and `is_audit` = 1 and `is_active` = 1 and `is_init` = 1 and ((`invite_code` = '$code' and `uid` != $uid) or (`area` = ".$user['area'].")) ";
		$uidList = model('user')->field('uid')->where($where)->order('`uid` desc')->findAll();
		$uids = getSubByKey($uidList, 'uid');
		if(!empty($uids))
		{
			$userList = model('user')->getUserInfoByUids($uids);
			//print_r($userList);
			$newuserList = array();
			foreach($userList as $k=>$v)
			{
				if(!array_key_exists($k, $authenticateExpert) && $k != $topUserID )
				{
					$newuserList[$k] = $v;
				}
			}
			//print_r($newuserList);
			$this->assign("youguanCount",count($newuserList));
			$this->assign('userList',$newuserList);
		}
		else
		{
			$this->assign("youguanCount",0);
		}
		
		//推荐用户
		$recommendwhere = " `key` = 'weibo_count' or `key` = 'answer_count' ";
		//$recommendwhere = " `key` = 'weibo_count' ";
		$recommendData = model('UserData')->field(uid)->where($recommendwhere)->order('`value` desc')->findPage(70);
		$uids = getSubByKey($recommendData['data'], 'uid');
		if(!empty($uids))
		{
			$recommenduserList = model('user')->getUserInfoByUids($uids);
			//print_r($recommenduserList);
			$newrecommenduserList = array();
			foreach($recommenduserList as $k=>$v)
			{
				if(!array_key_exists($k, $authenticateExpert)  && $k != $topUserID)
				{
					$newrecommenduserList[$k] = $v;
				}
			}
			$this->assign("tuijianCount",count($newrecommenduserList));
			$this->assign('recommendUserList',$newrecommenduserList);
		}
		else
			$this->assign("tuijianCount",0);
		$this->setTitle ( '关注朋友' );
		$this->setKeywords ( '关注朋友' );
		$this->display();	
	}

	public function follow()
	{
		$uid = $this->mid;
		if(empty($uid)){
			$this->error('参数错误');
		}
		$user = $this->_user_model->getUserInfo($uid);
		$this->assign('uid',$uid);
		
		
		//顶级专家
		$topUserID = 1901;
		$TopExpert = model('user')->getUserInfo($topUserID);
		$user_count = model ( 'UserData' )->getUserDataByUids ( array($topUserID) );
		//print_r($user_count);
		$this->assign ( 'TopExpert_UserCount', $user_count );
		$this->assign('TopExpert',$TopExpert);
		
		//认证专家
		$uids = model('UserGroupLink')->getUserByGroupID(8);
		$user_count = model ( 'UserData' )->getUserDataByUids ($uids);
		$authenticateExpert = model('user')->getUserInfoByUids($uids);
		//print_r($authenticateExpert);
		$this->assign ( 'authenticateExpert_UserCount', $user_count );
		$this->assign('authenticateExpert',$authenticateExpert);
		//print('<br /><br /><br />');
		
		//跟你有关的
		$where = " `is_del` = 0 and `is_audit` = 1 and `is_active` = 1 and `is_init` = 1 and ((`invite_code` = '$code' and `uid` != $uid) or (`area` = ".$user['area'].")) ";
		$uidList = model('user')->field('uid')->where($where)->order('`uid` desc')->findAll();
		$uids = getSubByKey($uidList, 'uid');
		if(!empty($uids))
		{
			$userList = model('user')->getUserInfoByUids($uids);
			//print_r($userList);
			$newuserList = array();
			foreach($userList as $k=>$v)
			{
				if(!array_key_exists($k, $authenticateExpert) && $k != $topUserID )
				{
					$newuserList[$k] = $v;
				}
			}
			//print_r($newuserList);
			$this->assign("youguanCount",count($newuserList));
			$this->assign('userList',$newuserList);
		}
		else
		{
			$this->assign("youguanCount",0);
		}
		
		//推荐用户
		$recommendwhere = " `key` = 'weibo_count' or `key` = 'answer_count' ";
		//$recommendwhere = " `key` = 'weibo_count' ";
		$recommendData = model('UserData')->field(uid)->where($recommendwhere)->order('`value` desc')->findPage(70);
		$uids = getSubByKey($recommendData['data'], 'uid');
		if(!empty($uids))
		{
			$recommenduserList = model('user')->getUserInfoByUids($uids);
			//print_r($recommenduserList);
			$newrecommenduserList = array();
			foreach($recommenduserList as $k=>$v)
			{
				if(!array_key_exists($k, $authenticateExpert)  && $k != $topUserID)
				{
					$newrecommenduserList[$k] = $v;
				}
			}
			$this->assign("tuijianCount",count($newrecommenduserList));
			$this->assign('recommendUserList',$newrecommenduserList);
		}
		else
			$this->assign("tuijianCount",0);
		$this->setTitle ( '关注朋友' );
		$this->setKeywords ( '关注朋友' );
		$this->display();	
	}

	public function doFollow()
	{
		$uid = $this->mid;
		if(empty($uid)){
			$this->error('参数错误');
		}
		$user = $this->_user_model->getUserInfo($uid);

		$uids = explode(',', $_POST['FollowingList']);
		foreach($uids as $k=>$v)
		{
			model('Follow')->doFollow($uid,$v);
		}
		
		//初始化完成
		$this->_register_model->overUserInit($uid);
		$this->_user_model->cleanCache ( array($uid) );
		unset($_SESSION['third-party-type']);
		$result = model('Passport')->loginLocalWhitoutPassword($user['login']);
		if(!$result){
			$status = 0; 
			$info	= model('Passport')->getError();
			$data 	= 0;
		}else{
			$status = 1;
			$info 	= model('Passport')->getSuccess();
			redirect($GLOBALS['ts']['site']['home_url']);
		}
		
		
	}

	/**
	 * 注册流程 - 执行第一步骤
	 * 输入邀请码
	 */
	public function doStep1(){	
		$return  = array('status'=>0,'data'=>'邀请码错误');
		if(empty($_POST['Code'])){
			$return['data'] = '参数错误';
			echo json_encode($return);exit();
		}

        $result=$this->CheckInviteCode($_POST['Code']);

        if($result==0)
            $return  = array('status'=>0,'data'=>'邀请码不存在');
		else if($result==1)
            $return  = array('status'=>1,'data'=>'邀请码验证成功');
		else if($result==2)
            $return  = array('status'=>0,'data'=>'邀请码已被使用');
		else if($result==3)
            $return  = array('status'=>0,'data'=>'邀请码限定次数已用完');

        $_SESSION["invite_code"]=$_POST['Code'];

		echo json_encode($return);exit();
	}




    /**
     * 注册流程 - 执行第三步骤
     * 添加标签
     */
    public function doStep3() {

    }

	
	/**
	 * 注册流程 - 第四步补充资料
	 *
	 * @return void
	 *
	 */	
	public function doStep4(){	
		if(empty($_GET['uid'])){
			$this->error("参数错误");
		} 
		$uid = intval($_GET['uid']);
		
		$user = $this->_user_model->getUserInfo($uid);
		if (!isset($_SESSION['third-party-type'])) {
			if(empty($_GET['code']))
			{
				$this->error("参数错误");
			}
			if($user['invite_code'] != $_GET['code'])
			{
				$this->error('参数错误');
			}
		}
		
		$user["realname"] = t($_POST['realname']);
		$user["idcard"] = t($_POST['idcard']);
		
		$birthday = '';
		$len = strlen($user["idcard"]);
		if($len==15)
		{
			$birthday = '19'.substr($user["idcard"],6,2).'-'.substr($user["idcard"],8,2).'-'.substr($user["idcard"],10,2);
		}
		else if($len==18)
		{
			$birthday = substr($user["idcard"],6,4).'-'.substr($user["idcard"],10,2).'-'.substr($user["idcard"],12,2);
		}
		else
			$this->error('输入的身份证号格式不正确');
		$user["birthday"] = $birthday;
		
		$cityIds = t($_POST['city_ids']);
		$cityIds = explode(',', $cityIds);
		if(!$cityIds[0] || !$cityIds[1] || !$cityIds[2]) $this->error('请选择完整地区');
		isset($cityIds[0]) && $user["province"] = intval($cityIds[0]);
		isset($cityIds[1]) && $user["city"] = intval($cityIds[1]);
		isset($cityIds[2]) && $user['area'] = intval($cityIds[2]);
		
		$user["location"] = t($_POST['city_names']);
		$user["intro"] = t($_POST['intro']);
		$user["linknumber"] = t($_POST['phone']);
		$user["qq"] = t($_POST['qq']);
		$user["weixin"] = t($_POST['weixin']);
		$user["tengxunVB"] = t($_POST['tengxuvb']);
		$user["xinlangVB"] = t($_POST['xinlangvb']);
		
		$updresult = $this->_user_model->where('uid='.$uid)->save($user);
		if(!$updresult)
		{
			$this->error('补充资料失败');
		}
		
		$permissions['realname'] = t($_POST['sel_realname']);
		//$permissions['birthday'] = t($_POST['sel_birthday']);
		$permissions['mobile'] = t($_POST['sel_mobile']);
		$permissions['qq'] = t($_POST['sel_qq']);
		$permissions['weixin'] = t($_POST['sel_weixin']);
		$permissions['tengxunVB'] = t($_POST['sel_tengxunVB']);
		$permissions['xinlangVB'] = t($_POST['sel_xinlangVB']);
		model('UserPermissions')->saveUserPermissions($uid, $permissions);
		
		$this->_user_model->cleanCache ( array($uid) );
		$this->redirect('public/Register/step5', array('uid'=>$uid, 'code'=>$_GET['code']));
	}
	
	public function doStep5()
	{
		if(empty($_GET['uid'])){
			$this->error("参数错误");
		} 
		$uid = intval($_GET['uid']);
		
		$user = $this->_user_model->getUserInfo($uid);
		if (!isset($_SESSION['third-party-type'])) {
			if(empty($_GET['code']))
			{
				$this->error("参数错误");
			}
			if($user['invite_code'] != $_GET['code'])
			{
				$this->error('参数错误');
			}	
		}
		
		$uids = explode(',', $_POST['FollowingList']);
		foreach($uids as $k=>$v)
		{
			model('Follow')->doFollow($uid,$v);
		}
		
		//初始化完成
		$this->_register_model->overUserInit($uid);
		$this->_user_model->cleanCache ( array($uid) );
		unset($_SESSION['third-party-type']);
		$result = model('Passport')->loginLocalWhitoutPassword($user['login']);
		if(!$result){
			$status = 0; 
			$info	= model('Passport')->getError();
			$data 	= 0;
		}else{
			$status = 1;
			$info 	= model('Passport')->getSuccess();
			redirect($GLOBALS['ts']['site']['home_url']);
		}
		
		
	}
	
	/**
	 * 检查邀请码是否可用
	 *
	 * @param string $code 邀请码
	 * @return int 邀请码使用情况，0：邀请码不存在，1：邀请码可用，2：邀请码已被使用，3：邀请码限定次数已用完
	 *
	 */	
	protected function CheckInviteCode($code)
	{
		$result = model('Invite')->checkInviteCode_new($code);
		return $result;
	}
	


	/**
	 * 等待审核页面
	 * @return void
	 */
	public function waitForAudit() {
		
	}

	/**
	 * 等待激活页面
	 */
	public function waitForActivation() {
		
	}
	
	/**
	 * 再次发送邮件
	 *
	 * @return mixed This is the return value description
	 *
	 */	
	public function AgainSendEmail()
	{
		if(empty($_GET['uid']) || empty($_GET['code'])){
			$this->error('参数错误');
		}
		$uid = intval($_GET['uid']);
		
		$user = $this->_user_model->getUserInfo($uid);
		if($user['invite_code'] != $_GET['code'])
		{
			$this->error('参数错误');
		}
		$this->uid = $uid;
		$this->resendActivationEmail();
	}
	

	/**
	 * 发送激活邮件
	 * @return void
	 */
	public function resendActivationEmail() {
		$res = $this->_register_model->sendActivationEmail($this->uid);
		$this->ajaxReturn(null, $this->_register_model->getLastError(), $res);
	}

	/**
	 * 修改激活邮箱
	 */
	public function changeActivationEmail() {
		$email = t($_POST['email']);
		// 验证邮箱是否为空
		if (!$email) {
			$this->ajaxReturn(null, '邮箱不能为空！', 0);
		}
		// 验证邮箱格式
		$checkEmail = $this->_register_model->isValidEmail($email);
		if (!$checkEmail) {
			$this->ajaxReturn(null, $this->_register_model->getLastError(), 0);
		}
		$res = $this->_register_model->changeRegisterEmail($this->uid, $email);
		$res && $this->_register_model->sendActivationEmail($this->uid);
		$this->ajaxReturn(null, $this->_register_model->getLastError(), $res);
	}

	/**
	 * 通过链接激活帐号
	 * @return void
	 */
	public function activate() {
		$user_info = $this->_user_model->getUserInfo($this->uid);

		$this->assign('user',$user_info);

		if ($user_info && $user_info['is_active'] == 1) {
			//$this->assign('jumpUrl', U('public/Register/step4', array('uid'=>$this->user ['uid'], 'code'=>$this->user ['invite_code'])));
			/*if($user_info['location'] == '')
			{
				//print('asdf');
				//print('<br /><br /><br /><br />');
				$this->redirect('public/Register/step4', array('uid'=>$user_info['uid'], 'code'=>$user_info['invite_code']));
			}
			else */
			if($user_info['is_init'] == 0)
			{
				$this->redirect('public/Register/avatar', array('uid'=>$user_info['uid']));
			}
			else
			{
				$this->redirect('public/Passport/login');
			}
		}
		
		/*print($user_info['is_active']);
		return;*/
		
		$active = $this->_register_model->activate($this->uid, t($_GET['code']));

		if ($active) {
			// 登陆
			model('Passport')->loginLocalWhitoutPassword($user_info['email']);
			$this->setTitle('成功激活帐号');
			$this->setKeywords('成功激活帐号');
			// 跳转下一步
			$this->assign('jumpUrl', U('public/Register/avatar', array('uid'=>$user_info['uid'])));
			//$this->success($this->_register_model->getLastError());
		} else {
			$this->redirect('public/Passport/login');
			$this->error($this->_register_model->getLastError());
		}
		$this->display();
	}






	/**
	 * 获取推荐用户
	 * @return void
	 */
	public function getRelatedUser() {
		
	}


	/**
	 * 验证邮箱是否已被使用
	 */
	public function isEmailAvailable() {
		$email = t($_POST['email']);
		$result = $this->_register_model->isValidEmail($email);
		$this->ajaxReturn(null, $this->_register_model->getLastError(), $result);
	}
	
	/**
	 * 验证帐号是否已被使用
	 */
	public function isAccountAvailable() {
		$account = t($_POST['account']);
		$result = $this->_register_model->isValidAccount($account);
		$this->ajaxReturn(null, $this->_register_model->getLastError(), $result);
	}


	/**
	 * 验证邀请邮件
	 */
    public function isEmailAvailable_invite() {
		
	}

	/**
	 * 验证昵称是否已被使用
	 */
	public function isUnameAvailable() {
		$uname = t($_POST['uname']);
		$oldName = t($_POST['old_name']);
		$result = $this->_register_model->isValidName($uname, $oldName);
		$this->ajaxReturn(null, $this->_register_model->getLastError(), $result);
	}

	/**
	 * 添加用户关注信息
	 */
	public function bulkDoFollow() {
		
	}

	/**
	 *  设置用户为已初始化
	 */
	public function doAuditUser(){
		
	}


    /*weibo的想关处理*/

    public function weibologin() {
        //echo "aaaaa";

        //check_invite_code($_GET["code"]);

        //$_SESSION["invite_code"] = $_GET["code"];

        $o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

        $code_url = $o->getAuthorizeURL( WB_CALLBACK_URL );

        $this->code_url = $code_url;

        //$this->redirect($code_url);

        $this->display();
    }

    public function weibologincallback(){



        $o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

        if (isset($_REQUEST['code'])) {
            $keys = array();
            $keys['code'] = $_REQUEST['code'];
            $keys['redirect_uri'] = WB_CALLBACK_URL;
            try {
                $token = $o->getAccessToken( 'code', $keys ) ;
                $_SESSION["token"] = $token;
            } catch (OAuthException $e) {
            }
        }

        if ($token) {
            $c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
            $uid_get = $c->get_uid();
            $uid = $uid_get['uid'];
            $user_message = $c->show_user_by_id( $uid);
            $weibo_uid = $uid;
            if ($this->judge_weibo_user_status($user_message)=="unreg"){
                $user["login"] = $uid;
                $user["uname"] = $user_message["screen_name"];
                $user["password"] = $user_message["screen_name"];
                $user["email"] = $user_message["screen_name"]+"@sina.weibo.com";
                $user["sex"] = $user_message["gender"]=="m"? 1 : 2 ;
                $user["invite_code"] = $_SESSION["invite_code"];
                $user["is_active"] = 1;
                $user["is_audit"] = 1;

                $uid = $this->_user_model->addUser($user);

                $map['login'] = $weibo_uid;

                $this->_user_model->where( " `uid` = ".$uid)->save($map);


                if($uid)
                {
                    // 添加积分
                    model('Credit')->setUserCredit($uid,'init_default');

                    // 添加至默认的用户组
                    $userGroup = model('Xdata')->get('admin_Config:register');
                    $userGroup = empty($userGroup['default_user_group']) ? C('DEFAULT_GROUP_ID') : $userGroup['default_user_group'];
                    model('UserGroupLink')->domoveUsergroup($uid, implode(',', $userGroup));

                    //发送验证邮件
                    $this->_register_model->sendActivationEmail($uid);

                    $this->redirect('public/Register/step3', array('uid'=>$uid,'code'=>$_SESSION["invite_code"]));
                }
                else
                {
                    $this->error(L('PUBLIC_REGISTER_FAIL'));			// 注册失败
                }
            }



            //echo $user_message;
        }
        else{
            echo "fail";

        }

    }

    public function judge_weibo_user_status($user_message) {
        //echo "111";
        //is_init
        return "unreg";
        //echo "222";
    }






}