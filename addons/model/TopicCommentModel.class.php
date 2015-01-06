<?php
/**
 * 第三方关联数据 - 活动报名数据模型
 * @author zhangzc
 * @version TS3.0
 */
class TopicCommentModel extends Model {

	protected $tableName = 'topic_comment';
	protected $fields = array('comment_id','comment_content','comment_uid','comment_topicid','comment_parentid','comment_dt','comment_state', '_pk'=>'comment_id');
	
	
	public function getCommentList($where, $limit=10)
	{
		$commentlist = $this->where($where)->order('comment_id desc')->findPage($limit);
		$result=$this->CreateArr($commentlist);
		//print_r($result);
		return $result;
	}
	
	
	public function CreateArr($commentlist)
	{
		//增加答案块/评论块
		foreach( $commentlist["data"] as $v => $vv )
		{
			$userinfo = model('user')->getUserInfo($vv['comment_uid']);
			$vv['userinfo'] = $userinfo;
			
			if(intval($vv['comment_parentid'])>0)
			{
				$parentcomment = $this->where('`comment_id` = '.$vv['comment_parentid'])->select();
				$parentuserinfo = model('user')->getUserInfo($parentcomment[0]['comment_uid']);
				$parentcomment[0]['userinfo'] = $parentuserinfo;
				$vv['parentcomment'] = $parentcomment[0];
			}
			
			$commentlist["data"][$v] = $vv;
		}
		
		return $commentlist;
	}
}