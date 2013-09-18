<?php
/**
 * 感兴趣的人模型 - 业务逻辑模型
 * @author zivss <guolee226@gmail.com>
 * @version TS3.0
 */
class RelatedUserModel extends Model {

	private $_uid = 0;					// 查询用户ID
	private $_exclude_uids = array();	// 排除用户ID数组
	private $_user_model;				// 用户模型对象
	private $_user_follow;				// 用户关注对象
	private $_error = '';				// 存储最后的错误信息
	private $user_sql_where = ' and is_active=1 and is_audit=1 and is_init=1 ';  //过滤掉未激活，未审核，未初始化的用户

	/**
	 * 初始化
	 */
	public function _initialize() {
		$this->setUid($GLOBALS['ts']['mid']);
		$this->_user_model = model('User');
		$this->_user_follow = model('Follow');
	}

	/**
	 * 设置关联用户
	 * @param integer $uid 用户ID
	 * @return void
	 */
	public function setUid($uid) {
		$uid = intval($uid);
		$uid < 0 && $uid = $GLOBALS['ts']['mid'];
		$this->_uid = $uid;
	}

	/**
	 * 获取最后的错误信息
	 * @return string 最后的错误信息
	 */
	public function getLastError() {
		return $this->_error;
	}

	/**
	 * 可能感兴趣的人
	 * @example
	 * 1.一周以内新注册的用户，最新注册用户推荐
	 * 2.好友的好友推荐，XX（我关注的人）也关注了TA
	 * 3.有共同好友的用户推荐，A，B（我关注的人）都关注了TA
	 * 4.有共同好友的用户推荐，有X个共同好友（我与这个人又XX个共同关组） - 未完成
	 * 5.职业信息推荐，TA跟你的职业信息相同
	 * 6.地区信息推荐，TA与你在同一个地方，只实现三级匹配
	 * 7.随机推荐
	 * @param integer $show 显示个数，默认为4
	 * @param integer $limit 查询缓存个数，默认为100
	 * @return array 可能感兴趣的人数组
	 */
	public function getRelatedUser($show = 4, $limit = 100) {
		// 获取100个用户的缓存
		$relatedUseInfo = model('Cache')->get('related_user_'.$GLOBALS['ts']['mid']);
		if(empty($relatedUseInfo)) {
			//过滤掉当前显示的用户
			if ( $show == 1 ){
				$nowrelated = $_SESSION['now_related_'.$GLOBALS['ts']['mid']];
				$this->_getExcludeUids( $nowrelated );
			}
			// 添加查询用户ID
			$this->_getExcludeUids(array($this->_uid));
			$fids = $this->_user_follow->where('uid='.$this->_uid)->getAsFieldArray('fid');
			//过滤掉未激活，未审核，未初始化的用户
			$notin = model('User')->where('is_active=0 or is_audit=0 or is_init=0')->field('uid')->findAll();
			$notinids = getSubByKey(  $notin , 'uid');
			$this->_getExcludeUids($notinids);
			$this->_getExcludeUids($fids);
			// 用户关联信息
			$relatedUseInfo = array();
			// 获取用户权重
			$weightsNum['following'] = 6;
			$weightsNum['friend'] = 5;
			$weightsNum['city'] = 4;
			$weightsNum['tag'] = 3;
			$weightsNum['new'] = 2;
			$weightsNum['random'] = 1;
			// 权重比例
			$weightsSum = array_sum($weightsNum);
			// 好友的共同好友
			$nums = ceil($limit * $weightsNum['following'] / $weightsSum);
			$relatedUseInfo = $this->_getRelatedUserFromFollowing($nums);
			// 关注的人
			$nums = ceil($limit * $weightsNum['friend'] / $weightsSum);
			$data = $this->_getRelatedUserFromFriend($nums,$limit);
			!empty($data) && $relatedUseInfo = array_merge($relatedUseInfo, $data);
			// 城市相同
			$nums = ceil($limit * $weightsNum['city'] / $weightsSum);
			$data = $this->_getRelatedUserFromCity($nums,$limit);
			!empty($data) && $relatedUseInfo = array_merge($relatedUseInfo, $data);
			// 工作相同
			$nums = ceil($limit * $weightsNum['tag'] / $weightsSum);
			$data = $this->_getRelatedUserFromTag($nums,$limit);
			!empty($data) && $relatedUseInfo = array_merge($relatedUseInfo, $data);
			// 新注册用户
			$nums = ceil($limit * $weightsNum['new'] / $weightsSum);
			$data = $this->_getRelatedUserFromNew($nums,$limit);
			!empty($data) && $relatedUseInfo = array_merge($relatedUseInfo, $data);
			// 随机用户
			$nums = $limit - count($relatedUseInfo);
			$data = $this->_getRelatedUserFromRandom($nums,$limit);
			!empty($data) && $relatedUseInfo = array_merge($relatedUseInfo, $data);
			// 添加缓存
			model('Cache')->set('related_user_'.$GLOBALS['ts']['mid'], $relatedUseInfo, 24 * 60 * 60);
		}

		srand((float)microtime() * 1000000);
		shuffle($relatedUseInfo);
		$relatedUseInfo = array_slice($relatedUseInfo, 0, $show);
		$nowshow = getSubByKey( getSubByKey( $relatedUseInfo , 'userInfo' ) , 'uid' );
		//将当前显示的用户存入SESSION
		if ( $show == 1 ){
			$sessionshow = array_merge( $_SESSION['now_related_'.$GLOBALS['ts']['mid']] , $nowshow );
			$_SESSION['now_related_'.$GLOBALS['ts']['mid']] = $sessionshow;
		} else {
			$_SESSION['now_related_'.$GLOBALS['ts']['mid']] = $nowshow;
		}
		return $relatedUseInfo;
	}

