<?php
/**
 * 微博模型 - 数据对象模型
 * @author jason <yangjs17@yeah.net>
 * @version TS3.0
 */
class FeedModel extends Model {

	protected $tableName = 'feed';
	protected $fields = array('feed_id','uid','type','app','app_row_id','app_row_table','publish_time','is_del','from','comment_count','repost_count','comment_all_count','digg_count','is_repost','is_audit','feed_questionid','feed_quid','answer_count','disapprove_count','feed_pv','thank_count','_pk'=>'feed_id');

	public $templateFile = '';			// 模板文件

	/**
	 * 添加微博
	 * @param integer $uid 操作用户ID
	 * @param string $app 微博应用类型，默认为public
	 * @param string $type 微博类型，
	 * @param array $data 微博相关数据
	 * @param integer $app_id 应用资源ID，默认为0
	 * @param string $app_table 应用资源表名，默认为feed
	 * @param array  $extUid 额外用户ID，默认为null
	 * @param array $lessUids 去除的用户ID，默认为null
	 * @param boolean $isAtMe 是否为进行发送，默认为true
	 * @return mix 添加失败返回false，成功返回新的微博ID
	 */
	public function put($uid, $app = 'public', $type = '', $data = array(), $app_id = 0, $app_table = 'feed', $extUid = null, $lessUids = null, $isAtMe = true, $is_repost = 0) {
		// 判断数据的正确性
		if(!$uid || $type == '') {
			$this->error = L('PUBLIC_ADMIN_OPRETING_ERROR');
			return false;
		}
		if ( strpos( $type , 'postvideo' ) !== false ){
			$type = 'postvideo';
		}
		//微博类型合法性验证 - 临时解决方案
		if ( !in_array( $type , array('post','repost','postvideo','postfile','postimage','weiba_post','weiba_repost') )){
			$type = 'post';
		}
		//应用类型验证 用于分享框 - 临时解决方案
		if ( !in_array( $app , array('public','weiba','tipoff') ) ){
			$app = 'public';
			$type = 'post';
			$app_table = 'feed';
		}
		
		$app_table = strtolower($app_table);
		// 添加feed表记录
		$data['uid'] = $uid;
		$data['app'] = $app;
		$data['type'] = $type;
		$data['app_row_id'] = $app_id;
		$data['app_row_table'] = $app_table;
		$data['publish_time'] = time();
		$data['from'] = isset($data['from']) ? intval($data['from']) : getVisitorClient();
		$data['is_del'] = $data['comment_count'] = $data['repost_count'] = $data['disapprove_count'] = 0;
		$data['is_repost'] = $is_repost;
		if($data['questionid']==null || $data['questionid'] == 0){
			$data['questionid']=0;
			$data['feed_quid']=0;
		}
		else{
			$data['feed_questionid'] = $data['questionid'];
			$QFeedData = $this->getFeeds(array($data['questionid']));
			$data['feed_quid']  = $QFeedData[0]['uid'];
		}
		//判断是否先审后发
		$weiboSet = model('Xdata')->get('admin_Config:feed');
        $weibo_premission = $weiboSet['weibo_premission'];
		if(in_array('audit',$weibo_premission) || CheckPermission('core_normal','feed_audit')){
			$data['is_audit'] = 0;
		}else{
			$data['is_audit'] = 1;
		}
		// 微博内容处理
        if(Addons::requireHooks('weibo_publish_content')){
        	Addons::hook("weibo_publish_content",array(&$data));
        }else{
        	// 拼装数据，如果是评论再转发、回复评论等情况，需要额外叠加对话数据
			$data['body'] = str_replace(SITE_URL, '[SITE_URL]', preg_html($data['body']));
			// 获取用户发送的内容，仅仅以//进行分割
			$scream = explode('//', $data['body']);
			// 截取内容信息为微博内容字数 - 重点
			//$feedConf = model('Xdata')->get('admin_Config:feed');
			//$feedNums = $feedConf['weibo_nums'];
			$feedNums = 43;
			$body = array();
			foreach($scream as $value) {
				$tbody[] = $value;
				$bodyStr = implode('//', $tbody);
				//答案就不检测字数
				if($data['questionid'] == 0 && get_str_length(ltrim($bodyStr)) > $feedNums) {
					break;
				}
				$body[] = $value;
				unset($bodyStr);
			}
			$data['body'] = implode('//', $body);
			// 获取用户发布内容
			$data['content'] = trim($scream[0]);
        }

        //分享到微博的应用资源，加入原资源链接
        $data['body'] .= $data['source_url'];
        $data['content'] .= $data['source_url'];
		
        // 微博类型插件钩子
		// if($type){
		// 	$addonsData = array();
		// 	Addons::hook("weibo_type",array("typeId"=>$type,"typeData"=>$type_data,"result"=>&$addonsData));
		// 	$data = array_merge($data,$addonsData);
		// }
        if( $type == 'postvideo' ){
        	$typedata = model('Video')->_weiboTypePublish( $_POST['videourl'] );
        	if ( $typedata && $typedata['flashvar'] && $typedata['flashimg'] ){
        		$data = array_merge( $data , $typedata );
        	} else {
        		$data['type'] = 'post';
        	}
        		
        }	
		// 添加微博信息
		$feed_id =  $this->data($data)->add();
		if(!$feed_id) return false;
		
		$data['answer_count']=0;
		//如果是回答,增加问题的回答数量
		if($data['questionid']!=null && $data['questionid']!='0')
		{
			$feed_Qid=model('feed')->where('feed_id='.$feed_id)->getField('feed_questionid');
			$updData['answer_count']=model('feed')->where('feed_questionid='.$feed_Qid.' and is_del=0')->count();
			model('feed')->where('feed_id='.$feed_Qid)->save($updData);
			$this->	cleanCache(array($feed_Qid));
			$this->	updateFeedCache($feed_Qid,'update');
			
			// 添加新回答
			$feedQData = model('feed')->getFeeds(array($feed_Qid));
			if($feedQData[0]['uid']>0){
				$data_model = model('UserData');
				$data_model->setUid($feedQData[0]['uid'])->updateKey('new_answer_count', 1, true);
			}
		}
		
		if(!$data['is_audit']){
			$touid = D('user_group_link')->where('user_group_id=1')->field('uid')->findAll();
			foreach($touid as $k=>$v){
				model('Notify')->sendNotify($v['uid'], 'feed_audit');
			}
		}
		// 目前处理方案格式化数据
		$data['content'] = str_replace(chr(31), '', $data['content']);
		$data['body'] = str_replace(chr(31), '', $data['body']);
		$data['description'] = str_replace(chr(31), '', $data['description']);
		
		// 添加关联数据
		$feed_data = D('FeedData')->data(array('feed_id'=>$feed_id,'feed_data'=>serialize($data),'client_ip'=>get_client_ip(),'feed_content'=>$data['body'],'feed_description'=>$data['description']))->add();

		// 添加微博成功后
		if($feed_id && $feed_data) {
			//微博发布成功后的钩子
			//Addons::hook("weibo_publish_after",array('weibo_id'=>$feed_id,'post'=>$data));

			// 发送通知消息 - 重点 - 需要简化把上节点的信息去掉.
			if($data['is_repost'] == 1) {
				// 转发微博
				$isAtMe && $content = $data['content'];									// 内容用户
				$extUid[] = $data['sourceInfo']['transpond_data']['uid'];				// 资源作者用户
				if($isAtMe && !empty($data['curid'])) {
					// 上节点用户
					$appRowData = $this->get($data['curid']);
					$extUid[] = $appRowData['uid'];
				}	
			} else {
				// 其他微博
				$content = $data['content'];
				//更新最近@的人
				model( 'Atme' )->updateRecentAt( $content );								// 内容用户
			}
			// 发送@消息
			model('Atme')->setAppName('Public')->setAppTable('feed')->addAtme($content, $feed_id, $extUid, $lessUids);

			$data['client_ip'] = get_client_ip();
			$data['feed_id'] = $feed_id;
			$data['feed_data'] = serialize($data);
			// 主动创建渲染后的缓存
			$return = $this->setFeedCache($data);
			$return['user_info'] = model('User')->getUserInfo($uid);
			$return['GroupData'] = model('UserGroupLink')->getUserGroupData($uid);   //获取用户组信息
			$return['feed_id'] = $feed_id;
			$return['app_row_id'] = $data['app_row_id'];
			$return['is_audit'] = $data['is_audit'];
			// 统计数修改
			if($data['questionid']!=null&&$data['questionid']!='0')
				model('UserData')->setUid($uid)->updateKey('answer_count', 1);
			else
			{
				model('UserData')->setUid($uid)->updateKey('feed_count', 1);
				// if($app =='public'){ //TODO 微博验证条件
				model('UserData')->setUid($uid)->updateKey('weibo_count', 1);
				// }
			}
			
			if(!$return) {
				$this->error = L('PUBLIC_CACHE_FAIL');				// Feed缓存写入失败
			}
			return $return;
		} else {
			$this->error = L('PUBLIC_ADMIN_OPRETING_ERROR');		// 操作失败
			return false;
		}
	}

