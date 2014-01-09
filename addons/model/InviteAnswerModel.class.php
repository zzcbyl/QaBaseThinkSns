<?php
/**
 * 邀请回答模型 - 数据对象模型
 * @author zhangzc
 * @version 1.0
 */
class InviteAnswerModel extends Model {

	protected $tableName = 'invite_answer';
	protected $fields = array(0=>'invite_answer_id',1=>'uid',2=>'invite_uid',3=>'questionid',4=>'answerid',5=>'ctime','_autoinc'=>true,'_pk'=>'invite_answer_id');

	/**
	 * 添加邀请回答
	 * 
	 * @param integer $uid 发起操作的邀请用户ID
	 * @param integer $invite_uid 被邀请的用户ID
	 * @param integer $question 问题ID
	 * @return boolean 是否关注成功
	 * 
	 */
	public function addInvite($uid, $invite_uid, $questionid) {
		$map['uid'] = $uid;
		$map['invite_uid'] = $invite_uid;
		$map['questionid'] = $questionid;
		$map['ctime'] = time();
		$result = $this->add($map);
		if($result) {
			// 添加邀请数和被邀请数
			$this->_updInviteCount($uid, $invite_uid);
			return true;
		}
		else
		{
			return false;	
		}
	}
	
	/**
	 * 统计邀请回答
	 *
	 * @param mixed $uid 发起操作的邀请用户ID
	 * @param mixed $invite_uid 被邀请的用户ID
	 * @param mixed $inc 添加还是减少
	 * @return mixed void
	 *
	 */	
	public function _updInviteCount($uid, $invite_uid, $inc = true)
	{
		model('UserData')->setUid($uid)->updateKey('invite_count', 1, $inc);
		model('UserData')->setUid($invite_uid)->updateKey('be_invited_count', 1, $inc);
		model('UserData')->setUid($invite_uid)->updateKey('new_be_invited_count', 1, $inc);	
	}
	
	/**
	 * 邀请回答结束更新回答ID
	 *
	 * @param mixed $uid 发起操作的邀请用户ID
	 * @param mixed $invite_uid 被邀请的用户ID
	 * @param integer $question 问题ID
	 * @param integer $answerid 答案ID
	 * @return mixed 影响的条数或者false
	 *
	 */	
	public function answerInvite($uid, $invite_uid, $questionid, $answerid)
	{
		$data['answerid'] = $answerid;
		$InviteResult = $this->where("`uid` = $uid and `invite_uid` = $invite_uid and `questionid` = $questionid")->save($data);
		return $InviteResult;
	}
	
	/**
	 * 根据问题获取邀请回答列表(可直接使用用户信息)
	 *
	 * @param int $questionid 问题ID
	 * @return void array
	 *
	 */	
	public function getInviteAnswerModel($questionid, $limit = 10, $order = 'invite_answer_id desc')
	{
		$InviteList = $this->where("`questionid` = $questionid")->order($order)->findPage($limit);
		foreach( $InviteList['data'] as $k=>$v)
		{
			$UserInfo = model('user')->getUserInfo($v['uid']);
			$inviteUserInfo = model('user')->getUserInfo($v['invite_uid']);
			$v['userinfo'] = $UserInfo;
			$v['inviteuserinfo'] = $inviteUserInfo;
			$InviteList['data'][$k] = $v;
		}
		
		return $InviteList;
	}
	
	/**
	* 根据问题获取邀请回答列表(返回关联列表)
	*
	* @param int $questionid 问题ID
	* @return void array
	*
	*/	
	public function getInviteAnswer($questionid, $limit = 10, $order = 'invite_answer_id desc')
	{
		$InviteList = $this->where("`questionid` = $questionid")->order($order)->findPage($limit);
		
		return $InviteList;
	}
	
	/**
	* 根据条件获取邀请回答列表(返回关联列表)
	*
	* @param array $map 条件
	* @return void array
		*
	*/	
	public function getInviteAnswerList($map, $limit = 10, $order = 'invite_answer_id desc')
	{
		$InviteList = $this->where($map)->order($order)->findPage($limit);
		
		return $InviteList;
	}

}