	/**
	 * 获取指定类型的关联用户
	 * @param string $type 类型字符串
	 * @param integer $limit 显示个数
	 * @return array 指定类型的关联用户
	 */
	public function getRelatedUserByType($type, $limit){
		// 添加查询用户ID
		$this->_getExcludeUids(array($this->_uid));
		//过滤掉未激活，未审核，未初始化的用户
		$notin = model('User')->where('is_active=0 or is_audit=0 or is_init=0')->field('uid')->findAll();
		$notinids = getSubByKey(  $notin , 'uid');
		$this->_getExcludeUids($notinids);
		$fids = $this->_user_follow->where('uid='.$this->_uid)->getAsFieldArray('fid');
		$this->_getExcludeUids($fids);
		// 用户关联信息
		$relatedUseInfo = array();
		for($i = 0; $i < $limit; $i++) {
			switch($type) {
				case 1:
					$data = $this->_getRelatedUserFromNew();
					break;
				case 2;
					$data = $this->_getRelatedUserFromFriend();
					break;
				case 3:
					$data = $this->_getRelatedUserFromCity();
					break;
				case 4:
					$data = $this->_getRelatedUserFromTag();
					break;
				case 5:
					$data = $this->_getRelatedUserFromRecommend();
					break;
			}
			$relatedUseInfo = array_merge($relatedUseInfo, $data);
		}
		
		return $relatedUseInfo;
	}

	/**
	 * 注册用户推荐
	 * @param integer $limit 查询用户个数，默认为20
	 * @return array 推荐用户ID数组
	 */
	public function getRelatedUserWithLogin($limit = 20) {
		$this->_getExcludeUids(array($this->_uid));
		$fids = $this->_user_follow->where('uid='.$this->_uid)->getAsFieldArray('fid');
		!empty($fids) && $this->_getExcludeUids($fids);
		// 用户ID
		$relatedUids = array();
		for($i = 0; $i < $limit; $i++) {
			$random = rand(1, 4);
			switch($random) {
				case 1:
					$data = $this->_getRelatedUserFromNew();
					break;
				case 2:
					$data = $this->_getRelatedUserFromCity();
					break;
				case 3:
					$data = $this->_getRelatedUserFromTag();
					break;
				case 4:
					$data = $this->_getRelatedUserFromRandom();
					break;
			}
			$relatedUids = array_merge($relatedUids, $data);
		}
		// 用户结果集合
		$result = array();
		foreach($relatedUids as $value) {
			$result[] = $value['userInfo']['uid'];
		}
		return $result;
	}

	/**
	 * 设置排除用户ID
	 * @param array $uids 排除用户ID数组
	 * @return array 排除用户ID
	 */
	private function _getExcludeUids($uids = array()) {
		if(!empty($uids)) {
			$this->_exclude_uids = array_merge($this->_exclude_uids, $uids);
			$this->_exclude_uids = array_filter($this->_exclude_uids);
			$this->_exclude_uids = array_unique($this->_exclude_uids);
		}
		if(empty($this->_exclude_uids)){
			$this->_exclude_uids = array(0);
		}
	}

