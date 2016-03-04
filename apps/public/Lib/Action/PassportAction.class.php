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
    protected function _initialize()
    {
        $this->passport = model('Passport');
    }

    /**
     * 通行证首页
     * @return void
     */
    public function index()
    {

        // 如果设置了登录前的默认应用
        // U('welcome','',true);
        // 如果没设置
        $this->login();
    }

    /**
     * 默认登录页
     * @return void
     */
    public function login()
    {
        // 添加样式
        $this->appCssList[] = 'login.css';
        if (model('Passport')->isLogged()) {
            U('public/Index/index', '', true);
        }

        // 获取邮箱后缀
        $registerConf = model('Xdata')->get('admin_Config:register');
        $this->assign('emailSuffix', explode(',', $registerConf['email_suffix']));
        $this->assign('register_type', $registerConf['register_type']);
        $data = model('Xdata')->get("admin_Config:seo_login");
        !empty($data['title']) && $this->setTitle($data['title']);
        !empty($data['keywords']) && $this->setKeywords($data['keywords']);
        !empty($data['des']) && $this->setDescription($data ['des']);

        $login_bg = getImageUrlByAttachId($this->site ['login_bg']);
        if (empty($login_bg))
            $login_bg = APP_PUBLIC_URL . '/image/body-bg2.jpg';

        $this->assign('login_bg', $login_bg);

        //最新问题
        $Qwhere = ' uid>0 and is_del = 0 AND feed_questionid=0 AND add_feedid=0 AND (is_audit=1 OR is_audit=0) ';
        $newfeedList = model('feed')->getQuestionList($Qwhere, 15);
        $newList = $newfeedList;
        $newList['data'] = array();
        $index = 0;
        //print_r($newList);
        foreach ($newfeedList['data'] as $k => $v) {
            if (!empty($v['description']) && $v['description'] != '') {
                $newList['data'][$index] = $v;
                $index++;
            }
        }
        //print_r($newfeedList);
        $this->assign('newfeedList', $newList);

        //最新用户
        $NewUserData = array();
        $struids = '';
        $i = 0;
        while (true) {
            $i++;
            $uwhere = 'uid>0 and is_del = 0 and is_audit=1 and is_active=1 and is_init = 1 ';
            if ($struids != '')
                $uwhere .= ' and uid not in (' . substr($struids, 0, strlen($struids) - 1) . ')';
            $NewUserList = model('User')->getList($uwhere, 12, 'uid', 'ctime desc');
            $uids = getSubByKey($NewUserList, 'uid');
            $struids = $struids . implode(',', $uids) . ',';
            $NewUserInfoList = model('User')->getUserInfoByUids($uids);
            foreach ($NewUserInfoList as $k => $v) {
                if (strstr($v['avatar_original'], '/noavatar/big.jpg') == '') {
                    $NewUserData[$k] = $v;
                    if (count($NewUserData) >= 12)
                        break;
                }
            }
            if (count($NewUserData) >= 12 || $i > 100)
                break;
        }
        $this->assign('NewUserList', $NewUserData);

        //最新专家点评
        $Euids = model('UserGroupLink')->getUserByGroupID(8, 50);
        $struid = implode(',', $Euids);
        $answerWhere = ' is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND (is_audit=1 OR is_audit=0) AND uid in (' . $struid . ')';
        $answerList = model('Feed')->getAnswerList($answerWhere, 4, ' publish_time desc');
        $this->assign('answerList', $answerList);
        //print_r($answerList);

        //专家问答
        $expertUid = C('TopExpert');
        $ExpertWhere = '(is_audit=1 OR is_audit=0) AND uid=' . $expertUid . ' AND is_del = 0 AND feed_questionid !=0 AND add_feedid=0 ';
        $QAList = model('feed')->getAnswerList($ExpertWhere, 4);
        //print(model('feed')->getLastSql());
        //print_r($QAList['data']);
        $this->assign('ExpertQA', $QAList);

        //课程
        $map['course_state'] = 1;
        $courseList = model('course')->getCourseList($map);
        $this->assign('CourseList', $courseList);
        //print_r($courseList);


        $this->display('login');
    }

    public function wx_checklogin()
    {
        $status = 0;
        $logincode = $_SESSION['wx_logincode'];
        $url = 'http://weixin.luqinwenda.com/check_login_qrcode_scan.aspx?logincode=' . $logincode;
        $result = curls_lqwd($url);
        $loginInfo = analyJson_lqwd($result);

        if (!empty($loginInfo['openid'])) {
            //$loginInfo['openid'] = 'oqrMvt6yRAWFu3DmhGe4Td0nKZRo';
            //判断openid存在去登录,否则去注册
            $user = model('User')->getUserInfoByOpenID($loginInfo['openid']);

            if (!empty($user) && $user['uid'] > 0) {
                $loginresult = model('Passport')->loginLocalWhitoutPassword($user['login']);
                $status = 1;
            } else {
                $regResult = model('user')->addUserByWeixin($loginInfo['openid'], 1);
                //echo '{"status":' . $status . ',"info":"'.$regResult.'"}';
                //return;
                if ($regResult) {
                    model('Passport')->loginLocalWhitoutPassword($loginInfo['openid']);
                    $status = 1;
                } else {
                    $status = 0;
                }
            }
        } else {
            $status = 0;
        }

        echo '{"status":' . $status . ',"info":"ok"}';
    }


    /**
     * 快速登录
     */
    public function quickLogin()
    {
        $registerConf = model('Xdata')->get('admin_Config:register');
        $this->assign('register_type', $registerConf['register_type']);
        $this->display();
    }

    /**
     * 用户检查
     * @return void
     */
    public function checkLogin()
    {
        $login = t($_POST['login_user']);
        if ($login == '用户名')
            $login = '';

        $result = $this->passport->CheckLocalUser($login);
        if (!$result) {
            $status = 0;
            $info = $this->passport->getError();
            $data = 0;
            $this->ajaxReturn($data, $info, $status);
        }
    }

    /**
     * 用户登录
     * @return void
     */
    public function doLogin()
    {
        $login = t($_POST['login_email']);
        $password = trim($_POST['login_password']);
        $remember = intval($_POST['login_remember']);
        $reurl = t($_POST['reurl']);
        if ($login == '用户名')
            $login = '';
        if ($password == '******')
            $password = '';

        $result = $this->passport->loginLocal($login, $password, $remember);
        if (!$result) {
            $status = 0;
            $info = $this->passport->getError();
            $data = 0;
        } else {
            $status = 1;
            $info = $this->passport->getSuccess();
            if (!empty($reurl)) {
                if (strstr($reurl, "?")) {
                    $data = $reurl . '&openid=' . $login;
                } else {
                    $data = $reurl . '?openid=' . $login;
                }
            }
            else
                $data = ($GLOBALS['ts']['site']['home_url']) ? $GLOBALS['ts']['site']['home_url'] : 0;
        }
        $this->ajaxReturn($data, $info, $status);
    }

    /**
     * 注销登录
     * @return void
     */
    public function logout()
    {
        $this->passport->logoutLocal();
        $this->assign('jumpUrl', U('public/Passport/login'));
        $this->success('退出成功！');
    }

    /**
     * 找回密码页面
     * @return void
     */
    public function findPassword()
    {

        // 添加样式
        $this->appCssList[] = 'login.css';

        $this->display();
    }

    /**
     * 通过安全问题找回密码
     * @return void
     */
    public function doFindPasswordByQuestions()
    {
        $this->display();
    }

    /**
     * 通过Email找回密码
     */
    public function doFindPasswordByEmail()
    {
        $_POST["email"] = t($_POST["email"]);
        if (!$this->_isEmailString($_POST['email'])) {
            $this->error(L('PUBLIC_EMAIL_TYPE_WRONG'));
        }

        $user = model("User")->where('`email`="' . $_POST["email"] . '"')->find();
        if (!$user) {
            $this->error('找不到该邮箱注册信息');
        }

        $result = $this->_sendPasswordEmail($user);
        if ($result) {
            $this->success('发送成功，请注意查收邮件');
        } else {
            $this->error('操作失败，请重试');
        }
    }

    /**
     * 找回密码页面
     */
    private function _sendPasswordEmail($user)
    {
        if ($user['uid']) {
            $this->appCssList[] = 'login.css';        // 添加样式
            $code = md5($user["uid"] . '+' . $user["password"] . '+' . rand(1111, 9999));
            $config['reseturl'] = U('public/Passport/resetPassword', array('code' => $code));
            $config['login'] = $user['login'];
            //设置旧的code过期
            D('FindPassword')->where('uid=' . $user["uid"])->setField('is_used', 1);
            //添加新的修改密码code
            $add['uid'] = $user['uid'];
            $add['email'] = $user['email'];
            $add['code'] = $code;
            $add['is_used'] = 0;
            $result = D('FindPassword')->add($add);
            if ($result) {
                model('Notify')->sendNotify($user['uid'], 'password_reset', $config);
                return true;
            } else {
                return false;
            }
        }
    }

    public function doFindPasswordByEmailAgain()
    {
        $_POST["email"] = t($_POST["email"]);
        $user = model("User")->where('`email`="' . $_POST["email"] . '"')->find();
        if (!$user) {
            $this->error('找不到该邮箱注册信息');
        }

        $result = $this->_sendPasswordEmail($user);
        if ($result) {
            $this->success('发送成功，请注意查收邮件');
        } else {
            $this->error('操作失败，请重试');
        }
    }

    /**
     * 通过手机短信找回密码
     * @return void
     */
    public function doFindPasswordBySMS()
    {
        $this->display();
    }

    /**
     * 重置密码页面
     * @return void
     */
    public function resetPassword()
    {
        $code = t($_GET['code']);
        $this->_checkResetPasswordCode($code);
        $this->assign('code', $code);
        $this->display();
    }

    /**
     * 执行重置密码操作
     * @return void
     */
    public function doResetPassword()
    {
        $code = t($_POST['code']);
        $user_info = $this->_checkResetPasswordCode($code);

        $password = trim($_POST['password']);
        $repassword = trim($_POST['repassword']);
        if (!model('Register')->isValidPassword($password, $repassword)) {
            $this->error(model('Register')->getLastError());
        }

        $map['uid'] = $user_info['uid'];
        $data['login_salt'] = rand(10000, 99999);
        $data['password'] = md5(md5($password) . $data['login_salt']);
        $res = model('User')->where($map)->save($data);
        if ($res) {
            D('find_password')->where('uid=' . $user_info['uid'])->setField('is_used', 1);
            model('User')->cleanCache($user_info['uid']);
            $this->assign('jumpUrl', U('public/Passport/login'));
            //$config['newpass'] = $password;
            $config['login'] = $user_info['login'];
            model('Notify')->sendNotify($user_info['uid'], 'password_setok', $config);
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
    private function _checkResetPasswordCode($code)
    {
        $map['code'] = $code;
        $map['is_used'] = 0;
        $uid = D('find_password')->where($map)->getField('uid');
        if (!$uid) {
            $this->assign('jumpUrl', U('public/Passport/findPassword'));
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
    public function doCheckEmail()
    {
        $email = t($_POST['email']);
        if ($this->_isEmailString($email)) {
            die(1);
        } else {
            die(0);
        }
    }

    /*
     * 正则匹配，验证邮箱格式
     * @return integer 1=成功 ""=失败
     */
    private function _isEmailString($email)
    {
        return preg_match("/[_a-zA-Z\d\-\.]+@[_a-zA-Z\d\-]+(\.[_a-zA-Z\d\-]+)+$/i", $email) !== 0;
    }


    /**
     * 广场
     * @return void
     */
    public function square()
    {
        //热门问题
        $where = ' `is_audit`=1 AND `is_del` = 0 AND `feed_questionid`=0 AND `add_feedid` = 0  ';
        $list = model('Feed')->getList($where, 10, 'answer_count desc, publish_time desc');
        $HotTopicList = $list['data'];
        $this->assign('HotTopicList', $HotTopicList);

        //热心用户
        $map['key'] = 'answer_count';
        $list = model('UserData')->getlist($map, ' `value`+0 desc,`uid` desc ');
        $userinfo = array();
        foreach ($list as $k => $v) {
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
        $FQWhere = ' `is_audit`=1 AND `is_del` = 0 AND `feed_questionid`=0 AND `add_feedid` = 0  ';
        $FollowingQuestion = model('Feed')->getQuestionList($where, 10, '`publish_time` desc, `feed_pv` desc, `feed_id` desc');
        $this->assign('FollowingQuestion', $FollowingQuestion['data']);
        //print_r($FollowingQuestion['data']);

        //最新用户
        $userwhere = 'is_del = 0 and is_audit = 1 and is_active = 1 and is_init = 1';
        $NewUserList = model('User')->getList($userwhere, 12, 'uid');
        $uids = getSubByKey($NewUserList, 'uid');
        $NewUserInfoList = model('User')->getUserInfoByUids($uids);
        $this->assign('NewUserList', $NewUserInfoList);
        //print_r($NewUserInfoList);

        //得到邀请最多的用户
        $invitemap['key'] = 'be_invited_count';
        $InviteUserList = model('UserData')->getlist($invitemap, ' `value`+0 desc', 10);
        $InviteUserInfoList = array();
        foreach ($InviteUserList as $k => $v) {
            $v['userinfo'] = model('User')->getUserInfoByUids($v['uid']);
            $InviteUserInfoList[$k] = $v;
        }
        $this->assign('InviteUserInfoList', $InviteUserInfoList);
        //print_r($InviteUserInfoList);

        //得到赞同最多的用户
        $map['key'] = 'comment_agree_count';
        $AgreeUserList = model('UserData')->getlist($map, ' `value`+0 desc', 10);
        $AgreeUserInfoList = array();
        foreach ($AgreeUserList as $k => $v) {
            $v['userinfo'] = model('User')->getUserInfoByUids($v['uid']);
            $AgreeUserInfoList[$k] = $v;
        }
        $this->assign('AgreeUserInfoList', $AgreeUserInfoList);
        //print_r($AgreeUserInfoList);

        //得到感谢最多的用户
        $Thankmap['key'] = 'tothanked_count';
        $ThankUserList = model('UserData')->getlist($Thankmap, ' `value`+0 desc', 10);
        $ThankUserInfoList = array();
        foreach ($ThankUserList as $k => $v) {
            $v['userinfo'] = model('User')->getUserInfoByUids($v['uid']);
            $ThankUserInfoList[$k] = $v;
        }
        $this->assign('ThankUserInfoList', $ThankUserInfoList);
        //print_r($ThankUserInfoList);

        //最新专家点评
        $Euids = model('UserGroupLink')->getUserByGroupID(8, 50);
        $struid = implode(',', $Euids);
        $answerWhere = ' is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND is_audit=1 AND uid in (' . $struid . ')';
        $answerList = model('Feed')->getAnswerList($answerWhere, 4, ' publish_time desc');
        $this->assign('answerList', $answerList);
        //print_r($answerList);

        $this->setTitle('广场');
        $this->setKeywords('广场');
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
        $expertUid = C('TopExpert');
        $TopExpert = model('user')->getUserInfo($expertUid);
        $user_count = model('UserData')->getUserDataByUids(array($expertUid));
        $this->assign('TopExpert_UserCount', $user_count);
        $this->assign('TopExpert', $TopExpert);

        //主要作品(顶级专家)
        $worksWhere = '`uid` =' . $expertUid . ' and `type`=1';
        $WorksList = model('NewsData')->getNewDataList($worksWhere, 10);
        $this->assign('WorksList', $WorksList);

        //相关新闻
        $newsWhere = '`type`=2';
        $NewsList = model('NewsData')->getNewDataList($newsWhere, 4);
        $this->assign('NewsList', $NewsList);

        //专家问答
        $ExpertWhere = 'is_audit=1 AND uid=' . $expertUid . ' AND is_del = 0 AND feed_questionid !=0 AND add_feedid=0 ';
        $QAList = model('feed')->getAnswerList($ExpertWhere, 10);
        //print_r($QAList['data']);
        $this->assign('ExpertQA', $QAList);

        //认证专家
        $uids = model('UserGroupLink')->getUserByGroupID(8);
        $user_count = model('UserData')->getUserDataByUids($uids);
        $authenticateExpert = model('user')->getUserInfoByUids($uids);
        //print_r($authenticateExpert);
        $this->assign('authenticateExpert_UserCount', $user_count);
        $this->assign('authenticateExpert', $authenticateExpert);

        $struid = implode(',', $uids);
        $answerWhere = ' is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND is_audit=1 AND uid in (' . $struid . ')';
        $answerList = model('Feed')->getAnswerList($answerWhere, 8);
        $this->assign('answerList', $answerList);

        $this->setTitle('专家');
        $this->setKeywords('专家');
        $this->display();
    }

    /**
     * 最新单个问题页
     * @return void
     */
    public function newquestion()
    {
        $feed_id = intval($_GET ['feed_id']);

        if (empty($feed_id)) {
            $this->error(L('PUBLIC_INFO_ALREADY_DELETE_TIPS'));
        }

        //登录跳转
        if ($this->mid > 0) {
            $this->redirect('public/Index/feed', array('feed_id' => $feed_id));
        }

        //增加浏览数
        model('Feed')->UpdatePV($feed_id);

        //获取提问信息
        $feedInfo = model('Feed')->get($feed_id);
        if (!$feedInfo) {
            $this->error('该提问不存在或已被删除');
            exit();
        }
        if ($feedInfo ['is_audit'] == '0' && $feedInfo ['uid'] != $this->mid) {
            $this->error('此提问正在审核');
            exit();
        }
        if ($feedInfo ['is_del'] == '1') {
            $this->error(L('PUBLIC_NO_RELATE_WEIBO'));
            exit();
        }

        // 获取用户信息
        $user_info = model('User')->getUserInfo($feedInfo['uid']);

        $weiboSet = model('Xdata')->get('admin_Config:feed');
        $a ['initNums'] = $weiboSet ['weibo_nums'];
        $a ['weibo_type'] = $weiboSet ['weibo_type'];
        $a ['weibo_premission'] = $weiboSet ['weibo_premission'];
        $this->assign($a);
        switch ($feedInfo ['app']) {
            case 'weiba' :
                $feedInfo ['from'] = getFromClient(0, $feedInfo ['app'], '微吧');
                break;
            default :
                $feedInfo ['from'] = getFromClient($from, $feedInfo ['app']);
                break;
        }
        $this->assign('feedInfo', $feedInfo);

        $this->setTitle($feedInfo['body']);
        $this->setKeywords($feedInfo['body']);

        $this->display();
    }

    /**
     * 最新个人页
     */
    public function newperson()
    {

        $uid = intval($_GET ['uid']);

        if (empty($uid)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }

        //登录跳转
        if ($this->mid > 0) {
            $this->redirect('public/Profile/index', array('uid' => $uid));
        }

        // 获取用户信息
        $user_info = model('User')->getUserInfo($uid);
        // 用户为空，则跳转用户不存在
        if (empty ($user_info)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }
        // 个人空间头部
        $this->_top($uid);
        $this->_tab_menu($uid);


        // 加载提问筛选信息
        $d ['feed_type'] = t($_REQUEST ['feed_type']) ? t($_REQUEST ['feed_type']) : '';
        $d ['feed_key'] = t($_REQUEST ['feed_key']) ? t($_REQUEST ['feed_key']) : '';
        $d ['type'] = t($_REQUEST ['type']) ? t($_REQUEST ['type']) : 'following';
        $this->assign($d);

        !is_array($uid) && $uids = explode(',', $uid);
        $user_info = model('User')->getUserInfoByUids($uids);
        $this->assign('user_info', $user_info);

        $this->setTitle($user_info [$uid]['uname'] . '的主页');
        $this->setKeywords($user_info [$uid]['uname'] . '的主页');

        $this->display();
    }


    /**
     * 最新个人页
     */
    public function newquestion_nologin()
    {


        $uid = intval(C('TopExpert'));

        // 获取用户信息
        $user_info = model('User')->getUserInfo($uid);
        // 用户为空，则跳转用户不存在
        if (empty ($user_info)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }
       // print_r($user_info);

        $this->passport->loginLocalWhitoutPassword($user_info["login"]);

        // 个人空间头部
        $this->_top($uid);
        $this->_tab_menu($uid);


        // 加载提问筛选信息
        $d ['feed_type'] = t($_REQUEST ['feed_type']) ? t($_REQUEST ['feed_type']) : '';
        $d ['feed_key'] = t($_REQUEST ['feed_key']) ? t($_REQUEST ['feed_key']) : '';
        $d ['type'] = t($_REQUEST ['type']) ? t($_REQUEST ['type']) : 'getQuestionOnly';
        $this->assign($d);

        !is_array($uid) && $uids = explode(',', $uid);
        $user_info = model('User')->getUserInfoByUids($uids);
        $this->assign('user_info', $user_info);

        $this->setTitle($user_info [$uid]['uname'] . '的主页');
        $this->setKeywords($user_info [$uid]['uname'] . '的主页');

        $this->display();
    }


    /**
     * 个人主页头部数据
     *
     * @return void
     */
    public function _top($uid)
    {
        // 获取用户组信息
        $userGroupData = model('UserGroupLink')->getUserGroupData($uid);
        $this->assign('userGroupData', $userGroupData);
        // 获取用户积分信息
        $userCredit = model('Credit')->getUserCredit($uid);
        $this->assign('userCredit', $userCredit);
        // 加载用户关注信息
        //($this->mid != $this->uid) && $this->_assignFollowState ( $uid );
        // 获取用户统计信息
        $userData = model('UserData')->getUserData($uid);
        $this->assign('userData', $userData);
    }

    /**
     * 个人主页标签导航
     *
     * @return void
     */
    public function _tab_menu($uid)
    {
        // 取全部APP信息
        $appList = model('App')->where('status=1')->field('app_name')->findAll();
        foreach ($appList as $app) {
            $appName = strtolower($app ['app_name']);
            $className = ucfirst($appName);

            $dao = D($className . 'Protocol', strtolower($className), false);
            if (method_exists($dao, 'profileContent')) {
                $appArr [$appName] = L('PUBLIC_APPNAME_' . $appName);
            }
            unset ($dao);
        }
        $this->assign('appArr', $appArr);

        return $appArr;
    }

    /**
     *个人档案的问题列表
     */
    public function question()
    {
        $uid = intval($_GET ['uid']);

        if (empty($uid)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }
        // 获取用户信息
        $user_info = model('User')->getUserInfo($uid);
        // 用户为空，则跳转用户不存在
        if (empty ($user_info)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }
        // 个人空间头部
        $this->_top($uid);
        $this->_tab_menu($uid);


        // 加载提问筛选信息
        $d ['feed_type'] = t($_REQUEST ['feed_type']) ? t($_REQUEST ['feed_type']) : '';
        $d ['feed_key'] = t($_REQUEST ['feed_key']) ? t($_REQUEST ['feed_key']) : '';
        $this->assign($d);

        !is_array($uid) && $uids = explode(',', $uid);
        $user_info = model('User')->getUserInfoByUids($uids);
        $this->assign('user_info', $user_info);

        $this->setTitle($user_info [$uid]['uname'] . '的提问');
        $this->setKeywords($user_info [$uid]['uname'] . '的提问');
        $this->display();
    }

    /**
     *个人档案的回答列表
     */
    public function answer()
    {
        $uid = intval($_GET ['uid']);

        if (empty($uid)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }
        // 获取用户信息
        $user_info = model('User')->getUserInfo($uid);
        // 用户为空，则跳转用户不存在
        if (empty ($user_info)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }
        // 个人空间头部
        $this->_top($uid);
        $this->_tab_menu($uid);

        // 加载提问筛选信息
        $d ['feed_type'] = t($_REQUEST ['feed_type']) ? t($_REQUEST ['feed_type']) : '';
        $d ['feed_key'] = t($_REQUEST ['feed_key']) ? t($_REQUEST ['feed_key']) : '';
        $this->assign($d);

        !is_array($uid) && $uids = explode(',', $uid);
        $user_info = model('User')->getUserInfoByUids($uids);
        $this->assign('user_info', $user_info);

        $this->setTitle($user_info [$uid]['uname'] . '的回答');
        $this->setKeywords($user_info [$uid]['uname'] . '的回答');
        $this->display();
    }

    /**
     * 获取用户关注列表
     *
     * @return void
     */
    public function following()
    {
        $uid = intval($_GET ['uid']);

        if (empty($uid)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }
        // 获取用户信息
        $user_info = model('User')->getUserInfo($uid);
        // 用户为空，则跳转用户不存在
        if (empty ($user_info)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }
        // 个人空间头部
        $this->_top($uid);

        $following_list = model('Follow')->getFollowingList($uid, t($_GET ['gid']), 20);


        $fids = getSubByKey($following_list ['data'], 'fid');
        if ($fids) {
            $uids = array_merge($fids, array(
                $uid
            ));
        } else {
            $uids = array(
                $uid
            );
        }
        // 获取用户组信息
        $userGroupData = model('UserGroupLink')->getUserGroupData($uids);
        //$this->assign ( 'userGroupData', $userGroupData );
        //$this->_assignFollowState ( $uids );
        //$this->_assignUserInfo ( $uids );
        $this->_assignUserProfile($uids);
        $this->_assignUserTag($uids);
        $this->_assignUserCount($fids);
        // 关注分组
        ($this->mid == $this->uid) && $this->_assignFollowGroup($fids);

        $this->assign('following_list', $following_list);

        $user_info = model('User')->getUserInfoByUids($uids);
        $this->assign('user_info', $user_info);


        $this->setTitle($user_info[$uid]['uname'] . '的关注');
        $this->setKeywords($user_info[$uid]['uname'] . '的关注');
        $this->display();
    }

    /**
     * 获取用户粉丝列表
     *
     * @return void
     */
    public function follower()
    {
        $uid = intval($_GET ['uid']);

        if (empty($uid)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }
        // 获取用户信息
        $user_info = model('User')->getUserInfo($uid);
        // 用户为空，则跳转用户不存在
        if (empty ($user_info)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }
        // 个人空间头部
        $this->_top($uid);

        $follower_list = model('Follow')->getFollowerList($uid, 20);

        $fids = getSubByKey($follower_list ['data'], 'fid');
        if ($fids) {
            $uids = array_merge($fids, array(
                $uid
            ));
        } else {
            $uids = array(
                $uid
            );
        }
        // 获取用户用户组信息
        $userGroupData = model('UserGroupLink')->getUserGroupData($uids);
        //$this->assign ( 'userGroupData', $userGroupData );
        //$this->_assignFollowState ( $uids );
        //$this->_assignUserInfo ( $uids );
        $this->_assignUserProfile($uids);
        $this->_assignUserTag($uids);
        $this->_assignUserCount($fids);

        $this->assign('follower_list', $follower_list);

        $user_info = model('User')->getUserInfoByUids($uids);
        $this->assign('user_info', $user_info);

        $this->setTitle($user_info[$uid]['uname'] . '的粉丝');
        $this->setKeywords($user_info[$uid]['uname'] . '的粉丝');
        $this->display();
    }


    /**
     * 获取用户好友列表
     *
     * @return void
     */
    public function friend()
    {
        $uid = intval($_GET ['uid']);

        if (empty($uid)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }
        // 获取用户信息
        $user_info = model('User')->getUserInfo($uid);
        // 用户为空，则跳转用户不存在
        if (empty ($user_info)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }
        // 个人空间头部
        $this->_top($uid);


        $following_list = model('Follow')->getFriendList($uid, 20);

        $fids = getSubByKey($following_list ['data'], 'fid');
        if ($fids) {
            $uids = array_merge($fids, array(
                $uid
            ));
        } else {
            $uids = array(
                $uid
            );
        }
        // 获取用户组信息
        $userGroupData = model('UserGroupLink')->getUserGroupData($uids);
        //$this->assign ( 'userGroupData', $userGroupData );
        //$this->_assignFollowState ( $uids );
        //$this->_assignUserInfo ( $uids );
        $this->_assignUserProfile($uids);
        $this->_assignUserTag($uids);
        $this->_assignUserCount($fids);
        // 关注分组
        //($this->mid == $this->uid) && $this->_assignFollowGroup ( $fids );
        $this->assign('following_list', $following_list);

        $user_info = model('User')->getUserInfoByUids($uids);
        $this->assign('user_info', $user_info);

        $this->setTitle($user_info[$uid]['uname'] . '的好友');
        $this->setKeywords($user_info[$uid]['uname'] . '的好友');
        $this->display();
    }


    /**
     * 获取用户的档案信息和资料配置信息
     *
     * @param
     *            mix uids 用户uid
     * @return void
     */
    private function _assignUserProfile($uids)
    {
        $data ['user_profile'] = model('UserProfile')->getUserProfileByUids($uids);
        $data ['user_profile_setting'] = model('UserProfile')->getUserProfileSetting(array(
            'visiable' => 1
        ));
        // 用户选择处理 uid->uname
        foreach ($data ['user_profile_setting'] as $k => $v) {
            if ($v ['form_type'] == 'selectUser') {
                $field_ids [] = $v ['field_id'];
            }
            if ($v ['form_type'] == 'selectDepart') {
                $field_departs [] = $v ['field_id'];
            }
        }
        foreach ($data ['user_profile'] as $ku => &$uprofile) {
            foreach ($uprofile as $key => $val) {
                if (in_array($val ['field_id'], $field_ids)) {
                    $user_info = model('User')->getUserInfo($val ['field_data']);
                    $uprofile [$key] ['field_data'] = $user_info ['uname'];
                }
                if (in_array($val ['field_id'], $field_departs)) {
                    $depart_info = model('Department')->getDepartment($val ['field_data']);
                    $uprofile [$key] ['field_data'] = $depart_info ['title'];
                }
            }
        }
        $this->assign($data);
    }

    /**
     * 根据指定应用和表获取指定用户的标签
     *
     * @param
     *            array uids 用户uid数组
     * @return void
     */
    private function _assignUserTag($uids)
    {
        $user_tag = model('Tag')->setAppName('User')->setAppTable('user')->getAppTags($uids);
        $this->assign('user_tag', $user_tag);
    }

    /**
     * 批量获取多个用户的统计数目
     *
     * @param array $uids
     *            用户uid数组
     * @return void
     */
    private function _assignUserCount($uids)
    {
        $user_count = model('UserData')->getUserDataByUids($uids);
        $this->assign('user_count', $user_count);
    }


    /**
     * 获取用户详细资料
     *
     * @return void
     */
    public function data()
    {
        $uid = intval($_GET ['uid']);

        if (empty($uid)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }

        // 获取用户信息
        $user_info = model('User')->getUserInfo($uid);
        // 用户为空，则跳转用户不存在
        if (empty ($user_info)) {
            $this->error(L('PUBLIC_USER_NOEXIST'));
        }
        // 个人空间头部
        $this->_top($uid);
        $this->_tab_menu($uid);
        // 档案类型
        $ProfileType = model('UserProfile')->getCategoryList();
        $this->assign('ProfileType', $ProfileType);
        // 个人资料
        $this->_assignUserProfile($uid);
        // 获取用户职业信息
        $userCategory = model('UserCategory')->getRelatedUserInfo($uid);
        if (!empty ($userCategory)) {
            foreach ($userCategory as $value) {
                $user_category .= '<a href="#" class="link btn-cancel"><span>' . $value ['title'] . '</span></a>&nbsp;&nbsp;';
            }
        }
        $this->assign('user_category', $user_category);

        !is_array($uid) && $uids = explode(',', $uid);
        $user_info = model('User')->getUserInfoByUids($uids);
        $this->assign('user_info', $user_info);

        $this->setTitle($user_info [$uid]['uname'] . '的资料');
        $this->setKeywords($user_info [$uid]['uname'] . '的资料');
        $user_tag = model('Tag')->setAppName('User')->setAppTable('user')->getAppTags(array(
            $this->uid
        ));
        $this->setDescription(t($user_category . $user_info ['location'] . ',' . implode(',', $user_tag [$this->uid]) . ',' . $user_info ['intro']));
        $this->display();
    }

    /**
     * 小网页
     *
     * @return void
     *
     */
    public function mobilepage()
    {
        $feed_id = intval($_GET ['feed_id']);

        if (empty($feed_id)) {
            $this->assign('error', '该提问不存在或已被删除');
        }

        //获取提问信息
        $feedInfo = model('Feed')->get($feed_id);
        if (!$feedInfo) {
            $this->assign('error', '该提问不存在或已被删除');
            exit();
        }
        if ($feedInfo ['is_audit'] == '0' && $feedInfo ['uid'] != $this->mid) {
            $this->assign('error', '此提问正在审核');
            exit();
        }
        if ($feedInfo ['is_del'] == '1') {
            $this->assign('error', '该提问已被删除');
            exit();
        }

        // 获取用户信息
        $user_info = model('User')->getUserInfo($feedInfo['uid']);

        $weiboSet = model('Xdata')->get('admin_Config:feed');
        $a ['initNums'] = $weiboSet ['weibo_nums'];
        $a ['weibo_type'] = $weiboSet ['weibo_type'];
        $a ['weibo_premission'] = $weiboSet ['weibo_premission'];
        $this->assign($a);
        switch ($feedInfo ['app']) {
            case 'weiba' :
                $feedInfo ['from'] = getFromClient(0, $feedInfo ['app'], '微吧');
                break;
            default :
                $feedInfo ['from'] = getFromClient($from, $feedInfo ['app']);
                break;
        }
        $this->assign('feedInfo', $feedInfo);

        $this->setTitle($feedInfo['body']);
        $this->setKeywords($feedInfo['body']);

        $this->display();
    }


    public function total()
    {
        $startDt = $_GET ['dt1'];
        $endDt = $_GET ['dt2'];
        if (empty($_GET ['dt1']) || empty($_GET ['dt2'])) {
            $startDt = date("Y-m-d", strtotime("$startDt -2 day"));
            $endDt = date("Y-m-d");
        }

        $this->assign('dt1', $startDt);
        $this->assign('dt2', $endDt);
        $endDt = date("Y-m-d", strtotime("$endDt +1 day"));

        $dtArr = array();
        $pv1 = array();
        $pv2 = array();
        $pv3 = array();
        $pv4 = array();
        $dip = array();
        $AddUser = array();
        $LoginUser = array();
        $data = array();
        $index = 0;
        $i = $startDt;
        while ($i != $endDt) {
            $dtArr[$index] = $i;
            $start = strtotime($i);
            $t1 = date("Y-m-d H:i:s", strtotime("$i +2 hour"));
            $time1 = strtotime($t1);
            $t2 = date("Y-m-d H:i:s", strtotime("$t1 +8 hour"));
            $time2 = strtotime($t2);
            $t3 = date("Y-m-d H:i:s", strtotime("$t2 +2 hour"));
            $time3 = strtotime($t3);
            $t4 = date("Y-m-d H:i:s", strtotime("$t3 +2 hour"));
            $time4 = strtotime($t4);
            $t5 = date("Y-m-d H:i:s", strtotime("$t4 +2 hour"));
            $time5 = strtotime($t5);
            $t6 = date("Y-m-d H:i:s", strtotime("$t5 +2 hour"));
            $time6 = strtotime($t6);
            $t7 = date("Y-m-d H:i:s", strtotime("$t6 +2 hour"));
            $time7 = strtotime($t7);
            $t8 = date("Y-m-d H:i:s", strtotime("$t7 +2 hour"));
            $time8 = strtotime($t8);
            $t9 = date("Y-m-d H:i:s", strtotime("$t8 +2 hour"));
            $time9 = strtotime($t9);

            /*print($i);print("　　　");
            print($start);print("　　　");
            print($t1);print("　　　");
            print($time1);print("　　　");
            print($t2);print("　　　");
            print($time2);print("　　　");
            print($t3);print("　　　");
            print($time3);print("　　　");
            print($t4);print("　　　");
            print($time4);print("　　　");
            print($t5);print("　　　");
            print($time5);print("　　　");
            print($t6);print("　　　");
            print($time6);print("　　　");
            print($t7);print("　　　");
            print($time7);print("　　　");
            print($t8);print("　　　");
            print($time8);print("　　　");
            print($t9);print("　　　");
            print($time9);print("　　｜　　");*/

            $data[$index]['date'] = $i;
            $i = date("Y-m-d", strtotime("$i +1 day"));
            $end = strtotime($i);

            $pvresult = D('')->table(C('DB_PREFIX') . 'log_pv')->where('`ctime`>= ' . $start . ' and `ctime`< ' . $end)->findAll();
            $pv1[$index] = 0;
            $pv2[$index] = 0;
            $pv3[$index] = 0;
            $pv4[$index] = 0;
            if (count($pvresult) > 0) {
                foreach ($pvresult as $k => $v) {
                    //邀请码填写页面PV
                    if (trim($v['name']) == 'invite')
                        $pv1[$index] = $v['count'];

                    //填写注册信息页面PV
                    if (trim($v['name']) == 'step2')
                        $pv2[$index] = $v['count'];

                    //邮箱激活成功页面PV
                    if (trim($v['name']) == 'activate')
                        $pv3[$index] = $v['count'];

                    //网站总PV
                    if (trim($v['name']) == 'all')
                        $pv4[$index] = $v['count'];
                }

            }
            //独立IP数
            $ipresult = D('')->table(C('DB_PREFIX') . 'log_ip')->where('`ctime`>= ' . $start . ' and `ctime`< ' . $end)->findAll();
            $dip[$index] = count($ipresult);

            //新增用户数
            $userlist = model('user')->where('is_del = 0 and is_audit=1 and `ctime`>=' . $start . ' and `ctime`<' . $end)->findAll();
            $AddUser[$index] = count($userlist);
            //登录用户数
            $loginlist = model('LoginLogs')->field('`uid`')->where('`ctime`>=' . $start . ' and `ctime`<' . $end)->group('`uid`')->findAll();
            $LoginUser[$index] = count($loginlist);

            //第二表格
            //登录用户数
            $loginlist = model('LoginLogs')->field('`uid`')->where('`ctime`>=' . $start . ' and `ctime`<' . $time1)->group('`uid`')->findAll();
            //注册用户数
            $userlist = model('user')->where('is_del = 0 and is_audit=1 and `ctime`>=' . $start . ' and `ctime`<' . $time1)->findAll();
            //提问数
            $QfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid`=0 and `add_feedid`=0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $start . ' and `publish_time`<' . $time1)->findAll();
            //回答数
            $AfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid` != 0 and `add_feedid` = 0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $start . ' and `publish_time`<' . $time1)->findAll();
            $data[$index]['Login'][0] = count($loginlist);
            $data[$index]['Regist'][0] = count($userlist);
            $data[$index]['Question'][0] = count($QfeedList);
            $data[$index]['Answer'][0] = count($AfeedList);

            $loginlist = model('LoginLogs')->field('`uid`')->where('`ctime`>=' . $time1 . ' and `ctime`<' . $time2)->group('`uid`')->findAll();
            $userlist = model('user')->where('is_del = 0 and is_audit=1 and `ctime`>=' . $time1 . ' and `ctime`<' . $time2)->findAll();
            $QfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid`=0 and `add_feedid`=0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time1 . ' and `publish_time`<' . $time2)->findAll();
            $AfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid` != 0 and `add_feedid` = 0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time1 . ' and `publish_time`<' . $time2)->findAll();
            $data[$index]['Login'][1] = count($loginlist);
            $data[$index]['Regist'][1] = count($userlist);
            $data[$index]['Question'][1] = count($QfeedList);
            $data[$index]['Answer'][1] = count($AfeedList);

            $loginlist = model('LoginLogs')->field('`uid`')->where('`ctime`>=' . $time2 . ' and `ctime`<' . $time3)->group('`uid`')->findAll();
            $userlist = model('user')->where('is_del = 0 and is_audit=1 and `ctime`>=' . $time2 . ' and `ctime`<' . $time3)->findAll();
            $QfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid`=0 and `add_feedid`=0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time2 . ' and `publish_time`<' . $time3)->findAll();
            $AfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid` != 0 and `add_feedid` = 0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time2 . ' and `publish_time`<' . $time3)->findAll();
            $data[$index]['Login'][2] = count($loginlist);
            $data[$index]['Regist'][2] = count($userlist);
            $data[$index]['Question'][2] = count($QfeedList);
            $data[$index]['Answer'][2] = count($AfeedList);

            $loginlist = model('LoginLogs')->field('`uid`')->where('`ctime`>=' . $time3 . ' and `ctime`<' . $time4)->group('`uid`')->findAll();
            $userlist = model('user')->where('is_del = 0 and is_audit=1 and `ctime`>=' . $time3 . ' and `ctime`<' . $time4)->findAll();
            $QfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid`=0 and `add_feedid`=0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time3 . ' and `publish_time`<' . $time4)->findAll();
            $AfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid` != 0 and `add_feedid` = 0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time3 . ' and `publish_time`<' . $time4)->findAll();
            $data[$index]['Login'][3] = count($loginlist);
            $data[$index]['Regist'][3] = count($userlist);
            $data[$index]['Question'][3] = count($QfeedList);
            $data[$index]['Answer'][3] = count($AfeedList);

            $loginlist = model('LoginLogs')->field('`uid`')->where('`ctime`>=' . $time4 . ' and `ctime`<' . $time5)->group('`uid`')->findAll();
            $userlist = model('user')->where('is_del = 0 and is_audit=1 and `ctime`>=' . $time4 . ' and `ctime`<' . $time5)->findAll();
            $QfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid`=0 and `add_feedid`=0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time4 . ' and `publish_time`<' . $time5)->findAll();
            $AfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid` != 0 and `add_feedid` = 0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time4 . ' and `publish_time`<' . $time5)->findAll();
            $data[$index]['Login'][4] = count($loginlist);
            $data[$index]['Regist'][4] = count($userlist);
            $data[$index]['Question'][4] = count($QfeedList);
            $data[$index]['Answer'][4] = count($AfeedList);

            $loginlist = model('LoginLogs')->field('`uid`')->where('`ctime`>=' . $time5 . ' and `ctime`<' . $time6)->group('`uid`')->findAll();
            $userlist = model('user')->where('is_del = 0 and is_audit=1 and `ctime`>=' . $time5 . ' and `ctime`<' . $time6)->findAll();
            $QfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid`=0 and `add_feedid`=0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time5 . ' and `publish_time`<' . $time6)->findAll();
            $AfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid` != 0 and `add_feedid` = 0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time5 . ' and `publish_time`<' . $time6)->findAll();
            $data[$index]['Login'][5] = count($loginlist);
            $data[$index]['Regist'][5] = count($userlist);
            $data[$index]['Question'][5] = count($QfeedList);
            $data[$index]['Answer'][5] = count($AfeedList);

            $loginlist = model('LoginLogs')->field('`uid`')->where('`ctime`>=' . $time6 . ' and `ctime`<' . $time7)->group('`uid`')->findAll();
            $userlist = model('user')->where('is_del = 0 and is_audit=1 and `ctime`>=' . $time6 . ' and `ctime`<' . $time7)->findAll();
            $QfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid`=0 and `add_feedid`=0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time6 . ' and `publish_time`<' . $time7)->findAll();
            $AfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid` != 0 and `add_feedid` = 0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time6 . ' and `publish_time`<' . $time7)->findAll();
            $data[$index]['Login'][6] = count($loginlist);
            $data[$index]['Regist'][6] = count($userlist);
            $data[$index]['Question'][6] = count($QfeedList);
            $data[$index]['Answer'][6] = count($AfeedList);

            $loginlist = model('LoginLogs')->field('`uid`')->where('`ctime`>=' . $time7 . ' and `ctime`<' . $time8)->group('`uid`')->findAll();
            $userlist = model('user')->where('is_del = 0 and is_audit=1 and `ctime`>=' . $time7 . ' and `ctime`<' . $time8)->findAll();
            $QfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid`=0 and `add_feedid`=0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time7 . ' and `publish_time`<' . $time8)->findAll();
            $AfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid` != 0 and `add_feedid` = 0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time7 . ' and `publish_time`<' . $time8)->findAll();
            $data[$index]['Login'][7] = count($loginlist);
            $data[$index]['Regist'][7] = count($userlist);
            $data[$index]['Question'][7] = count($QfeedList);
            $data[$index]['Answer'][7] = count($AfeedList);

            $loginlist = model('LoginLogs')->field('`uid`')->where('`ctime`>=' . $time8 . ' and `ctime`<' . $time9)->group('`uid`')->findAll();
            $userlist = model('user')->where('is_del = 0 and is_audit=1 and `ctime`>=' . $time8 . ' and `ctime`<' . $time9)->findAll();
            $QfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid`=0 and `add_feedid`=0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time8 . ' and `publish_time`<' . $time9)->findAll();
            $AfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid` != 0 and `add_feedid` = 0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $time8 . ' and `publish_time`<' . $time9)->findAll();
            $data[$index]['Login'][8] = count($loginlist);
            $data[$index]['Regist'][8] = count($userlist);
            $data[$index]['Question'][8] = count($QfeedList);
            $data[$index]['Answer'][8] = count($AfeedList);

            $index++;
        }

        $this->assign('dtTitle', $dtArr);
        $this->assign('addUser', $AddUser);
        $this->assign('loginUser', $LoginUser);
        $this->assign('data2', $data);
        $this->assign('pv1', $pv1);
        $this->assign('pv2', $pv2);
        $this->assign('pv3', $pv3);
        $this->assign('pv4', $pv4);
        $this->assign('ip', $dip);

        $startInt = strtotime($startDt);
        $endInt = strtotime($endDt);
        //总问题数
        $QfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid`=0 and `add_feedid`=0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $startInt . ' and `publish_time`<' . $endInt)->findAll();
        $QAllCount = count($QfeedList);
        $this->assign('QAllCount', $QAllCount);
        //总回答数
        $AfeedList = model('Feed')->where('`is_del` = 0 and `feed_questionid` != 0 and `add_feedid` = 0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $startInt . ' and `publish_time`<' . $endInt)->findAll();
        $AAllCount = count($AfeedList);
        $this->assign('AAllCount', $AAllCount);
        //总评论数
        $CommentList = model('Comment')->where('`is_del` = 0 and `table` = \'feed\' and `ctime`>=' . $startInt . ' and `ctime`<' . $endInt)->findAll();
        $CommentCount = count($CommentList);
        $this->assign('CommentCount', $CommentCount);
        //总感谢数
        $ThankList = model('Feed')->where('`is_del` = 0 and `feed_questionid` != 0 and `add_feedid` = 0 and `thank_count`>0 and (`is_audit`=1 OR `is_audit`=0) and `publish_time`>=' . $startInt . ' and `publish_time`<' . $endInt)->findAll();
        $ThankCount = count($ThankList);
        $this->assign('ThankCount', $ThankCount);

        $this->display();
    }


    /**
     * 页面增加pv
     *
     * @return void
     *
     */
    public function logpv()
    {
        $pName = $_GET['pname'];
        if (!empty($pName)) {
            $where = '`name` = \'' . $pName . '\' and `ctime`>= UNIX_TIMESTAMP(curdate()) and `ctime`< UNIX_TIMESTAMP(date_add(curdate(), interval 1 day))';
            $result = D('')->table(C('DB_PREFIX') . 'log_pv')->where($where)->findAll();
            if (count($result) > 0)
                D('')->table(C('DB_PREFIX') . 'log_pv')->where($where)->setInc('count');
            else {
                $pvdata['name'] = $pName;
                $pvdata['count'] = 1;
                $pvdata['ctime'] = time();
                D('')->table(C('DB_PREFIX') . 'log_pv')->add($pvdata);
            }
        }

        if ($pName == 'all') {
            $ipStr = get_client_ip();
            $where = '`ip` = \'' . $ipStr . '\' and `ctime`>= UNIX_TIMESTAMP(curdate()) and `ctime`< UNIX_TIMESTAMP(date_add(curdate(), interval 1 day))';
            $result = D('')->table(C('DB_PREFIX') . 'log_ip')->where($where)->findAll();
            if (count($result) <= 0) {
                $ipdata['ip'] = $ipStr;
                $ipdata['ctime'] = time();
                D('')->table(C('DB_PREFIX') . 'log_ip')->add($ipdata);
            }
        }
    }

    /**
     * 课程列表页面
     */
    public function course()
    {
        //顶级专家
        $expertUid = C('TopExpert');
        $TopExpert = model('user')->getUserInfo($expertUid);
        $user_count = model('UserData')->getUserDataByUids(array($expertUid));
        $this->assign('TopExpert_UserCount', $user_count);
        $this->assign('TopExpert', $TopExpert);

        $map['course_state'] = 1;
        $courseList = model('course')->getCourseList($map);
        $this->assign('courselist', $courseList);

        $this->setTitle('卢勤课程');
        $this->setKeywords('卢勤课程');
        $this->display();
    }

    /**
     * 课程详情页面
     */
    public function coursedetail()
    {
        $courseid = $_GET['courseid'];
        if (!empty($courseid)) {

            $course = model('course')->getCourseByID($courseid);
            if (!empty($course)) {
                $this->assign('course', $course);
                $this->setTitle('卢勤课程－' . $course['course_title']);
                $this->setKeywords('卢勤课程－' . $course['course_title']);
            }
        }
        $this->display();
    }

    /**
     * 书籍页面
     */
    public function book()
    {
        //顶级专家
        $expertUid = C('TopExpert');
        $TopExpert = model('user')->getUserInfo($expertUid);
        $user_count = model('UserData')->getUserDataByUids(array($expertUid));
        $this->assign('TopExpert_UserCount', $user_count);
        $this->assign('TopExpert', $TopExpert);


        $this->setTitle('卢勤签名著作');
        $this->setKeywords('卢勤签名著作');
        $this->display();
    }


    /**
     * 报名数据
     *
     * @return $result varchar
     *
     */
    public function doActivityForm()
    {
        $map['childname'] = $_POST['childName'];
        $map['childage'] = $_POST['childAge'];
        $map['childsex'] = $_POST['childSex'];
        $map['childheight'] = $_POST['childHeight'];
        $map['childminzu'] = $_POST['childMinzu'];
        $map["childidcard"] = t($_POST['childIDcard']);
        $map['parentsname1'] = $_POST['parentName1'];
        $map['parentsex1'] = $_POST['parentSex1'];
        $map['parentheight1'] = $_POST['parentHeight1'];
        $map['parentminzu1'] = $_POST['parentMinzu1'];
        $map['parentsmobile1'] = $_POST['parentMobile1'];
        $map['parentsemail1'] = $_POST['parentEmail1'];
        $map["parentidcard1"] = t($_POST['parentIDcard1']);
        $map['parentsname2'] = $_POST['parentName2'];
        $map['parentsex2'] = $_POST['parentSex2'];
        $map['parentheight2'] = $_POST['parentHeight2'];
        $map['parentminzu2'] = $_POST['parentMinzu2'];
        $map['parentsmobile2'] = $_POST['parentMobile2'];
        $map['parentsemail2'] = $_POST['parentEmail2'];
        $map["parentidcard2"] = t($_POST['parentIDcard2']);
        $map['istogether'] = $_POST['istogether'];
        $map['remarks'] = $_POST['remarks'];
        $map['ctime'] = time();
        $map['activityname'] = $_POST['activityname'];
        $map["location"] = t($_POST['city_names']);

        $result = model('ActivityForm')->add($map);
        if ($result > 0) {
            //发送验证邮件
            //$this->sendActivityEmail($_POST['childName'], $_POST['parentEmail1']);
            //报名成功
            if ($_POST['webType'] == "mobile") {
                $this->redirect('public/Passport/pactshz_result');
            } else {
                $name = 'bj';
                $showname = '“放飞梦想我能行”北京夏令营';
                if ($map['activityname'] == '“放飞梦想我能行”北京夏令营') {
                    $name = 'bj';
                    $showname = '“放飞梦想我能行”北京夏令营';
                } else if ($map['activityname'] == 'qly') {
                    $name = 'ql';
                    $showname = '“知心姐姐”快乐生存秦岭自然体验营';
                } else if ($map['activityname'] == '“放飞梦想我能行”北京夏令营（新疆石河子专场）') {
                    $name = 'shz';
                    $showname = '“放飞梦想我能行”北京夏令营（新疆石河子专场）';
                }
                $_SESSION["JoinID"] = $result;
                $_SESSION["JoinInfo"] = '孩子姓名：' . $map['childname'] . '　家长姓名：' . $map['parentsname1'] . '　手机号：' . $map['parentsmobile1'];
                $_SESSION["name"] = $name;
                $_SESSION["activityname"] = $showname;
                $this->redirect('public/Passport/activity_shzresult');
            }
        }
    }

    public function pactbj2()
    {
        $from = empty($_GET['from']) ? 0 : 1;
        $this->assign('from', $from);
        $this->display();
    }
    public function pactbj2_step()
    {
        $from = empty($_GET['from']) ? 0 : 1;
        $this->assign('from', $from);
        $this->display();
    }

    public function doActivityFormBJ()
    {
        $map['childname'] = $_POST['childName'];
        $map['childage'] = $_POST['childAge'];
        $map['childsex'] = $_POST['childSex'];
        $map['childheight'] = $_POST['childHeight'];
        $map['childminzu'] = $_POST['childMinzu'];
        $map["childidcard"] = t($_POST['childIDcard']);
        $map['parentsname1'] = $_POST['parentName1'];
        $map['parentsex1'] = $_POST['parentSex1'];
        $map['parentheight1'] = $_POST['parentHeight1'];
        $map['parentminzu1'] = $_POST['parentMinzu1'];
        $map['parentsmobile1'] = $_POST['parentMobile1'];
        $map['parentsemail1'] = $_POST['parentEmail1'];
        $map["parentidcard1"] = t($_POST['parentIDcard1']);
        $map['parentsname2'] = $_POST['parentName2'];
        $map['parentsex2'] = $_POST['parentSex2'];
        $map['parentheight2'] = $_POST['parentHeight2'];
        $map['parentminzu2'] = $_POST['parentMinzu2'];
        $map['parentsmobile2'] = $_POST['parentMobile2'];
        $map['parentsemail2'] = $_POST['parentEmail2'];
        $map["parentidcard2"] = t($_POST['parentIDcard2']);
        $map['istogether'] = $_POST['istogether'];
        $map['remarks'] = $_POST['remarks'];
        $map['ctime'] = time();
        $map['activityname'] = $_POST['activityname'];
        $map["location"] = t($_POST['city_names']);
        $map['paytime'] = time();
        $map['paytotal'] = 6380.00;
        $total_fee = 638000;
        $enddate = strtotime('2015-05-30');
        if (time() <= $enddate) {
            $map['paytotal'] = 6000.00;
            $total_fee = 600000;
        }

        $result = model('ActivityForm')->add($map);
        if ($result > 0) {
            //$this->redirect('public/Passport/pactbj_result2');
            //报名成功,去支付
            $body = urlencode('2015北京精品营');
            $detail = urlencode('孩子姓名：' . $map['childname'] . '　家长姓名：' . $map['parentsname1'] . '　手机号：' . $map['parentsmobile1']);
            if (!empty($_POST['from']) && $_POST['from'] == 1)
                redirect('http://weixin.luqinwenda.com/payment/payment.aspx?body=' . $body . '&detail=' . $body . '&product_id=' . $result . '&total_fee=' . $total_fee);
            else
                $this->redirect('public/Passport/pactbj_result2');
                //redirect('http://yeepay.luqinwenda.com/weixin_payment.aspx?body=' . $body . '&detail=' . $body . '&product_id=' . $result . '&total_fee=' . $total_fee);
        }
    }

    public function doSingaporeCampForm()
    {
        $map['childname'] = $_POST['childName'];
        $map['childage'] = $_POST['childAge'];
        $map['childsex'] = $_POST['childSex'];
        $map['childheight'] = $_POST['childHeight'];
        $map['childminzu'] = $_POST['childMinzu'];
        $map["childidcard"] = t($_POST['childIDcard']);
        $map['parentsname1'] = $_POST['parentName1'];
        $map['parentsex1'] = $_POST['parentSex1'];
        $map['parentheight1'] = $_POST['parentHeight1'];
        $map['parentminzu1'] = $_POST['parentMinzu1'];
        $map['parentsmobile1'] = $_POST['parentMobile1'];
        $map['parentsemail1'] = $_POST['parentEmail1'];
        $map["parentidcard1"] = t($_POST['parentIDcard1']);
        $map['parentsname2'] = $_POST['parentName2'];
        $map['parentsex2'] = $_POST['parentSex2'];
        $map['parentheight2'] = $_POST['parentHeight2'];
        $map['parentminzu2'] = $_POST['parentMinzu2'];
        $map['parentsmobile2'] = $_POST['parentMobile2'];
        $map['parentsemail2'] = $_POST['parentEmail2'];
        $map["parentidcard2"] = t($_POST['parentIDcard2']);
        if ($_POST['istogether'] != null && $_POST['istogether'] == 'on')
            $map['istogether'] = 1;
        else
            $map['istogether'] = 0;
        $map['remarks'] = '参营人数：' . $_POST['remarks'];
        $map['ctime'] = time();
        $map['activityname'] = $_POST['activityname'];


        $result = model('ActivityForm')->add($map);
        if ($result > 0) {
            $this->redirect('public/Passport/SingaporeCamp_result');
        }
    }
    public function doSpeakerCampForm()
    {
        $map['childname'] = $_POST['childName'];
        $map['childage'] = $_POST['childAge'];
        $map['childsex'] = $_POST['childSex'];
        $map['childheight'] = $_POST['childHeight'];
        $map['childminzu'] = $_POST['childMinzu'];
        $map["childidcard"] = t($_POST['childIDcard']);
        $map['parentsname1'] = $_POST['parentName1'];
        $map['parentsex1'] = $_POST['parentSex1'];
        $map['parentheight1'] = $_POST['parentHeight1'];
        $map['parentminzu1'] = $_POST['parentMinzu1'];
        $map['parentsmobile1'] = $_POST['parentMobile1'];
        $map['parentsemail1'] = $_POST['parentEmail1'];
        $map["parentidcard1"] = t($_POST['parentIDcard1']);
        $map['parentsname2'] = $_POST['parentName2'];
        $map['parentsex2'] = $_POST['parentSex2'];
        $map['parentheight2'] = $_POST['parentHeight2'];
        $map['parentminzu2'] = $_POST['parentMinzu2'];
        $map['parentsmobile2'] = $_POST['parentMobile2'];
        $map['parentsemail2'] = $_POST['parentEmail2'];
        $map["parentidcard2"] = t($_POST['parentIDcard2']);

        $map['ctime'] = time();
        $map['activityname'] = $_POST['activityname'];


        $result = model('ActivityForm')->add($map);
        if ($result > 0) {
            $this->redirect('public/Passport/SpeakerCamp_result');
        }
    }

    public function sendActivityEmail($name, $email)
    {
        $data['node'] = '';
        $data['appname'] = '';
        $data['title'] = '“快乐人生万里行（五一长隆假日营）”报名确认函';
        $data['body'] = '<p>' . $name . '的家长：</p>	<p>　　您好，感谢您参与"快乐人生万里行（五一长隆假日营）"的报名。你的报名信息我们已经收到！随后相关的工作人员将与你取得联系，确认报名信息以及后续事宜，包括付款方式、机票事宜、集合地点等。如果你有什么问题想要咨询，也可以拨打以下两位工作人员的电话：邵老师（15699780807），梁老师（18311160841）。</p>
						<p>　　如果你想知道更多关于卢勤问答平台的资讯，请直接搜索微信号添加"卢勤问答平台"，或者扫描下方二维码。</p>
						<p>　　亲密良好的亲子互动，有利于孩子的成长。这样的机会，也许你的朋友也需要！如果你的周围也有想参加亲子营的朋友，希望你能把这个消息分享给他们。</p>
						<p>　　最后，感谢您对"快乐人生万里行（五一长隆假日营）"的大力支持！预祝您旅途愉快！</p>
						<p style="text-align:center;"><img src="http://www.luqinwenda.com/addons/theme/stv1/_static/image/wx2v.jpg" style="width:150px;" /></p>
						<p style="text-align:right;"> 卢勤问答平台敬上</p>';
        $data['uid'] = 0;
        $data['email'] = $email;

        model('Notify')->sendActivityEmail($data);
    }

    public function activityresult()
    {
        //$body = urlencode('放飞梦想我能行知心姐姐北京精品营再次出发');
        //echo 'http://weixin.luqinwenda.com/payment/payment.aspx?body=' . $body . '&detail=' . $body . '&product_id=999&total_fee=600000';


        $name = '“放飞梦想我能行”知心姐姐北京精品营再次出发';
        if ($_GET['name'] && $_GET['name'] != '')
            $name = $_GET['name'];
        $data = model('ActivityForm')->getList('', null, 'ctime desc', 20, $name);
        //print_r($data);
        $this->assign('data', $data);
        $this->display();
    }

    public function activity_shzresult()
    {
        $activityprice = '4880';
        $postUrl = '';
        if ($_SESSION["name"] == 'bj')
            $postUrl = U('public/Passport/bjActivity_pay');
        else if ($_SESSION["name"] == 'ql') {
            $activityprice = '5880';
            $postUrl = U('public/Passport/qlActivity_pay');
            $this->assign('activityDetail', '<div style="margin-top:0px; font-size:14px; font-weight:bold; color:Red; text-align:center;">6月20日之前报名优惠300元；老营员优惠200元；以上优惠可累计。</div><div style="margin-top:0px; text-align:center;">现参营收费：￥5880元/人</div>');
        } else if ($_SESSION["name"] == 'shz')
            $postUrl = U('public/Passport/shzActivity_pay');

        $this->assign('postUrl', $postUrl);
        $this->assign('activityname', $_SESSION["activityname"]);
        $this->assign('activityprice', $activityprice);
        $this->display();
    }

    public function yeepaytest()
    {
        //订单编号
        $order_id = 'xly_shz_' . time();
        //支付金额
        $order_pay = "0.01";
        //商品名称
        $order_productname = "“放飞梦想我能行”北京夏令营";
        $order_productname = iconv("UTF-8", "GB2312", $order_productname);
        //商品类型
        $order_producttype = "假日营";
        $order_producttype = iconv("UTF-8", "GB2312", $order_producttype);
        //商品详情
        $order_productdetail = "“放飞梦想我能行”北京夏令营（新疆石河子专场）";
        $order_productdetail = iconv("UTF-8", "GB2312", $order_productdetail);
        //支付成功返回地址
        $order_callback = "http://pay.luqinwenda.com/Callback.aspx";

        $this->yeepay($order_id, $order_pay, $order_productname, $order_producttype, $order_productdetail, $order_callback);
    }

    /**
     *  易宝支付
     *  //订单编号
     * $order_id="";
     * //支付金额
     * $order_pay="";
     * //商品名称
     * $order_productname="";
     * //商品类型
     * $order_producttype="";
     * //商品详情
     * $order_productdetail="";
     * //支付成功返回地址
     * $order_callback="";
     * //0:不需要,1:需要
     * $order_isdelivery="0";
     * //商家扩展信息
     * $order_shopextension="";
     * //支付通道编码,默认空
     * $order_paychannel="";
     */
    public function yeepay($order_id, $order_pay, $order_productname, $order_producttype, $order_productdetail, $order_callback, $order_isdelivery = 0, $order_shopextension = '', $order_paychannel = '')
    {
        $successUrl = "http://www.luqinwenda.com/index.php?app=public&mod=Passport&act=paysuccess";
        $failureUrl = "http://www.luqinwenda.com/index.php?app=public&mod=Passport&act=payfailure";
        $parameter = "p2_Order=$order_id&p3_Amt=$order_pay&p5_Pid=" . urlencode($order_productname) . "&p6_Pcat=" . urlencode($order_producttype) .
            "&p7_Pdesc=" . urlencode($order_productdetail) . "&p8_Url=" . urlencode($order_callback) . "&p9_SAF=$order_isdelivery&pa_MP=" . urlencode($order_shopextension) .
            "&pd_FrpId=$order_paychannel&successUrl=" . urlencode($successUrl) . "&failureUrl=" . urlencode($failureUrl);

        $url = "http://pay.luqinwenda.com/Req.aspx?" . $parameter;

        echo "<script language='javascript' type='text/javascript'>";
        echo "window.location.href='$url'";
        echo "</script>";
    }

    public function shzActivity_pay()
    {
        $count = $_POST['joinCount'];
        //订单编号
        $order_id = 'xly_shz_' . time();
        //支付金额
        $order_pay = 4880 * $count;

        //更新报名信息的订单号,支付金额,支付时间
        $updInfo['orderID'] = $order_id;
        $updInfo['paytotal'] = $order_pay;
        $updInfo['paytime'] = time();
        model('ActivityForm')->where('`activity_form_id`=' . $_SESSION["JoinID"])->save($updInfo);

        //商品名称
        $order_productname = "“放飞梦想我能行”北京夏令营（新疆石河子专场）";
        $order_productname = iconv("UTF-8", "GB2312", $order_productname);
        //商品类型
        $order_producttype = "假日营";
        $order_producttype = iconv("UTF-8", "GB2312", $order_producttype);
        //商品详情
        $order_productdetail = "“放飞梦想我能行”北京夏令营（新疆石河子专场）";
        $order_productdetail = iconv("UTF-8", "GB2312", $order_productdetail);
        //支付成功返回地址
        $order_callback = "http://pay.luqinwenda.com/Callback.aspx";
        //商品的扩展信息,写入报名人的信息
        $order_shopextension = $_SESSION["JoinInfo"] . "　缴费人数：" . $count;
        $order_shopextension = iconv("UTF-8", "GB2312", $order_shopextension);

        $this->yeepay($order_id, $order_pay, $order_productname, $order_producttype, $order_productdetail, $order_callback, 0, $order_shopextension);
    }

    public function bjActivity_pay()
    {
        $count = $_POST['joinCount'];
        //订单编号
        $order_id = 'xly_bj_' . time();
        //支付金额
        $order_pay = 4880 * $count;

        //更新报名信息的订单号,支付金额,支付时间
        $updInfo['orderID'] = $order_id;
        $updInfo['paytotal'] = $order_pay;
        $updInfo['paytime'] = time();
        model('ActivityForm')->where('`activity_form_id`=' . $_SESSION["JoinID"])->save($updInfo);

        //商品名称
        $order_productname = "“放飞梦想我能行”北京夏令营";
        $order_productname = iconv("UTF-8", "GB2312", $order_productname);
        //商品类型
        $order_producttype = "假日营";
        $order_producttype = iconv("UTF-8", "GB2312", $order_producttype);
        //商品详情
        $order_productdetail = "“放飞梦想我能行”北京夏令营";
        $order_productdetail = iconv("UTF-8", "GB2312", $order_productdetail);
        //支付成功返回地址
        $order_callback = "http://pay.luqinwenda.com/Callback.aspx";
        //商品的扩展信息,写入报名人的信息
        $order_shopextension = $_SESSION["JoinInfo"] . "　缴费人数：" . $count;
        $order_shopextension = iconv("UTF-8", "GB2312", $order_shopextension);

        $this->yeepay($order_id, $order_pay, $order_productname, $order_producttype, $order_productdetail, $order_callback, 0, $order_shopextension);
    }

    public function qlActivity_pay()
    {
        $count = $_POST['joinCount'];
        //订单编号
        $order_id = 'xly_ql_' . time();
        //支付金额
        $order_pay = 5880 * $count;

        //更新报名信息的订单号,支付金额,支付时间
        $updInfo['orderID'] = $order_id;
        $updInfo['paytotal'] = $order_pay;
        $updInfo['paytime'] = time();
        model('ActivityForm')->where('`activity_form_id`=' . $_SESSION["JoinID"])->save($updInfo);

        //商品名称
        $order_productname = "“知心姐姐”快乐生存秦岭自然体验营";
        $order_productname = iconv("UTF-8", "GB2312", $order_productname);
        //商品类型
        $order_producttype = "假日营";
        $order_producttype = iconv("UTF-8", "GB2312", $order_producttype);
        //商品详情
        $order_productdetail = "“知心姐姐”快乐生存秦岭自然体验营";
        $order_productdetail = iconv("UTF-8", "GB2312", $order_productdetail);
        //支付成功返回地址
        $order_callback = "http://pay.luqinwenda.com/Callback.aspx";
        //商品的扩展信息,写入报名人的信息
        $order_shopextension = $_SESSION["JoinInfo"] . "　缴费人数：" . $count;
        $order_shopextension = iconv("UTF-8", "GB2312", $order_shopextension);

        $this->yeepay($order_id, $order_pay, $order_productname, $order_producttype, $order_productdetail, $order_callback, 0, $order_shopextension);
    }

    //支付失败
    public function payfailure()
    {
        if ($_GET['order']) {
            $updInfo['ispay'] = 2;
            model('ActivityForm')->where("`orderID`='" . $_GET['order'] . "'")->save($updInfo);
        }
        $this->display();
    }

    //支付成功
    public function paysuccess()
    {
       if ($_GET['order']) {
            $updInfo['ispay'] = 1;
            $updInfo['paysuccesstime'] = time();
            model('ActivityForm')->where("`orderID`='" . $_GET['order'] . "'")->save($updInfo);
        }
        $this->display();
    }

    //支付成功
    public function paysuccess2()
    {
        if(!empty($_GET['product_id'])) {
            $map['orderID'] = '';
            $map['ispay'] = 1;
            $map['paysuccesstime'] = time();
            model('ActivityForm')->where("`activity_form_id`=" . $_GET['product_id'])->save($map);
        }
        $this->display();
    }


    public function wx_post()
    {
        if (!empty($_POST['submit'])) {
            $item = '';
            if (!empty($_POST['title1']) && !empty($_POST['description1']) && !empty($_POST['picurl1']) && !empty($_POST['url1'])) {
                $item .= '{title:"' . $_POST['title1'] . '",description:"' . $_POST['description1'] . '",url:"' . $_POST['url1'] . '",picurl:"' . $_POST['picurl1'] . '"}';
            }
            if (!empty($_POST['title2']) && !empty($_POST['description2']) && !empty($_POST['url2'])) {
                $item .= ',{title:"' . $_POST['title2'] . '",description:"' . $_POST['description2'] . '",url:"' . $_POST['url2'] . '",picurl:"' . $_POST['picurl2'] . '"}';
            }
            if (!empty($_POST['title3']) && !empty($_POST['description3']) && !empty($_POST['url3'])) {
                $item .= ',{title:"' . $_POST['title3'] . '",description:"' . $_POST['description3'] . '",url:"' . $_POST['url3'] . '",picurl:"' . $_POST['picurl3'] . '"}';
            }
            if (!empty($_POST['title4']) && !empty($_POST['description4']) && !empty($_POST['url4'])) {
                $item .= ',{title:"' . $_POST['title4'] . '",description:"' . $_POST['description4'] . '",url:"' . $_POST['url4'] . '",picurl:"' . $_POST['picurl4'] . '"}';
            }
            if (!empty($_POST['title5']) && !empty($_POST['description5']) && !empty($_POST['url5'])) {
                $item .= ',{title:"' . $_POST['title5'] . '",description:"' . $_POST['description5'] . '",url:"' . $_POST['url5'] . '",picurl:"' . $_POST['picurl5'] . '"}';
            }
            if (!empty($_POST['title6']) && !empty($_POST['description6']) && !empty($_POST['url6'])) {
                $item .= ',{title:"' . $_POST['title6'] . '",description:"' . $_POST['description6'] . '",url:"' . $_POST['url6'] . '",picurl:"' . $_POST['picurl6'] . '"}';
            }
            if (!empty($_POST['title7']) && !empty($_POST['description7']) && !empty($_POST['url7'])) {
                $item .= ',{title:"' . $_POST['title7'] . '",description:"' . $_POST['description7'] . '",url:"' . $_POST['url7'] . '",picurl:"' . $_POST['picurl7'] . '"}';
            }
            if (!empty($_POST['title8']) && !empty($_POST['description8']) && !empty($_POST['url8'])) {
                $item .= ',{title:"' . $_POST['title8'] . '",description:"' . $_POST['description8'] . '",url:"' . $_POST['url8'] . '",picurl:"' . $_POST['picurl8'] . '"}';
            }

            if ($_POST['isTest'] == '0') {
                $url = 'http://weixin.luqinwenda.com/get_active_user.aspx';
                $Result = curl_post_lqwd($url, '');
                $jsonArr = analyJson_lqwd($Result);
                for ($i = 0; $i < count($jsonArr['openid']); $i++) {
                    //echo $jsonArr['openid'][$i];
                    //echo '<br />';
                    if (!empty($jsonArr['openid'][$i]))
                        $this->postInfo($jsonArr['openid'][$i], $item);
                }
            } else {

                $openid = 'oqrMvt_AYtvhUxaJ-4ijUjk62NwI';
                $this->postInfo($openid, $item);

                $openid = 'oqrMvt6yRAWFu3DmhGe4Td0nKZRo';
                $this->postInfo($openid, $item);

                $openid = 'oqrMvt8K6cwKt5T1yAavEylbJaRs';
                $this->postInfo($openid, $item);

            }
        }

        $this->display();
    }

    public function postInfo($openid, $item)
    {
        sleep(1);
        $postUrl = 'http://weixin.luqinwenda.com/send_message.aspx';
        $param = '{fromuser:"gh_7c0c5cc0906a",touser:"' . $openid . '",msgtype:"news",news:{articles:[' . $item . ']}}';
        $result = curl_post_lqwd($postUrl, $param);
    }


}

?>