	/**
	 * 获取指定微博的信息
	 * @param integer $feed_id 微博ID
	 * @return mix 获取失败返回false，成功返回微博信息
	 */
	public function get($feed_id) {
		$feed_list = $this->getFeeds(array($feed_id));
		if(!$feed_list) {
			$this->error = L('PUBLIC_INFO_GET_FAIL');			// 获取信息失败
			return false;
		} else {
			return $feed_list[0];
		}
	}

	/**
	 * 获取指定微博的信息，用于资源模型输出???
	 * @param integer $id 微博ID
	 * @param boolean $forApi 是否提供API数据，默认为false
	 * @return array 指定微博数据
	 */
	public function getFeedInfo($id, $forApi = false) {
		$data = model( 'Cache' )->get( 'feed_info_'.$id );
		if ( $data !== false ){
			return $data;
		}

		$map['a.feed_id'] = $id;
		
		// //过滤已删除的微博 wap 版收藏
		// if($forApi){
		// 	$map['a.is_del'] = 0;
		// }
		
		$data = $this->where($map)
					 ->table("{$this->tablePrefix}feed AS a LEFT JOIN {$this->tablePrefix}feed_data AS b ON a.feed_id = b.feed_id ")
					 ->find();

		$fd = unserialize($data['feed_data']);	

		$userInfo = model('User')->getUserInfo($data['uid']);
		$data['ctime'] = date('Y-m-d H:i',$data['publish_time']);
		$data['content'] = $forApi ? parseForApi($fd['body']):$fd['body'];
		$data['uname'] = $userInfo['uname'];
		$data['user_group'] = $userInfo['api_user_group'];
		$data['avatar_big'] = $userInfo['avatar_big'];
		$data['avatar_middle'] = $userInfo['avatar_middle'];
		$data['avatar_small']  = $userInfo['avatar_small'];
		unset($data['feed_data']);
		
		// 微博转发
		if($data['type'] == 'repost'){
			$data['transpond_id'] = $data['app_row_id'];
			$data['transpond_data'] = $this->getFeedInfo($data['transpond_id'], $forApi);
		}

		// 附件处理
		if(!empty($fd['attach_id'])) {
			$data['has_attach'] = 1;
			$attach = model('Attach')->getAttachByIds($fd['attach_id']);
			foreach($attach as $ak => $av) {
				$_attach = array(
							'attach_id'   => $av['attach_id'],
							'attach_name' => $av['name'],
							'attach_url'  => getImageUrl($av['save_path'].$av['save_name']),
							'extension'   => $av['extension'],
							'size'		  => $av['size']
						);
				if($data['type'] == 'postimage') {
					$_attach['attach_small'] = getImageUrl($av['save_path'].$av['save_name'], 100, 100, true);
 					$_attach['attach_middle'] = getImageUrl($av['save_path'].$av['save_name'], 550);
				}
				$data['attach'][] = $_attach;
			}
		} else {
			$data['has_attach'] = 0;
		}

		if( $data['type'] == 'postvideo' ){
			$data['host'] = $fd['host'];
			$data['flashvar'] = $fd['flashvar'];
			$data['source'] = $fd['source'];
			$data['flashimg'] = $fd['flashimg'];
			$data['title'] = $fd['title'];
		}

		$data['feedType'] = $data['type'];
		
		// 是否收藏微博
		if($forApi) {
			$data['iscoll'] = model('Collection')->getCollection($data['feed_id'],'feed');
			if(empty($data['iscoll'])) {
				$data['iscoll']['colled'] = 0;
			} else {
				$data['iscoll']['colled'] = 1;
			}
		}

		// 微博详细信息
		$feedInfo = $this->get($id);
		$data['source_body'] = $feedInfo['body'];
		$data['api_source'] = $feedInfo['api_source'];
		//一分钟缓存
		model( 'Cache' )->set( 'feed_info_'.$id , $data , 60);
		if($forApi){
			$data['content'] = real_strip_tags($data['content']);
			unset($data['is_del'],$data['is_audit'],$data['from_data'],$data['app_row_table'],$data['app_row_id']);
			unset($data['source_body']);
		}
		return $data;			  	
	}

	/**
	 * 获取微博列表
	 * @param array $map 查询条件
	 * @param integer $limit 结果集数目，默认为10
	 * @return array 微博列表数据
	 */
	public function getList($map, $limit = 10 , $order = 'feed_id DESC') {
		$feedlist = $this->field('feed_id')->where($map)->order($order)->findPage($limit); 
		$feed_ids = getSubByKey($feedlist['data'], 'feed_id');
		$feedlist['data'] = $this->getFeeds($feed_ids);
		
		//增加答案块
		foreach( $feedlist["data"] as $v => $vv )
		{
			$AnswerWhere='feed_questionid='.$vv['feed_id'].' and uid!='.$vv['uid'].' and (uid='.$GLOBALS['ts']['mid'].' or uid in (SELECT `fid` FROM `wb_user_follow` WHERE `uid` = '.$GLOBALS['ts']['mid'].'))';
			$AnswerFeed = $this->field('feed_id')->where($AnswerWhere)->order("feed_id DESC")->findPage(1);				
			$AnswerFeed_id = getSubByKey($AnswerFeed['data'], 'feed_id');
			$AnswerFeedData = $this->getFeeds($AnswerFeed_id);
			
			$vv["answer"] = $AnswerFeedData;
			$feedlist["data"][$v]=$vv;
			
			/*print_r($vv);
			print('<br /><br /><br /><br />');*/
		}
		
		return $feedlist;
	}
	/**
	* 获取问题列表
	* $where 查询条件
	* $limit 结果集数目，默认为10
	* @return array 微博列表数据
	**/
	public function getQuestionList($where, $limit=10, $order='feed_id DESC')
	{
		$feedlist = $this->field('feed_id')->where($where)->order($order)->findPage($limit); 
		$feed_ids = getSubByKey($feedlist['data'], 'feed_id');
		$feedlist['data'] = $this->getFeeds($feed_ids, false);
		
		return $feedlist;
	}
	
	/**
		* 获取回答列表
		* $where 查询条件
		* $limit 结果集数目，默认为10
		* @return array 微博列表数据
	**/
	public function getAnswerList($where, $limit=10, $order='feed_id DESC', $newCount=0)
	{
		$feedlist = $this->field('feed_id')->where($where)->order($order)->findPage($limit); 
		$feed_ids = getSubByKey($feedlist['data'], 'feed_id');
		$feedlist['data'] = $this->getFeeds($feed_ids, false);
		
		//根据答案取问题,调换数组位置
		$index=0;
		foreach($feedlist["data"] as $v => $vv )
		{
			$questionID=Array($vv['feed_questionid']);
			$AnswerFeedData = $this->getFeeds($questionID);
			if(is_array($AnswerFeedData))
			{
				$AnswerFeedData[0]['answer'][0]=$vv;;
				if($index<$newCount){
					$AnswerFeedData[0]['newCount']='1';
					$index++;
				}
				$feedlist["data"][$v]=$AnswerFeedData[0];
			}
		}
	
		return $feedlist;
	}
	

