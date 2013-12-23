<?php
/**
 * 关注模型 - 数据对象模型
 * @author zhangzc 
 * @version TS3.0
 */
class FeedFollowingModel extends Model {

	protected $tableName = 'feed_following';
	protected $fields =	array(0=>'feed_following_id',1=>'uid',2=>'feedid',3=>'ctime');

	/**
	 * 添加关注记录
	 * @param array $data 关注相关数据
	 * @return boolean 是否关注成功
	 */
	public function addFeedFollowing($data) {
		// 验证数据
		if(empty($data['feedid'])) {
			$this->error = "关注数据不能为空";			// 关注数据不能为空
			return false;
		}
		// 判断是否已关注 
		$isExist = $this->getFeedFollowing($data['feedid']);
		if(!empty($isExist)) {
			$this->error = "您已经关注过了";		// 您已经关注过了
			return false;
		}
		
		$data['uid'] = !$data['uid'] ? $GLOBALS['ts']['mid'] : $data['uid'];
		if ( !$data['uid'] ){
			$this->error = '未登录关注失败';		// 关注失败
			return false;
		}
		$data['feedid'] = intval($data['feedid']);
		$data['ctime'] = time();	
		if($data['feed_following_id'] = $this->add($data)) {
			// 生成缓存
			model('Cache')->set('feed_following_'.$data['uid'].'_'.$data['feedid'], $data);

			// 关注数加1
			model('UserData')->updateKey('feed_following_count', 1);
			return true;
		} else {
			$this->error = '关注失败';		// 关注失败,您可能已经关注此资源
			return false;
		}
	}
	
	/**
	 * 获取关注列表
	 * @param array $map 查询条件
	 * @param integer $limit 结果集显示数目，默认为20
	 * @param string $order 排序条件，默认为ctime DESC
	 * @return array 关注列表数据
	 */
	public function getFeedFollowingList($map, $limit = 20, $order = 'ctime DESC') {
		$list = $this->where($map)->order($order)->findPage($limit);
		foreach($list['data'] as &$v) {
			$publish_time = array('publish_time'=>$v['ctime']);
			$data = model('Feed')->CreateA(array(),array($v['feedid']));
			$feedData = array('feed_data'=>$data);
			$v = array_merge($v, $publish_time, $feedData);
		}
		return $list;
	}
	
	/**
	 * 获取个人关注列表(返回A类问题框)
	 *
	 * @return 
	 *
	 */	
	public function getFeedFollowingList1($map, $limit = 20, $order = 'ctime DESC')
	{
		$list = $this->where($map)->order($order)->findPage($limit);
		$feed_ids = getSubByKey($list['data'], 'feedid');
		$result=model('Feed')->CreateA($list,$feed_ids);
		//print_r($result);
		return $result;
	}

	
	/**
	 * 获取指定关注的信息
	 * @param integer $fid 问题ID
	 * @param integer $uid 用户UID
	 * @return array 指定关注的信息
	 */
	public function getFeedFollowing($fid, $uid = '') {
		// 验证数据
		if(empty($fid)) {
			$this->error = '参数错误';		// 错误的参数
			return false;
		}

		empty($uid) && $uid = $GLOBALS['ts']['mid'];
		// 获取关注信息
		if(($cache = model('Cache')->get('feed_following_'.$uid.'_'.$fid) ) === false) {
			$map['feedid'] = $fid;
			$map['uid'] = $uid;
			$cache = $this->where($map)->find();
			model('Cache')->set('feed_following_'.$uid.'_'.$fid, $cache);
		}

		return $cache;
	}
	
	/**
	 * 取消关注
	 * @param integer $sid 资源ID
	 * @param string $stable 资源表名称
	 * @param integer $uid 用户UID
	 * @return boolean 是否取消关注成功
	 */
	public function delFeedFollowing($fid, $uid = '') {
		// 验证数据
		if(empty($fid)) {
			$this->error = '参数错误';		// 错误的参数
			return false;
		}

		$uid = empty($uid) ? $GLOBALS['ts']['mid'] : $uid;
		$map['uid'] = $uid;
		$map['feedid'] = $fid;
		// 取消关注操作
		if($this->where( $map )->delete()){
			// 设置缓存
			model('Cache')->set('feed_following_'.$uid.'_'.$fid, '');
			// 关注数减1
			model('UserData')->updateKey('feed_following_count', -1);
			return true;
		} else {
			$this->error = "取消失败";		// 取消失败,您可能已经取消了该信息的关注
			return false;
		}
	}

}