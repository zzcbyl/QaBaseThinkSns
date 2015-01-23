<?php

/**
 * 对外接口
 * @author zhangzc
 * @version TS3.0
 */
class ApiAction
{
    private $limitnums = 10;

    /**
     * 全站问题
     *
     * @return JSON
     *
     */
    public function getAllFeed()
    {
        $psize = $_GET['psize'];
        if (!empty($psize)) {
            $this->limitnums = $psize;
        }

        $where = " (is_audit=1 OR is_audit=0) AND is_del = 0 AND feed_questionid=0 AND add_feedid=0";

        $list = model('Feed')->getQuestionAndAnswer($where, $this->limitnums);

        echo json_encode($list);
    }

    /**
     * 获取我的问题
     *
     * @param uid  用户ID
     * @return JSON
     *
     */
    public function getMyQuestion()
    {
        $current_uid = intval($_GET['uid']);
        if ($current_uid <= 0) {
            echo '{"error":"uid unavailable"}';
            return;
        }

        $psize = $_GET['psize'];
        if (!empty($psize)) {
            $this->limitnums = $psize;
        }

        $where = ' uid=' . $current_uid . ' AND is_del = 0 AND feed_questionid=0 AND add_feedid=0 AND (is_audit=1 OR is_audit=0) ';
        $list = model('Feed')->getQuestionAndAnswer($where, $this->limitnums);
        echo json_encode($list);
    }

    /**
     * 获取我的回答
     *
     * @param uid 用户ID
     * @return JSON
     *
     */
    public function getMyAnswer()
    {
        $current_uid = intval($_GET['uid']);
        if ($current_uid <= 0) {
            echo '{"error":"uid unavailable"}';
            return;
        }

        $psize = $_GET['psize'];
        if (!empty($psize)) {
            $this->limitnums = $psize;
        }

        $where = ' uid=' . $current_uid . ' AND is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND (is_audit=1 OR is_audit=0) ' . $LoadWhere;
        $list = model('Feed')->getAnswerList($where, $this->limitnums);
        echo json_encode($list);
    }

    /**
     * 邀请我的
     *
     * @param uid 用户ID
     * @return JSON
     *
     */
    public function getInviteMe()
    {
        $current_uid = intval($_GET['uid']);
        if ($current_uid <= 0) {
            echo '{"error":"uid unavailable"}';
            return;
        }

        $psize = $_GET['psize'];
        if (!empty($psize)) {
            $this->limitnums = $psize;
        }
        $list = model('Feed')->getInviteList($current_uid, $this->limitnums);
        echo json_encode($list);
    }

    /**
     * 获取我的关注
     *
     * @param uid 用户ID
     * @return JSON
     *
     */
    public function getMyFollowing()
    {
        $current_uid = intval($_GET['uid']);
        if ($current_uid <= 0) {
            echo '{"error":"uid unavailable"}';
            return;
        }

        $following_list = model('Follow')->getFollowingList($current_uid);
        $fids = getSubByKey($following_list ['data'], 'fid');
        if ($fids) {
            $uids = array_merge($fids, array(
                $this->uid
            ));
        } else {
            $uids = array(
                $this->uid
            );
        }
        // 获取用户组信息
        $follow_state = model('Follow')->getFollowStateByFids($current_uid, $fids);
        $ArrayData['follow_state'] = $follow_state;

        !is_array($uids) && $uids = explode(',', $uids);
        $user_info = model('User')->getUserInfoByUids($uids);
        $ArrayData['user_info'] = $user_info;


        $user_count = model('UserData')->getUserDataByUids($uids);

        $ArrayData['user_count'] = $user_count;


        echo json_encode($ArrayData);

    }

    /**
     * 获取我的粉丝
     *
     * @param uid 用户ID
     * @return JSON
     *
     */
    public function getMyFollower()
    {
        $current_uid = intval($_GET['uid']);
        if ($current_uid <= 0) {
            echo '{"error":"uid unavailable"}';
            return;
        }

        $following_list = model('Follow')->getFollowerList($current_uid);
        $fids = getSubByKey($following_list ['data'], 'fid');
        if ($fids) {
            $uids = array_merge($fids, array(
                $this->uid
            ));
        } else {
            $uids = array(
                $this->uid
            );
        }
        // 获取用户组信息
        $follow_state = model('Follow')->getFollowStateByFids($current_uid, $fids);
        $ArrayData['follow_state'] = $follow_state;

        !is_array($uids) && $uids = explode(',', $uids);
        $user_info = model('User')->getUserInfoByUids($uids);
        $ArrayData['user_info'] = $user_info;


        $user_count = model('UserData')->getUserDataByUids($uids);

        $ArrayData['user_count'] = $user_count;


        echo json_encode($ArrayData);

    }