	/**
	 * 获取指定用户所关注人的所有微博，默认为当前登录用户
	 * @param string $where 查询条件
	 * @param integer $limit 结果集数目，默认为10
	 * @param integer $uid 指定用户ID，默认为空
	 * @param integer $fgid 关组组ID，默认为空
	 * @return array 指定用户所关注人的所有微博，默认为当前登录用户
	 */
	public function getFollowingFeed($where = '', $limit = 10, $uid = '', $fgid = '', $LoadWhere = '') {
		$buid = empty($uid) ? $_SESSION['mid'] : $uid;
		//$table = "{$this->tablePrefix}feed AS a LEFT JOIN {$this->tablePrefix}user_follow AS b ON a.uid=b.fid AND b.uid = {$buid}";
		// 加上自己的信息，若不需要屏蔽下语句
		$_where = !empty($where) ? "(a.uid = '{$buid}' OR b.uid = '{$buid}') AND ($where)" : "(a.uid = '{$buid}' OR b.uid = '{$buid}')";
		//加载更多的条件
		$_LoadWhere = !empty($LoadWhere) ? "$LoadWhere" : "";
		// 若填写了关注分组
		if(!empty($fgid)) {
			$table .=" LEFT JOIN {$this->tablePrefix}user_follow_group_link AS c ON a.uid = c.fid AND c.uid ='{$buid}' ";
			$_where .= " AND c.follow_group_id = ".intval($fgid);
		}
		$table = "(select * from (";
		//关注的人和自己的问题
		$table .= "(select a.feed_id, a.publish_time from {$this->tablePrefix}feed AS a LEFT JOIN {$this->tablePrefix}user_follow AS b ON a.uid=b.fid AND b.uid = {$buid} where {$_where}) union ";
		//关注的人和自己的回答
		$table .= "(SELECT feed_questionid as feed_id, publish_time FROM {$this->tablePrefix}feed WHERE (`uid` ={$buid} or uid IN (SELECT `fid` FROM `{$this->tablePrefix}user_follow` WHERE `uid` ={$buid})) AND feed_questionid >0 GROUP BY feed_questionid) ";
		//关注人的评论
		//$table .= " union (select feed_id, ctime from (SELECT case b.feed_questionid when 0 then b.feed_id else b.feed_questionid end as feed_id,a.ctime FROM `{$this->tablePrefix}comment` a left join `{$this->tablePrefix}feed` b on a.row_id=b.feed_id WHERE a.app='public' and a.table='feed' and (a.uid = {$buid} or a.uid IN (SELECT `fid` FROM `{$this->tablePrefix}user_follow` WHERE `uid` = {$buid} ))) atab group by feed_id) ";
		
		$table .= ") tt group by feed_id) tab";
		//print($table);
		//$feedlist = $this->table($table)->where($_where)->field('a.feed_id')->order('a.publish_time DESC')->findPage($limit);
		$feedlist = $this->table($table)->where($_LoadWhere)->order('tab.feed_id DESC')->findPage($limit);
		//print($this->getLastSql());
		//print_r($feedlist);
		$feed_ids = getSubByKey($feedlist['data'], 'feed_id');
		
		//print_r($feed_ids);

		$result=$this->CreateA($feedlist,$feed_ids);
		
		return $result;
	}
	
	public function CreateA($feedlist, $feed_ids)
	{
		//查询到的数据
		$feedlist['data'] = $this->getFeeds($feed_ids);
		
		//存放结果
		$result = $feedlist;
		$result['data'] = Array();
		//增加答案块/评论块
		foreach( $feedlist["data"] as $v => $vv )
		{			
			//答案
			/*$AnswerWhere='';
			$AnswerFeed=Array();
			$AnswerFeed_id=Array();
			$AnswerFeedData=Array();
			$AnswerWhere='feed_questionid='.$vv['feed_id'].' and (uid='.$GLOBALS['ts']['mid'].' or uid in (SELECT `fid` FROM `'.$this->tablePrefix.'user_follow` WHERE `uid` = '.$GLOBALS['ts']['mid'].'))';
			$AnswerFeed = $this->field('feed_id')->where($AnswerWhere)->order("feed_id DESC")->findPage(1);				
			$AnswerFeed_id = getSubByKey($AnswerFeed['data'], 'feed_id');
			$AnswerFeedData = $this->getFeeds($AnswerFeed_id);
						
			$vv["answer"] = $AnswerFeedData;
			$feedlist["data"][$v]=$vv;*/
			
			$AnswerTable = '(SELECT max(feed_id) answer_id FROM `wb_feed` WHERE `feed_questionid` = '.$vv['feed_id'].' and (uid='.$GLOBALS['ts']['mid'].' or uid in (SELECT `fid` FROM `'.$this->tablePrefix.'user_follow` WHERE `uid` = '.$GLOBALS['ts']['mid'].')) group by uid) tt';
			$AnswerList = $this->table($AnswerTable)->order('answer_id DESC')->select();
			if(is_array($AnswerList)&&count($AnswerList)>0)
			{
				foreach( $AnswerList as $a => $aa )
				{
					//答案
					$AnswerFeed_id = Array($aa['answer_id']);
					$AnswerFeedData = $this->getFeeds($AnswerFeed_id);
					$vv["answer"] = $AnswerFeedData;
					
					/*//对答案的评论
					$CommentTable = '(SELECT max(comment_id) comment_id FROM `wb_comment` WHERE `row_id` = '.$aa['answer_id'].' and `is_del`= 0 and (uid='.$GLOBALS['ts']['mid'].' or uid in (SELECT `fid` FROM `'.$this->tablePrefix.'user_follow` WHERE `uid` = '.$GLOBALS['ts']['mid'].'))  group by uid) atab';
					$CommentList = $this->table($CommentTable)->order('comment_id DESC')->select();
					
					//对问题的评论
					$QCommentTable = '(SELECT comment_id FROM `wb_comment` WHERE `row_id` = '.$vv['feed_id'].' and `is_del`= 0 and (uid='.$GLOBALS['ts']['mid'].' or uid in (SELECT `fid` FROM `'.$this->tablePrefix.'user_follow` WHERE `uid` = '.$GLOBALS['ts']['mid'].'))  ) atab';
					$QCommentList = $this->table($QCommentTable)->order('comment_id DESC')->select();
					
					if(is_array($CommentList) && count($CommentList) > 0)
					{
						foreach($CommentList as $c => $cc )
						{
							$CommentFeedData = model('Comment')->getCommentInfo($cc['comment_id']);
							$vv["comment"] = $CommentFeedData;
							$result["data"][count($result["data"])]=$vv;
						}
					}
					else if(is_array($QCommentList) && count($QCommentList) > 0)
					{
						foreach( $QCommentList as $c => $cc )
						{
							$CommentFeedData = model('Comment')->getCommentInfo($cc['comment_id']);
							$vv["comment"] = $CommentFeedData;
							$result["data"][count($result["data"])]=$vv;
						}
					}
					else
					{
						$result["data"][count($result["data"])]=$vv;
					}*/
					
					$result["data"][count($result["data"])]=$vv;
				}
			}
			else
			{
				/*//对问题的评论
				$CommentTable = '(SELECT comment_id FROM `wb_comment` WHERE `row_id` = '.$vv['feed_id'].' and `is_del`= 0 and (uid='.$GLOBALS['ts']['mid'].' or uid in (SELECT `fid` FROM `'.$this->tablePrefix.'user_follow` WHERE `uid` = '.$GLOBALS['ts']['mid'].')) ) atab';
				$CommentList = $this->table($CommentTable)->order('comment_id DESC')->select();
				if(is_array($CommentList) && count($CommentList) > 0)
				{
					foreach( $CommentList as $c => $cc )
					{
						$CommentFeedData = model('Comment')->getCommentInfo($cc['comment_id']);
						$vv["comment"] = $CommentFeedData;
						$result["data"][count($result["data"])]=$vv;
					}
				}
				else
				{
					$result["data"][count($result["data"])]=$vv;
				}*/
				$result["data"][count($result["data"])]=$vv;
			}
			
			/*print_r($result["data"]);
			print_r($vv);
			print('<br /><br /><br /><br />');*/
			
			
			//评论
			/*$CommentWhere='';
			$CommentFeed=Array();
						
			$CommentWhere='row_id='.$vv['feed_id'].' and (uid='.$GLOBALS['ts']['mid'].' or uid in (SELECT `fid` FROM `'.$this->tablePrefix.'user_follow` WHERE `uid` = '.$GLOBALS['ts']['mid'].'))';
			$CommentFeed = model('Comment')->field('comment_id')->where($CommentWhere)->order("comment_id DESC")->findPage(1);	
			if(is_array($CommentFeed['data']) && count($CommentFeed['data']) > 0)
			{
				$CommentFeedData = model('Comment')->getCommentInfo($CommentFeed['data'][0]['comment_id']);
				//print_r($CommonFeedData);
				$vv["comment"] = $CommentFeedData;
				$feedlist["data"][$v]=$vv;
				//print_r($vv);
			}*/
			
		}
		
		return $result;
	}
	
