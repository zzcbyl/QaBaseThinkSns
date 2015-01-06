<?php

/**
 * 登录验证页面
 * @author liuxiaoqing <liuxiaoqing@zh
 * @version TS3.0
 */
class LandingPageAction
{
    public function Landing()
    {
        //$this->redirect('public/MobileNew/prompt'); //暂停页面

        $openid = '';
        $url = '';
        $from = $_GET['from'];
        /*if ($from == "1") //服务号
        {
            sleep(1);
            $idurl = 'http://weixin.luqinwenda.com/menu_click_landing.aspx?openid=' . $openid;
            $Result = $this->curls($idurl);
            //$Result = '{"status":0,"openid":"oqrMvt6yRAWFu3DmhGe4Td0nKZRo" }';
            $jsonArr = $this->analyJson($Result);
            $openid = $jsonArr['openid'];
            if (empty($openid) || $openid == '' || $openid == null) {
                echo '服务器繁忙，请稍后再试...';
                return;
            }
            //$expire = 3600 * 24 * 30 * 36;
            //cookie('lqwd_openid', $openid, $expire);

            //$openid = $_GET['openid'];
            $url = $_GET['url'];
            $source = $_GET['source'];
        } else {*/

        $openid = $_GET['openid'];
        $dt = $_GET['time'];
        $url = $_GET['url'];
        $code = $_GET['code'];
        //$source = $_GET['source'];

        if (empty($openid) || empty($dt) || empty($code)) {
            echo '非法访问';
            return;
        }
        //判断时间
        /*$date = date("Y-m-d H:i:s", strtotime("-30 minute"));
        //echo time().'<br>';
        if ($dt > time() || $dt < strtotime($date)) {
            echo '访问超时';
            return;
        }*/
        //判断code
        $key = C('WXURL_KEY');
        //echo md5($openid.$dt.$key).'<br>';
        if (md5($openid . $dt . $key) != $code) {
            echo '非法访问';
            return;
        }
        //}

        //判断openid存在去登录,否则去注册
        $user = model('User')->getUserInfoByOpenID($openid);

        if (!empty($user) && $user['uid'] > 0) {
            $result = model('Passport')->loginLocalWhitoutPassword($user['login']);
        } else {
            $this->saveUser($openid, $from);
        }

        if (empty($url)) {
            $this->redirect('public/MobileNew/all', array('openid' => $openid));
        } else {
            switch ($url) {
                case '1':
                    $this->redirect('public/MobileNew/all', array('openid' => $openid));
                    break;
                case '2':
                    $this->redirect('public/MobileNew/answer_lulaoshi', array('openid' => $openid));
                    break;
                case '3':
                    $this->redirect('public/MobileNew/quickask', array('openid' => $openid));
                    break;
                case '4':
                    $this->redirect('public/MobileNew/myquestion', array('openid' => $openid));
                    break;
                default:
                    $this->redirect('public/MobileNew/all', array('openid' => $openid));
            }
        }

        /*else
        {
            //$this->redirect('public/Register/Homemobile', array('openid'=>$openid,'source'=>$source));
            //$openid = 'oqrMvtySBUCd-r6-ZIivSwsmzr44';

            $from = $_GET['from'];
            if($from != null && $from != '' && $from == '1')
            {
                $this->redirect('public/Register/Homemobile', array('openid'=>$openid,'source'=>$url));
            }
            else
            {

                //自动注册
                $url = 'http://weixin.luqinwenda.com/getuserinfo.aspx?openid='.$openid;
                $UserResult = $this->curls($url);
                $jsonUserArr = $this->analyJson($UserResult);
                //print_r(model('user')->getUserInfo(2062));
                //return;

                $uname = $this->getUname($jsonUserArr['nickname']);

                $account = t($openid);
                $user["login"] = t($openid);
                $user["password"] = t($openid);
                $user["uname"] = t($uname);
                $user["sex"] = intval($jsonUserArr['sex']);
                $user["is_active"] = 1;
                $user["is_audit"] = 1;
                $user["is_init"] = 1;
                $user["linknumber"] = '';
                $user["email"] = t($openid);
                $user["realname"] = '';
                $user["idcard"] = '';
                $user["openid"] = t($openid);
                $user["source"] = '';
                $user["birthday"] = '';
                $user["location"] = $jsonUserArr['country'].' '.$jsonUserArr['province'].' '.$jsonUserArr['city'];
                $user["province"] = 0;
                $user["city"] = 0;
                $user['area'] = 0;
                $user["is_del"] = 0;
                $user["intro"] = '';
                $user["domain"] = '';
                $uid = model('User')->addUserMobile($user);
                //print(model('User')->getLastSql());
                if($uid)
                {
                    // 添加积分
                    model('Credit')->setUserCredit($uid,'init_default');

                    // 添加至默认的用户组
                    $userGroup = model('Xdata')->get('admin_Config:register');
                    $userGroup = empty($userGroup['default_user_group']) ? C('DEFAULT_GROUP_ID') : $userGroup['default_user_group'];
                    model('UserGroupLink')->domoveUsergroup($uid, implode(',', $userGroup));

                    if (isset($_SESSION['third-party-type']))  {
                        $user_message = $_SESSION["third-party-user-info"];
                        $avatar = new AvatarModel($uid);
                        $avatar->saveRemoteAvatar($user_message['userface'],$uid);
                    }

                    //头像
                    if($jsonUserArr!=null&&$jsonUserArr['headimgurl']!=null&&$jsonUserArr['headimgurl']!='')
                    {
                        model('Avatar')->saveRemoteAvatar($jsonUserArr['headimgurl'], $uid);
                    }
                    else
                    {
                        model('Avatar')->saveRemoteAvatar("http://www.luqinwenda.com/addons/theme/stv1/_static/image/noavatar/big.jpg", $uid);
                    }

                    model('Register')->overUserInit($uid);
                    model('User')->cleanCache ( array($uid) );

                    //登录
                    model('Passport')->loginLocalWhitoutPassword($account);
                    unset($_SESSION['YMZCODE']);
                    unset($_SESSION['sendDT']);

                    $this->redirect('public/Mobile/all');

                }
            }
        }*/
    }