	/**
	 * 新注册用户推荐
	 * @param integer $limit 查询个数，默认为1
	 * @return array 新注册用户信息
	 */
	private function _getRelatedUserFromNew($num = 1 , $limit = 100) {
		$time = time() - mktime(0, 0, 0, 0, 7, 0) + mktime(0,0,0,0,0,0);
		// 随机查询语句
		$limit = $limit * 10;
		$sql = "SELECT `uid` FROM `{$this->tablePrefix}user` WHERE `ctime` > $time AND `uid` NOT IN (".implode(',', $this->_exclude_uids).") LIMIT {$limit}";
		$data = D()->query($sql);
		$data = getSubByKey($data, 'uid');
		$data && $data = $this->_data_array_rand( $data , $num );
		if(empty($data)) {
			return array();
		}
		// 用户基本信息
		$userInfos = $this->_user_model->getUserInfoByUids($data);
		// 用户关注状态
		$userStates = $this->_user_follow->getFollowStateByFids($this->_uid, $data);
		// 设置去除用户
		$this->_getExcludeUids($data);
		foreach($data as $key => $value) {
			if ( !$userInfos[$value] ){
				unset( $data[$key] );
				continue;
			}
			$data[$key] = array('userInfo'=>$userInfos[$value]);
			$data[$key]['followState'] = $userStates[$value];
			$data[$key]['info']['msg'] = '最新注册用户推荐';
			$data[$key]['info']['extendMsg'] = '';
		}
		
		return $data;
	}

	/**
	 * 好友的好友用户推荐
	 * @param integer $limit 查询个数，默认为1
	 * @return array 好友的好友用户信息
	 */
	private function _getRelatedUserFromFriend($num = 1,$limit = 100) {
		// 获取我的好友
		$friendUids = $this->_user_follow->getFriendsData($this->_uid);
		$friendUids = getSubByKey($friendUids, 'fid');
		if(empty($friendUids)) {
			return array();
		}
		// 获取好友关注的用户
		$limit = $limit * 10;
		// 获取好友关注的用户 TODO 待过滤不合格的用户
		$sql = "SELECT `uid`, `fid` FROM `{$this->tablePrefix}user_follow` WHERE `uid` IN (".implode(',', $friendUids).") AND fid NOT IN (".implode(',', $this->_exclude_uids).") LIMIT {$limit}";
		$friendData = D()->query($sql);
		$data = getSubByKey($friendData, 'fid');
		$data = array_unique($data);
		$data && $data = $this->_data_array_rand( $data , $num );
		
		if(empty($data)) {
			return array();
		}
		// 用户基本信息
		$userInfos = $this->_user_model->getUserInfoByUids($data);
		// 用户关注状态
		$userStates = $this->_user_follow->getFollowStateByFids($this->_uid, $data);
		// 设置去除用户
		$this->_getExcludeUids($data);
		foreach($data as $key => $value) {
			if ( !$userInfos[$value] ){
				unset( $data[$key] );
				continue;
			}
			$data[$key] = array('userInfo'=>$userInfos[$value]);
			$data[$key]['followState'] = $userStates[$value];
			// 获取相关用户
			$relatedUids = array();
			foreach($friendData as $val) {
				if($val['fid'] == $value) {
					$relatedUids[] = $val['uid'];
				}
			}
			$relatedInfos = $this->_user_model->getUserInfoByUids($relatedUids);
			$relatedInfos = getSubByKey($relatedInfos, 'uname');
			$data[$key]['info']['msg'] = $relatedInfos[0].'也关注了TA';
			$data[$key]['extendMsg'] = '';
		}

		return $data;
	}