	/**
	 * 获取评论答案的总列表(包含问题,答案,评论)
	 *
	 * @param mixed $where This is a description
	 * @param mixed $limit This is a description
	 * @param mixed $uid This is a description
	 * @param mixed $LoadWhere This is a description
	 * @return mixed This is the return value description
	 *
	 */	
	public function getCommentFeedList($where = '', $limit = 10, $LoadWhere = '')
	{
		//加载更多的条件
		$_LoadWhere = !empty($LoadWhere) ? "$LoadWhere" : "";
		
		$table = "(SELECT `feed_id`,`comment_count` FROM `wb_feed` WHERE $where ) t";
		//print($table);
		$feedlist = $this->table($table)->where($_LoadWhere)->order('t.comment_count DESC')->findPage($limit);
		//print($this->getLastSql());
		//增加答案块和问题块
		if(is_array($feedlist["data"])&&count($feedlist["data"])>0)
		{
			foreach($feedlist["data"] as $v => $vv )
			{	
				//$Comment = model('Comment')->getCommentInfo($vv['comment_id']);
				$Answer = $this->getFeeds(array($vv['feed_id']), false);
				$Question = $this->getFeeds(array($Answer[0]['feed_questionid']), false);
				$vv=$Question;
				$vv[0]['answer']=$Answer;
				//$vv[0]['comment']=$Comment;
				$feedlist["data"][$v]=$vv[0];
			}
		}
		/*print_r($feedlist);*/
		return $feedlist;
	}
	
	
	public function getNewCommentFeedListByType($map, $limit = 10, $order='', $newCount=0)
	{
		$commentList = model('Comment')->getCommentList($map, $order, $limit);
		if(is_array($commentList["data"])&&count($commentList["data"])>0)
		{
			$index = 0;
			foreach($commentList["data"] as $v => $vv )
			{	
				//$Comment = model('Comment')->getCommentInfo($vv['comment_id']);
				$Answer = $this->getFeeds(array($vv['row_id']), false);
				$Question = $this->getFeeds(array($Answer[0]['feed_questionid']), false);
				$comment=$vv;
				$vv=$Question;
				$vv[0]['answer']=$Answer;
				$vv[0]['comment']=$comment;
				if($index<$newCount){
					$vv[0]['newCommentCount']='1';
					$index++;
				}
				//$vv[0]['comment']=$Comment;
				$commentList["data"][$v]=$vv[0];
			}
		}
		return $commentList;
	}
	
	public function getNewCommentFeedList($map, $limit = 10, $order='', $newCount=0)
	{
		$commentList = model('Comment')->getCommentList($map, $order, $limit);
		if(is_array($commentList["data"])&&count($commentList["data"])>0)
		{
			$index = 0;
			foreach($commentList["data"] as $v => $vv )
			{	
				$Question = $this->getFeeds(array($vv['row_id']), false);
				$comment=$vv;
				$vv=$Question;
				$vv[0]['comment']=$comment;
				if($index<$newCount){
					$vv[0]['newCommentCount']='1';
					$index++;
				}
				$commentList["data"][$v]=$vv[0];
			}
		}
		return $commentList;
	}
	
	/**
	* 获取评论答案的问题列表(问题,答案)
	*
	* @param mixed $where This is a description
	* @param mixed $limit This is a description
	* @param mixed $uid This is a description
	* @param mixed $LoadWhere This is a description
	* @return mixed This is the return value description
	*
	*/	
	public function getFeedListByComment($where = '', $limit = 10, $LoadWhere = '')
	{
		//加载更多的条件
		$_LoadWhere = !empty($LoadWhere) ? "$LoadWhere" : "";
		
		$table = "(SELECT `feed_id`,`comment_count` FROM `wb_feed` WHERE $where ) t";
		//print($table);
		$feedlist = $this->table($table)->where($_LoadWhere)->order('t.comment_count DESC')->findPage($limit);
		//print($this->getLastSql());
		//print('<br /><br /><br /><br />');
		//print_r($feedlist);
		//增加问题块
		foreach($feedlist["data"] as $v => $vv )
		{	
			$Answer = $this->getFeeds(array($vv['feed_id']), false);
			$Question = $this->getFeeds(array($Answer[0]['feed_questionid']), false);
			$vv=$Question;
			$vv[0]['answer']=$Answer;
			$feedlist["data"][$v]=$vv[0];
		}
		
		/*print_r($feedlist);*/
		return $feedlist;
	}
	

	/**
	 * 获取指定用户收藏的微博列表，默认为当前登录用户
	 * @param array $map 查询条件
	 * @param integer $limit 结果集数目，默认为10
	 * @param integer $uid 指定用户ID，默认为空
	 * @return array 指定用户收藏的微博列表，默认为当前登录用户
	 */
	public function getCollectionFeed($map, $limit = 10, $uid = '') {
		$map['uid'] = empty($uid) ? $_SESSION['mid'] : $uid;
		$map['source_table_name'] = 'feed';
		$table = "{$this->tablePrefix}collection";
		$feedlist = $this->table($table)->where($map)->field('source_id AS feed_id')->order('source_id DESC')->findPage($limit);
		$feed_ids = getSubByKey($feedlist['data'],'feed_id');
		$feedlist['data'] = $this->getFeeds($feed_ids);

		return $feedlist;
	}

	/**
	 * 获取指定用户所关注人的微博列表
	 * @param array $map 查询条件
	 * @param integer $uid 用户ID
	 * @param string $app 应用名称
	 * @param integer $type 应用类型
	 * @param integer $limit 结果集数目，默认为10
	 * @return array 指定用户所关注人的微博列表
	 */
	public function getFollowingList($map,$uid, $app, $type, $limit = 10) {
		// 读取列表
		$map['_string'] = "uid IN (SELECT fid FROM {$this->tablePrefix}user_follow WHERE uid={$uid}) OR uid={$uid}";
		!empty($app) && $map['app'] = $app;
		!empty($type) && $map['type'] = $type;
		if ( $map['type'] == 'post' ){
			unset($map['type']);
			$map['is_repost'] = 0;
		}
		$feedlist = $this->field('feed_id')->where($map)->order("publish_time DESC")->findPage($limit);
		if(!$feedlist) {
			$this->error = L('PUBLIC_INFO_GET_FAIL');			// 获取信息失败
			return false;
		}
		$feed_ids = getSubByKey($feedlist['data'], 'feed_id');
		
		$feedlist['data'] = $this->getFeeds($feed_ids);

		return $feedlist;
	}

	/**
	 * 查看指定用户的微博列表
	 * @param array $map 查询条件
	 * @param integer $uid 用户ID
	 * @param string $app 应用类型
	 * @param string $type 微博类型
	 * @param integer $limit 结果集数目，默认为10
	 * @return array 指定用户的微博列表数据
	 */
	public function getUserList($map, $uid, $app, $type, $limit = 10) {
		if(!$uid) {
			$this->error = L('PUBLIC_WRONG_DATA');				// 获取信息失败
			return false;
		}
		!empty($app) && $map['app'] = $app;
		!empty($type) && $map['type'] = $type;
		if( $map['type'] == 'post' ){
			unset($map['type']);
			$map['is_repost'] = 0;
		}
		$map['uid'] = $uid;
		$list = $this->getList($map, $limit); 

		return $list;
	}

	/**
	 * 获取指定用户的最后一条微博数据
	 * @param array $uids 用户ID
	 * @return array 指定用户的最后一条微博数据
	 */
	public function  getLastFeed($uids) {
		if(empty($uids)) {
			return false;
		}

		!is_array($uids) && $uids = explode(',', $uids);
		$map['uid'] = array('IN', $uids);
		$map['is_del'] = 0;
		$feeds = $this->where($map)->field('MAX(feed_id) AS feed_id,uid')->group('uid')->getAsFieldArray('feed_id');
		$feedlist = $this->getFeeds($feeds);
		$r = array();
		foreach($feedlist as $v) {
			if(!empty($v['sourceInfo'])) {
				$r[$v['uid']] = array('feed_id'=>$v['feed_id'],'title'=>getShort(t($v['sourceInfo']['shareHtml']), 30, '...'));
			} else {
				$t = unserialize($v['feed_data']);
				$r[$v['uid']] = array('feed_id'=>$v['feed_id'],'title'=>getShort(t($t['body']), 30, '...'));
			}
		}

		return $r;
	}

