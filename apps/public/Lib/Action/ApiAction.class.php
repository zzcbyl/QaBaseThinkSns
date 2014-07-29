<?php
/**
 * ����ӿ�
 * @author zhangzc
 * @version TS3.0
 */
class ApiAction 
{
    private $limitnums = 10;
	/**
	 * ȫվ����
	 *
	 * @return JSON
	 *
	 */	
	public function getAllFeed()
	{
		$where =" (is_audit=1 OR is_audit=0) AND is_del = 0 AND feed_questionid=0 AND add_feedid=0";
		if($var['loadId'] > 0){ //�ǵ�һ��
			$where .=" AND `last_updtime` < '".intval($var['loadId'])."'";
		}
		$list = model('Feed')->getQuestionAndAnswer($where,$this->limitnums);
		
		echo json_encode($list);
	}
	
	/**
	 * ��ȡ�ҵ�����
	 * 
	 * @param uid  �û�ID
	 * @return JSON
	 *
	 */	
	public function getMyQuestion()
	{
		$current_uid = intval($_GET['uid']);
		if($var['loadId'] > 0){ //�ǵ�һ��
			$LoadWhere = "AND publish_time < '".intval($var['loadId'])."'";
		}
		$where =' uid='.$current_uid.' AND is_del = 0 AND feed_questionid=0 AND add_feedid=0 AND (is_audit=1 OR is_audit=0) '.$LoadWhere;
		$list = model('Feed')->getQuestionAndAnswer($where, $this->limitnums);
		echo json_encode($list);
	}
	
	/**
	 * ��ȡ�ҵĻش�
	 *
	 * @param uid �û�ID
	 * @return JSON
	 *
	 */ 	
	public function getMyAnswer()
	{
		$current_uid = intval($_GET['uid']);
		if($var['loadId'] > 0){ //�ǵ�һ��
			$LoadWhere = "AND publish_time < '".intval($var['loadId'])."'";
		}
		$where =' uid='.$current_uid.' AND is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND (is_audit=1 OR is_audit=0) '.$LoadWhere;
		$list = model('Feed')->getAnswerList($where, $this->limitnums);
		echo json_encode($list);
	}
	
	/**
	 * �����ҵ�
	 *
	 * @param uid �û�ID
	 * @return JSON
	 *
	 */	
	public function getInviteMe()
	{
		$current_uid = intval($_GET['uid']);
		if($var['loadId'] > 0){ //�ǵ�һ��
			$LoadWhere = "invite_answer_id < '".intval($var['loadId'])."'";
		}
		$list =  model('Feed')->getInviteList($current_uid, $this->limitnums, $LoadWhere, $var['newcount']);
		echo json_encode($list);
	}
	
	/**
	 * ��ȡ�ҵĹ�ע
	 *
	 * @param uid �û�ID
	 * @return JSON
	 *
	 */	
	public function getMyFollowing()
	{
		$current_uid = intval($_GET['uid']);
		
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
		// ��ȡ�û�����Ϣ
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
	 * ��ȡ�ҵķ�˿
	 *
	 * @param uid �û�ID
	 * @return JSON
	 *
	 */	
	public function getMyFollower()
	{
		$current_uid = intval($_GET['uid']);
		
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
		// ��ȡ�û�����Ϣ
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
	 * ��ȡ�ҵĺ���
	 *
	 * @param uid �û�ID
	 * @return JSON
	 *
	 */	
	public function getMyFriend()
	{
		$current_uid = intval($_GET['uid']);
		
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
		// ��ȡ�û�����Ϣ
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
	 * ͨ��ID��ȡ�û�
	 *
	 * @param uid �û�ID
	 * @return JSON
	 *
	 */	
	public function getUserID()
	{
		$current_uid = intval($_GET['uid']);
		
		$user = model('User')->getUserInfo($current_uid);
		
		echo json_encode($user);
		
	}
	
	/**
	 * ͨ��OpenID��ȡ�û�
	 *
	 * @param uid �û�ID
	 * @return JSON
	 *
	 */	
	public function getUserOpenID()
	{
		$OpenID = $_GET['openID'];
		
		$user = model('User')->getUserInfoByOpenID($OpenID);
		
		echo json_encode($user);
	}
	
	/**
	 * ��������(5��)
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
	 * ��������(5��)
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
}