	/**
	 * 获取有共同好友的用户推荐
	 * @param integer $limit 查询个人，默认为1
	 * @return array 有共同好友的用户推荐
	 */
	public function _getRelatedUserFromFollowing($limit = 2) {
		// 获取用户的关组用户
		$followFids = $this->_user_follow->where('uid='.$this->_uid)->getAsFieldArray('fid');
		// 获取关注用户所关注的用户
		if(empty($followFids)) {
			return array();
		}
		// 获取有共同好友的用户推荐  TODO 待过滤不合格的用户
		$sql = "SELECT `uid`, `fid` FROM `{$this->tablePrefix}user_follow` WHERE `uid` IN (".implode(',', $followFids).") AND fid NOT IN (".implode(',', $this->_exclude_uids).")";
		$followData = D()->query($sql);
		$fids = getSubByKey($followData, 'fid');
		$count = array_count_values($fids);
		// 推荐用户筛选
		foreach($count as $key => $value) {
			if($value < 2) {
				unset($count[$key]);
			}
		}
		if(empty($count)) {
			return array();
		}
		if(count($count) > $limit) {
			$data = $this->_data_array_rand($count, $limit);
			count($data) == 1 && $data = array($data);
		} else {
			$data = array_keys($count);
		}
		// 用户基本信息
		$userInfos = $this->_user_model->getUserInfoByUids($data);
		// 用户关注状态
		$userStates = $this->_user_follow->getFollowStateByFids($this->_uid, $data);
		// 设置去除用户
		$this->_getExcludeUids($data);
		foreach($data as $key => $value) {
			if ( !$userInfos[$value] ){
				unset( $data[$key] );
				continue;
			}
			$data[$key] = array('userInfo'=>$userInfos[$value]);
			$data[$key]['followState'] = $userStates[$value];
			// 获取相关用户
			$relatedUids = array();
			foreach($followData as $val) {
				if($val['fid'] == $value) {
					$relatedUids[] = $val['uid'];
				}
			}
			$relatedInfos = $this->_user_model->getUserInfoByUids($relatedUids);
			$relatedInfos = getSubByKey($relatedInfos, 'space_link_no');
			$relatedInfos = array_slice($relatedInfos, 0, 2);
			$data[$key]['info']['msg'] = '好友的共同好友推荐';
			$data[$key]['info']['extendMsg'] = implode('，', $relatedInfos).'也关注了TA';
		}
		
		return $data;
	}
	
	/**
	 * 获取相同的用户标签用户
	 * @param integer $limit 查询个数，默认为1
	 * @return array 相同的用户标签用户数据
	 */
	private function _getRelatedUserFromTag($num = 1 , $limit = 100) {

		// 获取用户的标签信息
		$maps['app'] = 'public';
		$maps['table'] = 'user';
		$maps['row_id'] = $this->_uid;
		$tagInfo = D('app_tag')->where($maps)->findAll();
		$tagIds = getSubByKey($tagInfo, 'tag_id');
		if(empty($tagIds)) {
			return array();
		}
		
		// 获取具有相同标签信息的用户
		$limit = $limit * 10;
		$sql = "SELECT `row_id`, `tag_id` FROM `{$this->tablePrefix}app_tag` AS a WHERE `tag_id` IN (".implode(',', $tagIds).") AND `row_id` NOT IN (".implode(',', $this->_exclude_uids).") group by `row_id` LIMIT {$limit}";
		$tagData = D()->query($sql);
		// Tag Hash数组
		$tagHash = array();
		foreach($tagData as $tag) {
			$tagHash[$tag['row_id']] = $tag['tag_id'];
		}
		$data = getSubByKey($tagData, 'row_id');
		$data = array_unique($data);
		$data && $data = $this->_data_array_rand( $data , $num );
		if(empty($data)) {
			return array();
		}
		
		// 用户基本信息
		$userInfos = $this->_user_model->getUserInfoByUids($data);

		// 用户关注状态
		$userStates = $this->_user_follow->getFollowStateByFids($this->_uid, $data);
		// 设置去除用户
		$this->_getExcludeUids($data);
		foreach($data as $key => $value) {
			if ( !$userInfos[$value] ){
				unset( $data[$key] );
				continue;
			}
			$data[$key] = array('userInfo'=>$userInfos[$value]);
			$data[$key]['followState'] = $userStates[$value];
			// 获取标签信息
			$tag_id = 0;
			foreach($tagData as $val) {
				if($val['row_id'] == $value) {
					$tag_id = $tagHash[$val['row_id']];
					break;
				}
			}
			$tagName = model('Tag')->where('tag_id='.$tag_id)->getField('name');
			$data[$key]['info']['msg'] = 'TA跟你的标签信息相同';
			$data[$key]['info']['extendMsg'] = '你们都选择了'.$tagName;
			$data[$key]['uid'] = $value;

		}
		
		return $data;
	}

