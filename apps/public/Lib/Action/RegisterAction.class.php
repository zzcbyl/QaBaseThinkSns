<?php
session_start();
include_once( 'third-party-api/weibo/config.php' );
include_once( 'third-party-api/weibo/saetv2.ex.class.php' );

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

        $this->display();

    }

    public function step2() {

        $this->assign("gender",0);
        $this->assign("pwd","");
        $this->assign("nick","");
        $this->assign("from","");

        if (isset($_SESSION["sina"])) {

            $this->assign("from","sina");
            $c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['sina']['access_token']['oauth_token'] );
            $user_message = $c->show_user_by_id($_SESSION["sina"]["uid"]);
            $this->assign("pwd","lqqa123456");
            $this->assign("nick",$user_message["screen_name"]);
            if ($user_message["gender"]=="m") {

                $this->assign("gender","1");

            } else {

                if ($user_message["gender"]=="f") {

                    $this->assign("gender","2");

                }

            }
        }

        $this->display();

    }
	
	public function step3()
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
		//$this->_register_model->sendActivationEmail($uid);
		$this->assign('Code',$_GET['code']);
		$this->assign('User',$user);
		$this->display();	
	}
	
	public function step4()
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
		//$this->_register_model->sendActivationEmail($uid);
		$this->assign('Code',$_GET['code']);
		$this->assign('User',$user);
		$this->display();	
	}
	
	public function step5()
	{
		if(empty($_GET['uid']) || empty($_GET['code'])){
			$this->error('参数错误');
		}
		$uid = intval($_GET['uid']);
		$code = $_GET['code'];
		$user = $this->_user_model->getUserInfo($uid);
		if($user['invite_code'] != $code)
		{
			$this->error('参数错误');
		}
		$this->assign('uid',$uid);
		$this->assign('code',$code);
		
		//顶级专家
		$TopExpert = model('user')->getUserInfo(3);
		$user_count = model ( 'UserData' )->getUserDataByUids ( array(3) );
		//print_r($user_count);
		$this->assign ( 'TopExpert_UserCount', $user_count );
		$this->assign('TopExpert',$TopExpert);
		
		//认证专家
		$uids = model('UserGroupLink')->getUserByGroupID(8, 4);
		$user_count = model ( 'UserData' )->getUserDataByUids ($uids);
		$authenticateExpert = model('user')->getUserInfoByUids($uids);
		//print_r($authenticateExpert);
		$this->assign ( 'authenticateExpert_UserCount', $user_count );
		$this->assign('authenticateExpert',$authenticateExpert);
		
		//跟你有关的
		$where = " (`invite_code` = $code and `uid` != $uid) or (`area` = ".$user['area'].") ";
		$uidList = model('user')->field('uid')->where($where)->order('`uid` desc')->findAll();
		$uids = getSubByKey($uidList, 'uid');
		$userList = model('user')->getUserInfoByUids($uids);
		$this->assign("youguanCount",count($uids));
		$this->assign('userList',$userList);
		
		//推荐用户
		$recommendwhere = " `key` = 'weibo_count' or `key` = 'answer_count' ";
		//$recommendwhere = " `key` = 'weibo_count' ";
		$recommendData = model('UserData')->field(uid)->where($recommendwhere)->order('`value` desc')->findPage(70);
		$uids = getSubByKey($recommendData['data'], 'uid');
		$recommenduserList = model('user')->getUserInfoByUids($uids);
		//print_r($recommenduserList);
		$this->assign("tuijianCount",count($uids));
		$this->assign('recommendUserList',$recommenduserList);
		$this->display();	
	}

	/**
	 * 注册流程 - 执行第一步骤
	 * 输入邀请码
	 */
	public function doStep1(){	
		$return  = array('status'=>0,'data'=>L('邀请码错误'));
		if(empty($_POST['Code'])){
			$return['data'] = L('参数错误');
			echo json_encode($return);exit();
		}

        $result=$this->CheckInviteCode($_POST['Code']);

        if($result==0)
            $return  = array('status'=>0,'data'=>L('邀请码不存在'));
		else if($result==1)
            $return  = array('status'=>1,'data'=>L('邀请码验证成功'));
		else if($result==2)
            $return  = array('status'=>0,'data'=>L('邀请码已被使用'));
		else if($result==3)
            $return  = array('status'=>0,'data'=>L('邀请码限定次数已用完'));

        $_SESSION["invite_code"]=$_POST['Code'];

		echo json_encode($return);exit();

        echo json_encode($return);exit();
	}

    /**
     * 注册流程 - 执行第二步骤
     * 填写注册信息
     */
    public function doStep2() {
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

        $uid = $this->_user_model->addUser($user);
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

            $this->redirect('public/Register/step3', array('uid'=>$uid,'code'=>$_GET['code']));
        }
        else
        {
            $this->error(L('PUBLIC_REGISTER_FAIL'));			// 注册失败
        }
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
		if(empty($_GET['code']) || empty($_GET['uid'])){
			$this->error("参数错误");
		} 
		$uid = intval($_GET['uid']);
		
		$user = $this->_user_model->getUserInfo($uid);
		if($user['invite_code'] != $_GET['code'])
		{
			$this->error('参数错误');
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
		$this->_user_model->cleanCache ( array($uid) );
		$this->redirect('public/Register/step5', array('uid'=>$uid, 'code'=>$_GET['code']));
	}
	
	public function doStep5()
	{
		if(empty($_GET['code']) || empty($_GET['uid'])){
			$this->error("参数错误");
		} 
		$uid = intval($_GET['uid']);
		
		$user = $this->_user_model->getUserInfo($uid);
		if($user['invite_code'] != $_GET['code'])
		{
			$this->error('参数错误');
		}
		
		$uids = explode(',', $_POST['FollowingList']);
		foreach($uids as $k=>$v)
		{
			model('Follow')->doFollow($uid,$v);
		}
		
		//初始化完成
		$this->_register_model->overUserInit($uid);
		$this->_user_model->cleanCache ( array($uid) );
		
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
			if($user_info['location'] == '')
			{
				$this->redirect('public/Register/step4', array('uid'=>$user_info['uid'], 'code'=>$user_info['invite_code']));
			}
			else if($user_info['is_init'] == 0)
			{
				$this->redirect('public/Register/step5', array('uid'=>$user_info['uid'], 'code'=>$user_info['invite_code']));
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
			$this->assign('jumpUrl', U('public/Register/step4', array('uid'=>$user_info['uid'], 'code'=>$user_info['invite_code'])));
			$this->success($this->_register_model->getLastError());
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