	/**
	 * 获取给定微博ID的微博信息
	 * @param array $feed_ids 微博ID数组
	 * @return array 给定微博ID的微博信息
	 */
	public function getFeeds($feed_ids, $isfilter=true) {
		$feedlist = array();
		if($isfilter)
			$feed_ids = array_filter(array_unique($feed_ids));
		
		// 获取数据
		if(count($feed_ids) > 0) {
			$cacheList = model('Cache')->getList('fd_', $feed_ids);
		} else {
			return false;
		}
		// 按照传入ID顺序进行排序
		foreach($feed_ids as $key => $v) {
			if($cacheList[$v]) {
				$feedlist[$key] = $cacheList[$v]; 
			} else {
				$feed = $this->setFeedCache(array(), $v);
				$feedlist[$key] = $feed[$v];
			}
		}
		return $feedlist;
	}

	/**
	 * 清除指定用户指定微博的列表缓存
	 * @param array $feed_ids 微博ID数组，默认为空
	 * @param integer $uid 用户ID，默认为空
	 * @return void
	 */
	public function cleanCache($feed_ids = array(), $uid = '') {
		if(!empty($uid)) {
			model('Cache')->rm('fd_foli_'.$uid);
			model('Cache')->rm('fd_uli_'.$uid);
		}
		if(empty($feed_ids)) {
			return true;
		}
		if(is_array($feed_ids)) {
			foreach($feed_ids as $v) {
				model('Cache')->rm('fd_'.$v);
				model('Cache')->rm('feed_info_'.$v);
			}
		} else {
			model('Cache')->rm('fd_'.$feed_ids);
			model('Cache')->rm('feed_info_'.$feed_ids);
		}
	}

	/**
	 * 更新指定微博的缓存
	 * @param array $feed_ids 微博ID数组，默认为空
	 * @param string $type 操作类型，默认为update
	 * @return bool true
	 */
	public function updateFeedCache($feed_ids, $type = 'update') {
		if($type == 'update') {
			$this->getFeeds($feed_ids);
		} else {
			foreach($feed_ids as $v) {
				model('Cache')->rm('fd_'.$v);
			}
		}

		return true;
	}

	/**
	 * 生成指定微博的缓存
	 * @param array $value 微博相关数据
	 * @param array $feed_id 微博ID数组
	 */
	private function setFeedCache($value = array(), $feed_id = array()) {
		if(!empty($feed_id)) {
			!is_array($feed_id) && $feed_id = explode(',', $feed_id);
			$map['a.feed_id'] = array('IN', $feed_id);
			$list = $this->where($map)
						 ->field('a.*,b.client_ip,b.feed_data')
						 ->table("{$this->tablePrefix}feed AS a LEFT JOIN {$this->tablePrefix}feed_data AS b ON a.feed_id = b.feed_id")
						 ->findAll();

			$r = array();
			foreach($list as &$v) {
				// 格式化数据模板
				$parseData = $this->__paseTemplate($v);
				$v['info'] = $parseData['info'];
				$v['title'] = $parseData['title'];
				$v['body'] = $parseData['body'];
				$v['description'] = $parseData['description'];
				//$v['answer_count'] = $parseData['answer_count'];
				$v['api_source'] = $parseData['api_source'];
				$v['actions'] = $parseData['actions'];
				$v['user_info'] = $parseData['userInfo'];
				$v['GroupData'] = model('UserGroupLink')->getUserGroupData($v['uid']);
 				model('Cache')->set('fd_'.$v['feed_id'], $v);			// 1分钟缓存
				$r[$v['feed_id']] = $v;
			}

			return $r;
		} else {
			// 格式化数据模板
			$parseData = $this->__paseTemplate($value);
			$value['info'] = $parseData['info'];
			$value['title'] = $parseData['title'];
			$value['body'] = $parseData['body'];
			$value['description'] = $parseData['description'];
			$value['api_source'] = $parseData['api_source'];
			//$value['answer_count'] = $parseData['answer_count'];
			$value['actions'] = $parseData['actions'];
			$value['user_info'] = $parseData['userInfo'];
			$value['GroupData'] = model('UserGroupLink')->getUserGroupData($value['uid']);
 			model('Cache')->set('fd_'.$value['feed_id'], $value);		// 1分钟缓存
			return $value;
		}
	}

	/**
	 * 解析微博模板标签
	 * @param array $_data 微博的原始数据
	 * @return array 解析微博模板后的微博数据
	 */
	private function __paseTemplate($_data) {
		// 获取作者信息
		$user = model('User')->getUserInfo($_data['uid']);
		// 处理数据
		$_data['data'] = unserialize($_data['feed_data']);
		// 模版变量赋值
		$var = $_data['data'];
		if(!empty($var['attach_id'])) {
			$var['attachInfo'] = model('Attach')->getAttachByIds($var['attach_id']);
			foreach($var['attachInfo'] as $ak => $av) {
				$_attach = array(
							'attach_id'   => $av['attach_id'],
							'attach_name' => $av['name'],
							'attach_url'  => getImageUrl($av['save_path'].$av['save_name']),
							'extension'   => $av['extension'],
							'size'		  => $av['size']
						);
				if($_data['type'] == 'postimage') {
					$_attach['attach_small'] = getImageUrl($av['save_path'].$av['save_name'], 100, 100, true);
 					$_attach['attach_middle'] = getImageUrl($av['save_path'].$av['save_name'], 550);
				}
				$var['attachInfo'][$ak] = $_attach;
			}
		}
		if ( $_data['type'] == 'postvideo' && !$var['flashimg']){
			$var['flashimg'] = '__THEME__/image/video.png';
		}
		$var['uid'] = $_data['uid'];
		$var["actor"] = "<a href='{$user['space_url']}' class='name' event-node='face_card' uid='{$user['uid']}'>{$user['uname']}</a>";
		$var["actor_uid"] = $user['uid'];
		$var["actor_uname"]	= $user['uname'];
		$var['feedid'] = $_data['feed_id'];   
		//微吧类型微博用到
		// $var["actor_groupData"] = model('UserGroupLink')->getUserGroupData($user['uid']);
		//需要获取资源信息的微博：所有类型的微博，只要有资源信息就获取资源信息并赋值模版变量，交给模版解析处理
		if(!empty($_data['app_row_id'])) {
			empty($_data['app_row_table']) && $_data['app_row_table'] = 'feed';
			$var['sourceInfo'] = model('Source')->getSourceInfo($_data['app_row_table'], $_data['app_row_id'], false, $_data['app']);
			$var['sourceInfo']['groupData'] = model('UserGroupLink')->getUserGroupData($var['sourceInfo']['source_user_info']['uid']);
		}
		// 解析Feed模版
		$feed_template_file = APPS_PATH.'/'.$_data['app'].'/Conf/'.$_data['type'].'.feed.php';
		if(!file_exists($feed_template_file)){
			$feed_template_file = APPS_PATH.'/public/Conf/post.feed.php';
		}
		$feed_xml_content = fetch($feed_template_file, $var);
		$s = simplexml_load_string($feed_xml_content);
		if(!$s){
			return false;
		}
		$result = $s->xpath("//feed[@type='".t($_data['type'])."']");
		$actions = (array) $result[0]->feedAttr;
		//输出模版解析后信息
		$return["userInfo"]  = $user;
		$return["actor_groupData"] = $var["actor_groupData"];
		$return['title'] = trim((string) $result[0]->title);
	    $return['body'] =  trim((string) $result[0]->body);
		$return['description'] =  trim((string) $result[0]->description);
		// $return['sbody'] = trim((string) $result[0]->sbody);
	    $return['info'] =  trim((string) $result[0]['info']);
	    //$return['title'] =  parse_html($return['title']); 
	    $return['body']  =  parse_html($return['body']);
		$return['description']  =  parse_html($return['description']);
	    $return['api_source'] = $var['sourceInfo'];
		// $return['sbody'] =  parse_html($return['sbody']); 
	    $return['actions'] = $actions['@attributes'];
	    //验证转发的原信息是否存在
	    if(!$this->_notDel($_data['app'],$_data['type'],$_data['app_row_id'])) {
	    	$return['body'] = L('PUBLIC_INFO_ALREADY_DELETE_TIPS');				// 此信息已被删除〜
	    }
		return $return;
	}