    private function saveUser($openid, $from)
    {
        $result = false;
        if (!empty($openid)) {
            //自动注册
            if ($from == 1) //服务号
                $url = 'http://weixin.luqinwenda.com/getuserinfo.aspx?openid=' . $openid;
            else
                $url = 'http://weixin.luqinwenda.com/dingyue/getuserinfo.aspx?openid=' . $openid;
            $UserResult = $this->curls($url);
            $jsonUserArr = $this->analyJson($UserResult);
            if (intval($jsonUserArr['errcode']) > 0) {
                return $result;
            }
            //print_r(model('user')->getUserInfo(2062));
            //return;

            $uname = $this->getUname($jsonUserArr['nickname']);

            $account = t($openid);
            $user["login"] = t($openid);
            $user["password"] = t($openid);
            $user["uname"] = t($uname);
            $user["sex"] = intval($jsonUserArr['sex']);
            $user["is_active"] = 1;
            $user["is_audit"] = 1;
            $user["is_init"] = 1;
            $user["linknumber"] = '';
            $user["email"] = t($openid);
            $user["realname"] = '';
            $user["idcard"] = '';
            $user["openid"] = t($openid);
            if ($from == 1)
                $user["source"] = '4';
            else
                $user["source"] = '5';
            $user["birthday"] = '';
            $user["location"] = $jsonUserArr['country'] . ' ' . $jsonUserArr['province'] . ' ' . $jsonUserArr['city'];
            $user["province"] = 0;
            $user["city"] = 0;
            $user['area'] = 0;
            $user["is_del"] = 0;
            $user["intro"] = '';
            $user["domain"] = '';
            $uid = model('User')->addUserMobile($user);
            //print(model('User')->getLastSql());
            if ($uid) {
                // 添加积分
                model('Credit')->setUserCredit($uid, 'init_default');

                // 添加至默认的用户组
                $userGroup = model('Xdata')->get('admin_Config:register');
                $userGroup = empty($userGroup['default_user_group']) ? C('DEFAULT_GROUP_ID') : $userGroup['default_user_group'];
                model('UserGroupLink')->domoveUsergroup($uid, implode(',', $userGroup));

                //头像
                if ($jsonUserArr != null && $jsonUserArr['headimgurl'] != null && $jsonUserArr['headimgurl'] != '') {
                    model('Avatar')->saveRemoteAvatar($jsonUserArr['headimgurl'], $uid);
                } else {
                    model('Avatar')->saveRemoteAvatar("http://www.luqinwenda.com/addons/theme/stv1/_static/image/noavatar/big.jpg", $uid);
                }

                model('Register')->overUserInit($uid);
                model('User')->cleanCache(array($uid));

                model('Passport')->loginLocalWhitoutPassword($user['login']);
                //$result = model('Passport')->loginLocalWhitoutPassword($user['login']);
                $result = true;
            }
        }
        return $result;
    }

    private function getUname($uname)
    {
        $isUse = model('Register')->isValidName($uname);
        if (!$isUse) {
            $uname = $uname . rand(1000, 999999);
            $this->getUname($uname);
        }
        return $uname;
    }

    /**
     * Action跳转(URL重定向） 支持指定模块和延时跳转
     * @access protected
     * @param string $url 跳转的URL表达式
     * @param array $params 其它URL参数
     * @param integer $delay 延时跳转的时间 单位为秒
     * @param string $msg 跳转提示信息
     * @return void
     */
    protected function redirect($url, $params = array(), $delay = 0, $msg = '')
    {
        if (C('LOG_RECORD')) Log::save();
        $url = U($url, $params);
        redirect($url, $delay, $msg);
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
    function analyJson($json_str)
    {
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
}