    /**
     * 获取我的好友
     *
     * @param uid 用户ID
     * @return JSON
     *
     */
    public function getMyFriend()
    {
        $current_uid = intval($_GET['uid']);
        if ($current_uid <= 0) {
            echo '{"error":"uid unavailable"}';
            return;
        }


        $following_list = model('Follow')->getFriendList($current_uid);
        $fids = getSubByKey($following_list ['data'], 'fid');
        if ($fids) {
            $uids = array_merge($fids, array(
                $this->uid
            ));
        } else {
            $uids = array(
                $this->uid
            );
        }
        // 获取用户组信息
        $follow_state = model('Follow')->getFollowStateByFids($current_uid, $fids);
        $ArrayData['follow_state'] = $follow_state;

        !is_array($uids) && $uids = explode(',', $uids);
        $user_info = model('User')->getUserInfoByUids($uids);
        $ArrayData['user_info'] = $user_info;


        $user_count = model('UserData')->getUserDataByUids($uids);

        $ArrayData['user_count'] = $user_count;


        echo json_encode($ArrayData);

    }

    /**
     * 通过ID获取用户
     *
     * @param uid 用户ID
     * @return JSON
     *
     */
    public function getUserID()
    {
        $current_uid = intval($_GET['uid']);
        if ($current_uid <= 0) {
            echo '{"error":"uid unavailable"}';
            return;
        }

        $user = model('User')->getUserInfo($current_uid);

        echo json_encode($user);

    }

    /**
     * 通过OpenID获取用户
     *
     * @param uid 用户ID
     * @return JSON
     *
     */
    public function getUserOpenID()
    {
        $OpenID = $_GET['openid'];
        if ($OpenID <= 0) {
            echo '{"error":"openid unavailable"}';
            return;
        }

        $user = model('User')->getUserInfoByOpenID($OpenID);

        echo json_encode($user);
    }

    /**
     * 热门问题(5条)
     *
     * @return JSON
     *
     */
    public function getHotQuestion()
    {
        $where = ' (`is_audit`=1 OR `is_audit`=0) AND `is_del` = 0 AND `feed_questionid`=0 AND `add_feedid` = 0  ';
        $list = model('Feed')->getList($where, 5, 'answer_count desc, publish_time desc');

        $returnData = Array();
        foreach ($list['data'] as $k => $v) {
            $data = Array();
            $data['feed_id'] = $v['feed_id'];
            $data['body'] = $v['body'];
            $data['description'] = $v['description'];
            $returnData[$k] = $data;
        }

        echo '{"data":' . $this->JSON($returnData) . '}';
    }

    function JSON($array)
    {

        $this->arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }

    function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }

            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }


    /**
     * 最新问题(5条)
     *
     * @return JSON
     *
     */
    public function getNewQuestion()
    {
        $where = ' (`is_audit`=1 OR `is_audit`=0) AND `is_del` = 0 AND `feed_questionid`=0 AND `add_feedid` = 0  ';
        $NewQuestion = model('Feed')->getQuestionList($where, 5);
        $returnData = Array();
        foreach ($NewQuestion['data'] as $k => $v) {
            $data = Array();
            $data['feed_id'] = $v['feed_id'];
            $data['body'] = $v['body'];
            $data['description'] = $v['description'];
            $returnData[$k] = $data;
        }
        echo '{"data":' . $this->JSON($returnData) . '}';
    }

    public function isPass($str, $dt, $code)
    {

        $date = date("Y-m-d H:i:s", strtotime("-30 minute"));
        if ($dt > time() || $dt < strtotime($date)) {
            return false;
        }

        $key = C('WXURL_KEY');
        if (md5($str . $dt . $key) != $code) {
            return false;
        }

        return true;
    }

    /**
     * 登录
     *
     * @return JSON
     *
     */
    public function userlogin()
    {
        $login = $_GET['login'];
        $pwd = $_GET['pwd'];
        $dt = $_GET['time'];
        $code = $_GET['code'];

        if (!$this->isPass($login . $pwd, $dt, $code)) {
            echo '非法访问';
            return;
        }

        $result = model('Passport')->getLocalUser($login, $pwd);

        if (!$result) {
            $status = 0;
            $info = model('Passport')->getError();
            $data = 0;
        } else {
            $status = 1;
            $info = model('Passport')->getSuccess();
            $data = $result;
        }

        $info = str_replace('"', '\"', $info);
        echo '{"status":' . $status . ',"info":"' . $info . '","data":' . $this->JSON($data) . '}';
    }

    private $_email_reg = '/[_a-zA-Z\d\-\.]+(@[_a-zA-Z\d\-\.]+\.[_a-zA-Z\d\-]+)+$/i';        // 邮箱正则规则
    private $_mobile_reg = '/^0*(13|15|18)\d{9}$/i';        // 手机号正则规则

    /**
     * 用户注册
     */
    public function userregist()
    {
        $login = $_GET['login'];
        $pwd = $_GET['pwd'];
        $uname = $_GET['uname'];
        $sex = $_GET['sex'];
        $dt = $_GET['time'];
        $code = $_GET['code'];

        if (!$this->isPass($login . $pwd, $dt, $code)) {
            echo '非法访问';
            return;
        }

        $status = 0;
        $returnInfo = '';
        $data = '';

        $account = t($login);

        if (!model("Register")->isValidAccount($account)) {
            echo '{"status":0,"info":"' . model("Register")->getLastError() . '","data":""}';
            return;
        }

        $res = preg_match($this->_email_reg, $account, $matches) !== 0;
        $res_mobile = preg_match($this->_mobile_reg, $account, $matches) !== 0;
        //邮箱
        $user["login"] = $account;
        if ($res) {
            $user["email"] = $account;
        }
        //手机号
        if ($res_mobile) {
            $user["linknumber"] = $account;
        }
        $user["uname"] = t($uname);
        $user["password"] = t($pwd);
        $user["sex"] = intval($sex);
        $user["is_active"] = 1;
        $user["is_audit"] = 1;
        $user["realname"] = '';
        $user["idcard"] = '';
        $user["birthday"] = '';
        $user["province"] = 0;
        $user["city"] = 0;
        $user['area'] = 0;
        $user["location"] = '';
        $user["intro"] = '';

        $uid = model('user')->addUser($user);
        //print(model('user')->getLastSql());
        //return;
        if ($uid) {
            // 添加积分
            model('Credit')->setUserCredit($uid, 'init_default');

            // 添加至默认的用户组
            $userGroup = model('Xdata')->get('admin_Config:register');
            $userGroup = empty($userGroup['default_user_group']) ? C('DEFAULT_GROUP_ID') : $userGroup['default_user_group'];
            model('UserGroupLink')->domoveUsergroup($uid, implode(',', $userGroup));

            $result = model('Passport')->getLocalUser($login, $pwd);

            if (!$result) {
                $returnInfo = model('Passport')->getError();
                $data = '';
            } else {
                $status = 1;
                $returnInfo = model('Passport')->getSuccess();
                $data = $result;
            }
        } else {
            $returnInfo = model('user')->getError();
        }

        echo '{"status":' . $status . ',"info":"' . $returnInfo . '","data":' . $this->JSON($data) . '}';
    }

    /**
     * 获取feed信息
     */
    public function getfeedbyid()
    {
        $feedid = $_GET['feedid'];
        $dt = $_GET['time'];
        $code = $_GET['code'];

        /*if (!$this->isPass($feedid, $dt, $code)) {
            echo '非法访问';
            return;
        }*/

        $status = 0;
        $returnInfo = '';
        $data = '';

        $result = model('feed')->get($feedid);
        if ($result) {
            $status = 1;
            $returnInfo = '';
            $data = $result;
        } else {
            $returnInfo = '问答不存在';
        }

        echo '{"status":' . $status . ',"info":"' . $returnInfo . '","data":[' . json_encode($data) . ']}';
    }

    public function getInterviewFeed()
    {
        $psize = 10;
        if (intval($_GET['p']) > 0) {
            $InterView = model('Interview')->getInterView('', 1);
            if (!empty($InterView)) {
                $data = $InterView[0];
                $startdt = strtotime($data['iv_startdt']);
                $enddt = strtotime($data['iv_enddt']);
                $InterView[0]['iv_state'] = model('Interview')->getStateInt($startdt,$enddt);
                $expert = C('TopExpert');
                $limitnums = (intval($_GET['p']) - 1) * $psize;
                $where = " is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND is_audit=1 and interview_audit=1 and last_updtime >='"
                    . $startdt . "' AND last_updtime < '" . $enddt . "' and `uid` in (" . $expert . ")";
                $list = model('Feed')->getAnswerListbyInterview($where, $limitnums . ',' . $psize);
                //echo model('Feed')->getLastSql();
                $count = model('Feed')->where($where)->count();
                $page = new Page($count, $psize);
                echo '{"status":1,"info":"","interview":'.json_encode($InterView[0]).',"page":' . json_encode($page) . ',"data":' . json_encode($list['data']) . '}';
                return;
            }
        }
        echo '{"status":0,"info":"暂无见面会内容","data":}';
    }
}