	/**
	 * 判断资源是否已被删除
	 * @param string $app 应用名称
	 * @param string $feedtype 动态类型
	 * @param integer $app_row_id 资源ID
	 * @return boolean 资源是否存在
	 */
	private function _notDel($app, $feedtype, $app_row_id) {
		// TODO:该方法为完成？
		// 非转发的内容，不需要验证
		if(empty($app_row_id)){
			return true;
		}
		return true;
	}

	/**
	 * 获取所有微博节点列表 - 预留后台查看、编辑微博模板文件
	 * @param boolean $ignore 从微博设置里面获取，默认为false
	 * @return array 所有微博节点列表
	 */
	public function getNodeList($ignore = false) {
		if(false===($feedNodeList=S('FeedNodeList'))){
			//应用列表
			$apps = C('DEFAULT_APPS');
			$appList = model('App')->getAppList();
			foreach($appList as $app){
				$apps[] = $app['app_name'];
			}
	  		//获得所有feed配置文件
	        require_once ADDON_PATH.'/library/io/Dir.class.php';
	        $dirs = new Dir(SITE_PATH,'*.feed.php');
	        foreach($apps as $app){
	        	$app_config_path = SITE_PATH.'/apps/'.$app.'/Conf/';
	        	$dirs->listFile($app_config_path,'*.feed.php');
	        	$files = $dirs->toArray();        	
	        	if(is_array($files) && count($files)>0){
	        		foreach($files as $file){
	        			$feed_file['app'] = $app;
	        			$feed_file['filename']= $file['filename'];
	        			$feed_file['pathname']= $file['pathname'];
	        			$feed_file['mtime']= $file['mtime'];
	        			$feedNodeList[] = $feed_file;
	        		}
	        	}
	        }
	        S('FeedNodeList',$feedNodeList);
        }
        return $feedNodeList;
        // $xml = simplexml_load_file( $this->_getFeedXml() );
		// $feed = $xml->feedlist->feed;
		// $list = array();
		// foreach($feed as $key => $v) {
		// 	$app = (string)$v['app'];
		// 	$type = (string)$v['type'];
		// 	$list[$app][] = array(
		// 		'app'=>$app,
		// 		'type'=>$type,
		// 		'info'=>(string)$v['info']
		// 	);
		// }
		// return $list;
	}

	/**
	 * 获取微博模板的XML文件路径
	 * @param boolean $set 是否重新生成微博模板XML文件
	 * @return string 微博模板的XML文件路径
	 */
	public function _getFeedXml($set = false) {
		if($set || !file_exists(SITE_PATH.'/config/feeds.xml')) {
			$data = D('feed_node')->findAll();
			$xml = "<?xml version='1.0' encoding='UTF-8'?>
					<root>
					<feedlist>";
			foreach($data as $v) {
				$xml.="
				<feed app='{$v['appname']}' type='{$v['nodetype']}' info='{$v['nodeinfo']}'>
				".htmlspecialchars_decode($v['xml'])."
				</feed>";
			}
			$xml .= "</feedlist>
					</root>";
				
			file_put_contents(SITE_PATH.'/config/feeds.xml', $xml);
			chmod(SITE_PATH.'/config/feeds.xml', 0666);
		}
		
		return SITE_PATH.'/config/feeds.xml';
	}

	/**
	 * 微博操作，彻底删除、假删除、回复
	 * @param integer $feed_id 微博ID
	 * @param string $type 微博操作类型，deleteFeed：彻底删除，delFeed：假删除，feedRecover：恢复
	 * @param string $title 日志内容，目前没没有该功能
	 * @param string $uid 删除微博的用户ID（区别超级管理员）
	 * @return array 微博操作后的结果信息数组
	 */
	public function doEditFeed($feed_id, $type, $title ,$uid = null) {
		$return = array('status'=>'0');
		if(empty($feed_id)) {
			//$return['data'] = '微博ID不能为空！';
		} else {
			$map['feed_id'] = is_array($feed_id) ? array('IN', $feed_id) : intval($feed_id);
			$save['is_del'] = $type =='delFeed' ? 1 : 0;

			if($type == 'deleteFeed') {
				$feedArr = is_array($feed_id) ? $feed_id : explode(',', $feed_id);
				// 取消微博收藏
				foreach ($feedArr as $sid) {
					$feed = $this->where('feed_id='.$sid)->find();
					model('Collection')->delCollection($sid, 'feed', $feed['uid']);
				}
				// 彻底删除微博
				$res = $this->where($map)->delete();
				// 删除微博相关信息
				if($res) {
					$this->_deleteFeedAttach($feed_id, 'deleteAttach');
				}
			} else {
				$ids = !is_array($feed_id) ? array($feed_id) : $feed_id;
				$feedList = $this->getFeeds($ids);
				$res = $this->where($map)->save($save);
				if($type == 'feedRecover'){
					// 添加微博数
					foreach($feedList as $v) {
						model('UserData')->setUid($v['user_info']['uid'])->updateKey('feed_count', 1);
						model('UserData')->setUid($v['user_info']['uid'])->updateKey('weibo_count', 1);
					}
					$this->_deleteFeedAttach($ids, 'recoverAttach');
				} else {
					// 减少微博数
					foreach($feedList as $v) {
						model('UserData')->setUid($v['user_info']['uid'])->updateKey('feed_count', -1);
						model('UserData')->setUid($v['user_info']['uid'])->updateKey('weibo_count', -1);
					}
					$this->_deleteFeedAttach($ids, 'delAttach');
					// 删除频道相应微博
					$channelMap['feed_id'] = array('IN', $ids);
					D('channel')->where($channelMap)->delete();
				}
				$this->cleanCache($ids); 		// 删除微博缓存信息
				// 资源微博缓存相关微博
				$sids = $this->where('app_row_id='.$feed_id)->getAsFieldArray('feed_id');
				$this->cleanCache($sids);
			}
			
			//如果是回答,减少问题的回答总数
			$ids = !is_array($feed_id) ? array($feed_id) : $feed_id;
			$feedList = $this->getFeeds($ids);
			foreach($feedList as $v) {
				if($v['feed_questionid']>0)
				{
					$feed_Qid = $v['feed_questionid'];
					$updData['answer_count']=$this->where('feed_questionid='.$feed_Qid.' and is_del=0')->count();
					$this->where('feed_id='.$feed_Qid)->save($updData);
					$this->	cleanCache(array($feed_Qid));
					$this->	updateFeedCache($feed_Qid,'update');
				}
			}
			
			// 删除评论信息
			$cmap['app'] = 'Public';
			$cmap['table'] = 'feed';
			$cmap['row_id'] = is_array($feed_id) ? array('IN', $feed_id) : intval($feed_id);
			$commentIds = model('Comment')->where($cmap)->getAsFieldArray('comment_id');
			model('Comment')->setAppName('Public')->setAppTable('feed')->deleteComment($commentIds);
			if($res) {
				// TODO:是否记录日志，以及后期缓存处理
				$return = array('status'=>1);
				//添加积分
				model('Credit')->setUserCredit($uid,'delete_weibo');
			}
		}

		return $return;
	}

	/**
	 * 删除微博相关附件数据
	 * @param array $feedIds 微博ID数组
	 * @param string $type 删除附件类型
	 * @return void
	 */
	private function _deleteFeedAttach($feedIds, $type)
	{
		// 查询微博内是否存在附件
		$feeddata = $this->getFeeds($feedIds);
		$feedDataInfo = getSubByKey($feeddata, 'feed_data');
		$attachIds = array();
		foreach($feedDataInfo as $value) {
			$value = unserialize($value);
			!empty($value['attach_id']) && $attachIds = array_merge($attachIds, $value['attach_id']);
		}
		array_filter($attachIds);
		array_unique($attachIds);
		!empty($attachIds) && model('Attach')->doEditAttach($attachIds, $type, '');
	}

