<?php
/**
 * 朋友圈主题数据模型
 * @author zhangzc
 * @version TS3.0
 */
class TopicModel extends Model {

	protected $tableName = 'topic';
	protected $fields = array('topic_id','topic_content','topic_dt','topic_img','topic_uid','topic_state','topic_groupid','topic_commentcount','_pk'=>'topic_id');
	
	public function getTopicByID($topicid)
	{
		$data = $this->where('topic_id='.$topicid)->order('topic_dt desc')->select();
		$topiclist = array();
		$topiclist['data'] = $data;
		$result=$this->CreateArr($topiclist);
		
		return $result;
	}	
	
	public function getTopicList($where, $limit=10, $userid = 0)
	{
		$topiclist = $this->where($where)->order('topic_dt desc')->findPage($limit);
		
		$result=$this->CreateArr($topiclist);
		
		return $result;
	}
	
	
	public function getMyTopicList($where, $limit=10, $userid = 0)
	{
		$topiclist = $this->where($where)->order('topic_dt desc')->findPage($limit);
		
		$result=$this->CreateArr($topiclist);
		
		return $result;
	}
	
	public function CreateArr($topiclist)
	{
		//增加答案块/评论块
		foreach( $topiclist["data"] as $v => $vv )
		{
			if(!empty($vv['topic_img']))
			{
				$imgStr = substr($vv['topic_img'],0,strlen($vv['topic_img'])-1);
				$imgArr = explode(';',$imgStr);
				
				$TopicImglist=Array();
				foreach($imgArr as $key=>$value)
				{
					$name=strtolower(substr($value,0,(strrpos($value,'.'))));
					$exname=strtolower(substr($value,(strrpos($value,'.')+1)));
					$slimage = $name.'_50_50'.'.'.$exname;
					$slvalue = SITE_URL.C('PYQ_IMAGE').$slimage;
					$value = SITE_URL.C('PYQ_IMAGE').$value;
					$TopicImg=Array();
					$TopicImg['suolvimg'] = $slvalue;
					$TopicImg['img'] = $value;
					array_push($TopicImglist,$TopicImg);
				}
				$vv['topicImage'] = $TopicImglist;
			}
			$userinfo = model('user')->getUserInfo($vv['topic_uid']);
			$vv['userinfo'] = $userinfo;
			$topiclist["data"][$v] = $vv;
		}
		
		return $topiclist;
	}
}