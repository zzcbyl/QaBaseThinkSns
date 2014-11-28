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
		if(!empty($psize))
		{
			$this->limitnums = $psize;
		}
		
		$where =" (is_audit=1 OR is_audit=0) AND is_del = 0 AND feed_questionid=0 AND add_feedid=0";
		
		$list = model('Feed')->getQuestionAndAnswer($where,$this->limitnums);
		
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
		if($current_uid<=0)
		{
			echo '{"error":"uid unavailable"}';
			return;
		}
		
		$psize = $_GET['psize'];
		if(!empty($psize))
		{
			$this->limitnums = $psize;
		}
		
		$where =' uid='.$current_uid.' AND is_del = 0 AND feed_questionid=0 AND add_feedid=0 AND (is_audit=1 OR is_audit=0) ';
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
		if($current_uid<=0)
		{
			echo '{"error":"uid unavailable"}';
			return;
		}
		
		$psize = $_GET['psize'];
		if(!empty($psize))
		{
			$this->limitnums = $psize;
		}
		
		$where =' uid='.$current_uid.' AND is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND (is_audit=1 OR is_audit=0) '.$LoadWhere;
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
		if($current_uid<=0)
		{
			echo '{"error":"uid unavailable"}';
			return;
		}
		
		$psize = $_GET['psize'];
		if(!empty($psize))
		{
			$this->limitnums = $psize;
		}
		$list =  model('Feed')->getInviteList($current_uid, $this->limitnums);
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
		if($current_uid<=0)
		{
			echo '{"error":"uid unavailable"}';
			return;
		}
		
		$following_list = model ( 'Follow' )->getFollowingList ( $current_uid);
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
		$follow_state = model ( 'Follow' )->getFollowStateByFids ( $current_uid, $fids );
		$ArrayData['follow_state']=$follow_state;
		
		! is_array ( $uids ) && $uids = explode ( ',', $uids );
		$user_info = model ( 'User' )->getUserInfoByUids ( $uids );
		$ArrayData['user_info']=$user_info;
		
		
		$user_count = model ( 'UserData' )->getUserDataByUids ( $uids );
		
		$ArrayData['user_count']=$user_count;
		
		
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
		if($current_uid<=0)
		{
			echo '{"error":"uid unavailable"}';
			return;
		}
		
		$following_list = model ( 'Follow' )->getFollowerList ( $current_uid);
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
		$follow_state = model ( 'Follow' )->getFollowStateByFids ( $current_uid, $fids );
		$ArrayData['follow_state']=$follow_state;
		
		! is_array ( $uids ) && $uids = explode ( ',', $uids );
		$user_info = model ( 'User' )->getUserInfoByUids ( $uids );
		$ArrayData['user_info']=$user_info;
		
		
		$user_count = model ( 'UserData' )->getUserDataByUids ( $uids );
		
		$ArrayData['user_count']=$user_count;
		
		
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
		if($current_uid<=0)
		{
			echo '{"error":"uid unavailable"}';
			return;
		}
		
		
		$following_list = model ( 'Follow' )->getFriendList ( $current_uid);
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
		$follow_state = model ( 'Follow' )->getFollowStateByFids ( $current_uid, $fids );
		$ArrayData['follow_state']=$follow_state;
		
		! is_array ( $uids ) && $uids = explode ( ',', $uids );
		$user_info = model ( 'User' )->getUserInfoByUids ( $uids );
		$ArrayData['user_info']=$user_info;
		
		
		$user_count = model ( 'UserData' )->getUserDataByUids ( $uids );
		
		$ArrayData['user_count']=$user_count;
		
		
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
		if($current_uid<=0)
		{
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
		if($OpenID<=0)
		{
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
		$where =' (`is_audit`=1 OR `is_audit`=0) AND `is_del` = 0 AND `feed_questionid`=0 AND `add_feedid` = 0  ';
		$list = model('Feed')->getList($where,5,'answer_count desc, publish_time desc');
		
		$returnData = Array();
		foreach($list['data'] as $k=>$v)
		{
			$data = Array();
			$data['feed_id'] = $v['feed_id'];
			$data['body'] = $v['body'];
			$data['description'] = $v['description'];
			$returnData[$k] = $data;
		}
		
		echo '{"data":'.$this->JSON($returnData).'}';
	}
	
	function JSON($array) {

		$this->	arrayRecursive($array, 'urlencode', true);
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
		$where =' (`is_audit`=1 OR `is_audit`=0) AND `is_del` = 0 AND `feed_questionid`=0 AND `add_feedid` = 0  ';
		$NewQuestion = model('Feed')->getQuestionList($where, 5);
		$returnData = Array();
		foreach($NewQuestion['data'] as $k=>$v)
		{
			$data = Array();
			$data['feed_id'] = $v['feed_id'];
			$data['body'] = $v['body'];
			$data['description'] = $v['description'];
			$returnData[$k] = $data;
		}
		echo '{"data":'.$this->JSON($returnData).'}';
	}
	
	public function isPass($str, $dt, $code)
	{
		$date = date("Y-m-d H:i:s",strtotime("-30 minute"));
		if($dt > time() || $dt < strtotime($date))
		{
			return false;
		}
		
		$key = C('WXURL_KEY');
		if(md5($str.$dt.$key) != $code)
		{
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
		
		/*if(!$this->isPass($login, $dt, $code))
		{
			echo '非法访问';
			return;
		}*/

		$result = model('Passport')->getLocalUser($login,$pwd);
		
		if(!$result){
			$status = 0; 
			$info	= model('Passport')->getError();
			$data 	= 0;
		}else{
			$status = 1;
			$info 	= model('Passport')->getSuccess();
			$data 	= $result;
		}
		
		echo '{status:'.$status.',info:"'.$info.'",data:'.$this->JSON($data).'}';
	}

    public function userregist()
    {
        $login = $_GET['login'];
        $pwd = $_GET['pwd'];
        $dt = $_GET['time'];
        $code = $_GET['code'];



    }
	
	
	
}