	/**
	 * 审核通过微博
	 * @param integer $feed_id 微博ID
	 * @return array 微博操作后的结果信息数组
	 */
	public function doAuditFeed($feed_id){
		$return = array('status'=>'0');
		if(empty($feed_id)) {
			$return['data'] = '请选择微博！';
		} else {
			$map['feed_id'] = is_array($feed_id) ? array('IN', $feed_id) : intval($feed_id);
			$save['is_audit'] = 1;
			$res = $this->where($map)->save($save);
			if($res) {
				$return = array('status'=>1);
			}
			
			//更新缓存
			$this->cleanCache($feed_id);
		}	
		return $return;
	}
	
	/*** 搜索引擎使用 ***/
	/**
	 * 搜索微博
	 * @param string $key 关键字
	 * @param string $type 搜索类型，following、all、space
	 * @param integer $loadId 载入微博ID，从此微博ID开始搜索
	 * @param integer $limit 结果集数目
	 * @param boolean $forApi 是否返回API数据，默认为false
	 * @return array 搜索后的微博数据
	 */
	public function searchFeed($key, $type, $loadId, $limit, $forApi = false,$feed_type) {
		switch($type){
			case 'following':
				$buid = $GLOBALS['ts']['uid'];
				$table = "{$this->tablePrefix}feed AS a LEFT JOIN {$this->tablePrefix}user_follow AS b ON a.uid=b.fid AND b.uid = {$buid} LEFT JOIN {$this->tablePrefix}feed_data AS c ON a.feed_id = c.feed_id";
				$where = !empty($loadId) ? " a.is_del = 0 AND a.is_audit = 1 AND a.feed_id <'{$loadId}'" : "a.is_del = 0 AND a.is_audit = 1";
				$where .= " AND (a.uid = '{$buid}' OR b.uid = '{$buid}' )";
				$where .= " AND c.feed_data LIKE '%".t($key)."%'";
				$feedlist = $this->table($table)->where($where)->field('a.feed_id')->order('a.publish_time DESC')->findPage($limit);
				break;
			case 'all':
				$map['a.is_del'] = 0;
				$map['a.is_audit'] = 1;
				!empty($loadId) && $map['a.feed_id'] = array('LT', intval($loadId));
				$map['b.feed_content'] = array('LIKE', '%'.t($key).'%');
				if($feed_type){
					$map['a.type'] = $feed_type;
					if ( $map['a.type'] == 'post' ){
						unset($map['a.type']);
						$map['a.is_repost'] = 0;
					}
				}
				$table = "{$this->tablePrefix}feed AS a LEFT JOIN {$this->tablePrefix}feed_data AS b ON a.feed_id = b.feed_id";
				$feedlist = $this->table($table)->field('a.feed_id')->where($map)->order('a.publish_time DESC')->findPage($limit);
				break;
			case 'space':
				$map['a.is_del'] = 0;
				$map['a.is_audit'] = 1;
				!empty($loadId) && $map['a.feed_id'] = array('LT', intval($loadId));
				$map['b.feed_content'] = array('LIKE', '%'.t($key).'%');
				if($feed_type){
					$map['a.type'] = $feed_type;
					if ( $map['a.type'] == 'post' ){
						unset($map['a.type']);
						$map['a.is_repost'] = 0;
					}
				}
				$map['a.uid'] = $GLOBALS['ts']['uid'];
				$table = " {$this->tablePrefix}feed AS a LEFT JOIN {$this->tablePrefix}feed_data AS b ON a.feed_id = b.feed_id";
				$feedlist = $this->table($table)->field('a.feed_id')->where($map)->order('a.publish_time DESC')->findPage($limit);
				break;	
			case 'topic':
				$map['a.is_del'] = 0;
				$map['a.is_audit'] = 1;
				!empty($loadId) && $map['a.feed_id'] = array('LT', intval($loadId));
				$map['b.feed_content'] = array('LIKE', '%#'.t($key).'#%');
				if($feed_type){
					$map['a.type'] = $feed_type;
					if ( $map['a.type'] == 'post' ){
						unset($map['a.type']);
						$map['a.is_repost'] = 0;
					}
				}
				$table = "{$this->tablePrefix}feed AS a LEFT JOIN {$this->tablePrefix}feed_data AS b ON a.feed_id = b.feed_id";
				$feedlist = $this->table($table)->field('a.feed_id')->where($map)->order('a.publish_time DESC')->findPage($limit);
				break;
		}
		$feed_ids = getSubByKey($feedlist['data'], 'feed_id');
		if($forApi) {
			return $this->formatFeed($feed_ids, true);
		}
		$feedlist['data'] = $this->getFeeds($feed_ids);

		return $feedlist;
	}

	/**
	 * 数据库搜索微博
	 * @param string $key 关键字
	 * @param string $type 微博类型，post、repost、postimage、postfile
	 * @param integer $limit 结果集数目
	 * @param boolean $forApi 是否返回API数据，默认为false
	 * @return array 搜索后的微博数据
	 */
	public function searchFeeds($key, $feed_type, $limit, $Stime, $Etime) {	
		$map['a.is_del'] = 0;
		$map['a.is_audit'] = 1;
		$map['b.feed_content'] = array('LIKE', '%'.t($key).'%');
		if($feed_type){
			$map['a.type'] = $feed_type;
			if ( $map['a.type'] == 'post' ){
				unset($map['a.type']);
				$map['a.is_repost'] = 0;
			}
		}
		if($Stime && $Etime){
			$map['a.publish_time'] = array('between',array($Stime,$Etime));
		}
		$table = "{$this->tablePrefix}feed AS a LEFT JOIN {$this->tablePrefix}feed_data AS b ON a.feed_id = b.feed_id";
		$feedlist = $this->table($table)->field('a.feed_id')->where($map)->order('a.publish_time DESC')->findPage($limit);
		//return D()->getLastSql();exit;
		$feed_ids = getSubByKey($feedlist['data'], 'feed_id');
		$feedlist['data'] = $this->getFeeds($feed_ids);
		foreach($feedlist['data'] as &$v) {
        	switch ( $v['app'] ){
        		case 'weiba':
        			$v['from'] = getFromClient(0 , $v['app'] , '微吧');
        			break;
        		default:
        			$v['from'] = getFromClient( $v['from'] , $v['app']);
        			break;
        	}
        	!isset($uids[$v['uid']]) && $v['uid'] != $GLOBALS['ts']['mid'] && $uids[] = $v['uid'];
        }
		return $feedlist;
	}

	/*** API使用 ***/
	/**
	 * 获取全站最新的微博
	 * @param string $type 微博类型,原创post,转发repost,图片postimage,附件postfile,视频postvideo
	 * @param integer $since_id 微博ID，从此微博ID开始，默认为0
	 * @param integer $max_id 最大微博ID，默认为0
	 * @param integer $limit 结果集数目，默认为20
	 * @param integer $page 分页数，默认为1
	 * @return array 全站最新的微博
	 */
	public function public_timeline($type, $since_id = 0, $max_id = 0 ,$limit = 20 ,$page = 1) {
		$since_id = intval($since_id);
		$max_id = intval($max_id);
		$limit = intval($limit);
		$page = intval($page); 
		$where = " is_del = 0 ";
		//动态类型
		if(in_array($type,array('post','repost','postimage','postfile','postvideo'))){
			$where .= " AND type='$type' ";
		}
		if(!empty($since_id) || !empty($max_id)) {
			!empty($since_id) && $where .= " AND feed_id > {$since_id}";
			!empty($max_id) && $where .= " AND feed_id < {$max_id}";
		}
		$start = ($page - 1) * $limit;
		$end = $limit;
		$feed_ids = $this->where($where)->field('feed_id')->limit("{$start},{$end}")->order('feed_id DESC')->getAsFieldArray('feed_id');
		return $this->formatFeed($feed_ids,true);
	}