	/**
	 * 获取相同地区的用户
	 * @param integer $limit 查询个数，默认为1
	 * @return array 相同地区的用户数据
	 */
	private function _getRelatedUserFromCity($num = 1 , $limit = 100) {
		// 获取用户的区信息
		$areaInfo = $this->_user_model->field('city, area')->where('uid='.$this->_uid)->find();
		$areaId = $areaInfo['area'];
		// 获取地区信息
		if(empty($areaId)) {
			return array();
		}
		// 获取相同地区的用户
		$limit = $limit * 10;
		$sql = "SELECT `uid` FROM `{$this->tablePrefix}user` AS a WHERE `area` = {$areaId} AND `uid` NOT IN (".implode(',', $this->_exclude_uids).") ".$this->user_sql_where." LIMIT {$limit}";
		$data = D()->query($sql);
		$data = getSubByKey($data, 'uid');
		$data && $data = $this->_data_array_rand( $data , $num );
		// 用户基本信息
		$userInfos = $this->_user_model->getUserInfoByUids($data);
		// 用户关注状态
		$userStates = $this->_user_follow->getFollowStateByFids($this->_uid, $data);
		// 设置去除用户
		$this->_getExcludeUids($data);
		foreach($data as $key => $value) {
			if ( !$userInfos[$value] ){
				unset( $data[$key] );
				continue;
			}
			$data[$key] = array('userInfo'=>$userInfos[$value]);
			$data[$key]['followState'] = $userStates[$value];
			// 获取地区信息
			$map['area_id'] = array('IN', array($areaInfo['city'], $areaInfo['area']));
			$areaName = model('Area')->where($map)->getAsFieldArray('title');
			$areaName = implode(' ', $areaName);
			$data[$key]['info']['msg'] = 'TA与你在同一个地方';
			$data[$key]['info']['extendMsg'] = '你们都在'.$areaName;
			$data[$key]['uid'] = $value;
		}

		return $data;
	}

	/**
	 * 获取后台推荐用户
	 * @param integer $limit 查询个数，默认为1
	 * @return array 相同的用户标签用户数据
	 */
	private function _getRelatedUserFromRecommend($num = 1,$limit = 100) {

		// 获取用户的区信息
		$RegisterConfig = model('Xdata')->get('admin_Config:register');
		$recommendUids = $RegisterConfig['interester_recommend'];
		// 获取地区信息
		if(empty($recommendUids)) {
			return array();
		}
		// 获取相同地区的用户
		$limit = $limit * 10;
		$sql = "SELECT `uid` FROM `{$this->tablePrefix}user` WHERE `uid` IN (".$recommendUids.") 
				AND `uid` NOT IN (".implode(',', $this->_exclude_uids).") ".$this->user_sql_where." LIMIT {$limit}";
		$data = D()->query($sql);
		//return $data;exit;
		$data = getSubByKey($data, 'uid');
		$data & $data = $this->_data_array_rand( $data , $num );
		// 用户基本信息
		$userInfos = $this->_user_model->getUserInfoByUids($data);
		// 用户关注状态
		$userStates = $this->_user_follow->getFollowStateByFids($this->_uid, $data);
		// 设置去除用户
		$this->_getExcludeUids($data);
		foreach($data as $key => $value) {
			if ( !$userInfos[$value] ){
				unset( $data[$key] );
				continue;
			}
			$data[$key] = array('userInfo'=>$userInfos[$value]);
			$data[$key]['followState'] = $userStates[$value];
			$data[$key]['info']['msg'] = '后台推荐用户';
			$data[$key]['uid'] = $value;
		}

		return $data;
	}

	/**
	 * 获取随机用户
	 * @param integer $limit 查询个数，默认为1
	 * @return array 随机用户信息
	 */
	private function _getRelatedUserFromRandom($num = 1 , $limit = 100 ) {
		// 获取随机用户
		$limit = $limit * 10;
		$sql = "SELECT `uid` FROM `{$this->tablePrefix}user` AS a WHERE `uid` NOT IN (".implode(',', $this->_exclude_uids).") LIMIT {$limit}";
		$data = D()->query($sql);
		$data = getSubByKey($data, 'uid');
		$data && $this->_data_array_rand( $data , $num );
		// 用户基本信息
		$userInfos = $this->_user_model->getUserInfoByUids($data);
		// 用户关注状态
		$userStates = $this->_user_follow->getFollowStateByFids($this->_uid, $data);
		// 设置去除用户
		$this->_getExcludeUids($data);
		foreach($data as $key => $value) {
			if ( !$userInfos[$value] ){
				unset( $data[$key] );
				continue;
			}
			$data[$key] = array('userInfo'=>$userInfos[$value]);
			$data[$key]['followState'] = $userStates[$value];
			$data[$key]['info']['msg'] = '系统推荐';
			$data[$key]['info']['extendMsg'] = '';
		}

		return $data;
	}
	private function _data_array_rand($data, $num){
		shuffle($data);
		return array_slice($data, 0, $num);
	}
}