	/**
	 * 获取登录用户所关注人的最新微博
	 * @param string $type 微博类型,原创post,转发repost,图片postimage,附件postfile,视频postvideo
	 * @param integer $mid 用户ID
	 * @param integer $since_id 微博ID，从此微博ID开始，默认为0
	 * @param integer $max_id 最大微博ID，默认为0
	 * @param integer $limit 结果集数目，默认为20
	 * @param integer $page 分页数，默认为1
	 * @return array 登录用户所关注人的最新微博
	 */
	public function friends_timeline($type, $mid, $since_id = 0, $max_id = 0, $limit = 20, $page = 1) {
		$since_id = intval($since_id);
		$max_id = intval($max_id);
		$limit = intval($limit);
		$page = intval($page); 
		$where = " a.is_del = 0 ";
		//动态类型
		if(in_array($type,array('post','repost','postimage','postfile','postvideo'))){
			$where .= " AND a.type='$type' ";
		}
		if(!empty($since_id) || !empty($max_id)) {
			!empty($since_id) && $where .= " AND a.feed_id > {$since_id}";
			!empty($max_id) && $where .= " AND a.feed_id < {$max_id}";
		}
		$start = ($page - 1) * $limit;
		$end = $limit;
		$table = "{$this->tablePrefix}feed AS a LEFT JOIN {$this->tablePrefix}user_follow AS b ON a.uid=b.fid AND b.uid = {$mid}";
		// 加上自己的信息，若不需要此数据，请屏蔽下面语句
		$where = "(a.uid = '{$mid}' OR b.uid = '{$mid}') AND ($where)";
		$feed_ids = $this->where($where)->table($table)->field('a.feed_id')->limit("{$start},{$end}")->order('a.feed_id DESC')->getAsFieldArray('feed_id');
		
		return $this->formatFeed($feed_ids, true);
	}

	/**
	 * 获取指定用户发布的微博列表
	 * @param string $type 微博类型,原创post,转发repost,图片postimage,附件postfile,视频postvideo
	 * @param integer $user_id 指定用户ID
	 * @param string $user_name 指定用户名称
	 * @param integer $since_id 微博ID，从此微博ID开始，默认为0
	 * @param integer $max_id 最大微博ID，默认为0
	 * @param integer $limit 结果集数目，默认为20
	 * @param integer $page 分页数，默认为1
	 * @return array 指定用户发布的微博列表
	 */
	public function user_timeline($type, $user_id , $user_name , $since_id = 0 , $max_id = 0 , $limit = 20 , $page = 1) {
		if(empty($user_id) && empty($user_name)) {
			return array();
		}
		if(empty($user_id)) {
			$user_info = model('User')->getUserInfoByName($user_name);
			$user_id = $user_info['uid'];
		}
		if(empty($user_id)) {
			return array();
		}
		$where = "uid = '{$user_id}' AND is_del = 0 ";
		//动态类型
		if(in_array($type,array('post','repost','postimage','postfile','postvideo'))){
			$where .= " AND type='$type' ";
		}
		$since_id = intval($since_id);
		$max_id = intval($max_id);
		$limit = intval($limit);
		$page = intval($page); 

		if(!empty($since_id) || !empty($max_id)) {
			!empty($since_id) && $where .= " AND feed_id > {$since_id}";
			!empty($max_id) && $where .= " AND feed_id < {$max_id}";
		}
		$start = ($page - 1) * $limit;
		$end = $limit;

		$feed_ids = $this->field('feed_id')->where($where)->field('feed_id')->limit("{$start},{$end}")->order('publish_time DESC')->getAsFieldArray('feed_id'); 
		
		return $this->formatFeed($feed_ids, true);
	}

	/**
	 * 获取某条微博的被转发列表
	 * @param string $row_id 被转发微博ID
	 * @param integer $since_id 微博ID，从此微博ID开始，默认为0
	 * @param integer $max_id 最大微博ID，默认为0
	 * @param integer $limit 结果集数目，默认为20
	 * @param integer $page 分页数，默认为1
	 * @return array 全站最新的微博
	 */
	public function repost_timeline($row_id, $since_id = 0, $max_id = 0 ,$limit = 20 ,$page = 1) {
		$row_id = intval($row_id);
		$since_id = intval($since_id);
		$max_id = intval($max_id);
		$limit = intval($limit);
		$page = intval($page); 
		if($row_id<=0){
			return false;
		}

		$where = " is_del = 0 AND type='repost' AND app_row_id=$row_id ";
		if(!empty($since_id) || !empty($max_id)) {
			!empty($since_id) && $where .= " AND feed_id > {$since_id}";
			!empty($max_id) && $where .= " AND feed_id < {$max_id}";
		}
		$start = ($page - 1) * $limit;
		$end = $limit;
		$feed_ids = $this->where($where)->field('feed_id')->limit("{$start},{$end}")->order('feed_id DESC')->getAsFieldArray('feed_id');
		return $this->formatFeed($feed_ids,true);
	}

	/**
	 * 格式化微博数据
	 * @param array $feed_ids 微博ID数组
	 * @param boolean $forApi 是否为API数据，默认为false
	 * @return array 格式化后的微博数据
	 */
	public function formatFeed($feed_ids, $forApi = false) {
		if(empty($feed_ids)) {
			return array();
		} else {
			if(count($feed_ids) > 0 ) {
			 	$r = array();
				foreach($feed_ids as $feed_id) {
					$r[] = $this->getFeedInfo($feed_id, $forApi); 
				}
				return $r;
			} else {
				return array();
			}	
		}
	}

	/**
	 * 同步到微博
	 * @param string content 内容
	 * @param integer uid 发布者uid
	 * @param mixed attach_ids 附件ID  
	 * @return integer feed_id 微博ID
	 */
	public function syncToFeed($content,$uid,$attach_ids,$from) {	
		$d['content'] = '';
		$d['body'] = $content.'&nbsp;';
		$d['from'] = 0; //TODO
		if($attach_ids){
			$type = 'postimage';
			$d['attach_id'] = $attach_ids;
		}else{
			$type = 'post';
		}
		$feed = model('Feed')->put($uid, 'public', $type, $d, '', 'feed');
		return $feed['feed_id'];
	}

	/**
	 * 分享到微博
	 * @param string content 内容
	 * @param integer uid 分享者uid
	 * @param mixed attach_ids 附件ID  
	 * @return integer feed_id 微博ID
	 */
	public function shareToFeed($content,$uid,$attach_ids,$from) {	
		$d['content'] = '';
		$d['body'] = $content.'&nbsp;';
		$d['from'] = 0; //TODO
		if($attach_ids){
			$type = 'postimage';
			$d['attach_id'] = $attach_ids;
		}else{
			$type = 'post';
		}
		$feed = model('Feed')->put($uid, 'public', $type, $d, '', 'feed');
		return $feed['feed_id'];
	}
	
	/**
	 * 增加问题的浏览数
	 * @param 问题ID
	 * @return void
	 */
	public function UpdatePV($feedid)
	{
		$feed = model('Feed')->field('feed_pv')->where('feed_id='.$feedid)->select();
		$updData['feed_pv']=$feed[0]['feed_pv'] + 1;
		model('Feed')->where('feed_id='.$feedid)->save($updData);
		$feedids=array($feedid);
		$this->cleanCache($feedids);
	}
	
	/**
	 * 感谢答案(问题和用户添加感谢统计数)
	 *
	 * @return array 是否成功,失败原因
	 *
	 */	
	public function SetThankAnswer($feedid, $uid)
	{
		$return=array('status'=>0,'data'=>'感谢失败');
		$feed = model('Feed')->where('feed_id='.$feedid)->select();
		$Qfeed = model('Feed')->field('thank_count')->where('feed_id='.$feed[0]['feed_questionid'])->select();
		if($Qfeed[0]['thank_count'] <= 0)
		{
			$updData['thank_count'] = 1;
			$updResult = model('Feed')->where('feed_id='.$feedid)->save($updData);
			if($updResult!=false)
			{
				$QupdData['thank_count']=$Qfeed[0]['thank_count'] + 1;
				$QupdResult = model('Feed')->where('feed_id='.$feed[0]['feed_questionid'])->save($QupdData);
				if($QupdResult!=false)
				{
					model('UserData')->setUid($uid)->updateKey('tothanked_count', 1, true);
					$feedids=array($feedid, $feed[0]['feed_questionid']);
					$this->cleanCache($feedids);
					$return['status'] = 1;
					$return['data']="感谢成功";
				}	
			}
		}
		else
		{
			$return['status']=0;
			$return['data']="每个提问只能感谢一次";
		}
		return $return;
	}
	
	
	
}