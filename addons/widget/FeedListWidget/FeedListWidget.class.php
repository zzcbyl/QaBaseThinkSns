<?php
/**
 * 提问列表
 * @example {:W('FeedList',array('type'=>'space','feed_type'=>$feed_type,'feed_key'=>$feed_key,'loadnew'=>0,'gid'=>$gid))}
 * @author jason
 * @version TS3.0
 */
class FeedListWidget extends Widget {
	
	private static $rand = 1;
	private $limitnums   = 10;

    /**
     * @param string type 获取哪类提问 following:我关注的 space：
     * @param string feed_type 提问类型
     * @param string feed_key 提问关键字
     * @param integer fgid 关注的分组id
     * @param integer gid 群组id
     * @param integer loadnew 是否加载更多 1:是  0:否
     */
	public function render($data) {
		$var = array();
		$var['loadmore'] = 1;
		$var['loadnew'] = 1;
		$var['tpl'] = 'FeedList.html';
		
 		is_array($data) && $var = array_merge($var, $data);
    
 		$weiboSet = model('Xdata')->get('admin_Config:feed');
        $var['initNums'] = $weiboSet['weibo_nums'];
        $var['weibo_type'] = $weiboSet['weibo_type'];
        $var['weibo_premission'] = $weiboSet['weibo_premission'];
        // 我关注的频道
        $var['channel'] = M('channel_follow')->where('uid='.$this->mid)->count();
 
        // 查询是否有话题ID
        if($var['topic_id']) {
        	$content = $this->getTopicData($var,'_FeedList.html');
        } else {
        	$content = $this->getData($var,'_FeedList.html');
        }
		
        // 查看是否有更多数据
        if(empty($content['html'])) {
        	// 没有更多的
        	$var['list'] = L('PUBLIC_WEIBOISNOTNEW');
        } else {
        	$var['list'] = $content['html'];
        	$var['lastId'] = $content['lastId'];
        	$var['firstId'] = $content['firstId'] ? $content['firstId'] : 0;
        	$var['pageHtml']	= $content['pageHtml'];
        }
		//print($var['lastId']);
	    $content['html'] = $this->renderFile(dirname(__FILE__)."/".$var['tpl'], $var); 
		
		self::$rand ++;
		//unset($var, $data);
        //输出数据
		return $content['html'];
    }
    /**
     * 显示更多提问
     * @return  array 更多提问信息、状态和提示
     */
    public function loadMore() {
        // 获取GET与POST数据
    	$_REQUEST = $_GET + $_POST;
        // 查询是否有分页
    	if(!empty($_REQUEST['p']) ) {
    		unset($_REQUEST['loadId']);
    		$this->limitnums = 40;
    	} else {
    		$return = array('status'=>-1,'msg'=>L('PUBLIC_LOADING_ID_ISNULL'));
            $_REQUEST['loadId'] = intval($_REQUEST['loadId']);
    		$this->limitnums = 10;
    	}
        // 查询是否有话题ID
        if($_REQUEST['topic_id']) { 
            $content = $this->getTopicData($_REQUEST,'_FeedList.html');
        } else {
    	    $content = $this->getData($_REQUEST,'_FeedList.html');
        }
		
		/*$return = array('status'=>1,'msg'=>L('PUBLIC_SUCCESS_LOAD'));
		$return['html'] = '||'.json_encode($content).'||';
		exit(json_encode($return));*/
		
        // 查看是否有更多数据
    	if(empty($content['html'])) {
            // 没有更多的
    		$return = array('status'=>0,'msg'=>L('PUBLIC_WEIBOISNOTNEW'));
    	} else {
    		$return = array('status'=>1,'msg'=>L('PUBLIC_SUCCESS_LOAD'));
    		$return['html'] = $content['html'];
    		$return['loadId'] = $content['lastId'];
            $return['firstId'] = ( empty($_REQUEST['p']) && empty($_REQUEST['loadId']) ) ? $content['firstId'] : 0;
    		$return['pageHtml']	= $content['pageHtml'];
    	}
        exit(json_encode($return));
    }

    /**
     * 显示最新提问
     * @return  array 最新提问信息、状态和提示
     */
    public function loadNew() {
    	$return = array('status'=>-1,'msg'=>'');
        $_REQUEST['maxId'] = intval($_REQUEST['maxId']);
    	if(empty($_REQUEST['maxId'])){
    		echo json_encode($return);exit();
    	}
    	$content = $this->getData($_REQUEST,'_FeedList.html');
    	if(empty($content['html'])){//没有最新的
    		$return = array('status'=>0,'msg'=>L('PUBLIC_WEIBOISNOTNEW'));
    	}else{
    		$return = array('status'=>1,'msg'=>L('PUBLIC_SUCCESS_LOAD'));
    		$return['html'] = $content['html'];
    		$return['maxId'] = intval($content['firstId']);
            $return['count'] = intval($content['count']);
    	}
    	echo json_encode($return);exit();
    }
    
    /**
     * 获取提问数据，渲染提问显示页面
     * @param array $var 提问数据相关参数
     * @param string $tpl 渲染的模板
     * @return array 获取提问相关模板数据
     */
    private function getData($var, $tpl = 'FeedList.html') {
    	$var['feed_key'] = t($var['feed_key']);
		$var['newcount'] = t($var['newCount']);
        $var['cancomment'] = isset($var['cancomment']) ? $var['cancomment'] : 1;
        //$var['cancomment_old_type'] = array('post','repost','postimage','postfile');
        $var['cancomment_old_type'] = array('post','repost','postimage','postfile','weiba_post','weiba_repost');
        // 获取提问配置
        $weiboSet = model('Xdata')->get('admin_Config:feed');
        $var = array_merge($var, $weiboSet);
    	$var['remarkHash'] = model('Follow')->getRemarkHash($GLOBALS['ts']['mid']);
    	$map = $list = array();
    	$type = $var['new'] ? 'new'.$var['type'] : $var['type'];	// 最新的提问与默认提问类型一一对应
		switch($type) {
			case 'following':// 我关注的
				if(!empty($var['feed_key'])){
					//关键字匹配 采用搜索引擎兼容函数搜索 后期可能会扩展为搜索引擎
					$list = model('Feed')->searchFeed($var['feed_key'],'following',$var['loadId'],$this->limitnums);
				}else{
					$current_uid=$GLOBALS['ts']['mid'];
					if($var['uid']!=null&&$var['uid']!='0') $current_uid = $var['uid'];
					$where =' (a.is_audit=1 OR a.is_audit=0 AND a.uid='.$current_uid.') AND a.is_del = 0 AND a.feed_questionid=0 AND a.add_feedid=0 ';
					$LoadWhere = '';
					if($var['loadId'] > 0){ //非第一次
						//$where .=" AND a.feed_id < '".intval($var['loadId'])."'";
						$LoadWhere = "publish_time < '".intval($var['loadId'])."'";
					}
					if(!empty($var['feed_type'])){
						if ( $var['feed_type'] == 'post' ){
							$where .=" AND a.is_repost = 0";
						} else {
							$where .=" AND a.type = '".t($var['feed_type'])."'";
						}
					}
					$list =  model('Feed')->getFollowingFeed($where,$this->limitnums,$current_uid,$var['fgid'],$LoadWhere);
					//print_r($list);
				}
				break;
			case 'all'://所有的 --正在发生的
				if(!empty($var['feed_key'])){
					//关键字匹配 采用搜索引擎兼容函数搜索 后期可能会扩展为搜索引擎
					$list = model('Feed')->searchFeed($var['feed_key'],'all',$var['loadId'],$this->limitnums);
				}else{
					$where =' (is_audit=1 OR is_audit=0) AND is_del = 0 AND feed_questionid=0 AND add_feedid=0 ';
					if($var['loadId'] > 0){ //非第一次
						$where .=" AND publish_time < '".intval($var['loadId'])."'";
					}
					if(!empty($var['feed_type'])){
						if ( $var['feed_type'] == 'post' ){
							$where .=" AND is_repost = 0";
						} else {
							$where .=" AND type = '".t($var['feed_type'])."'";
						}
					}
					$list = model('Feed')->getQuestionAndAnswer($where,$this->limitnums);
					
					//print_r($list);
				}
				break;
			case 'newfollowing'://关注的人的最新提问  (检查使用的地方)
				$where = ' a.is_del = 0 and a.is_audit = 1 and a.uid != '.$GLOBALS['ts']['uid'].' and a.feed_questionid=0 AND a.add_feedid=0 ';
				if($var['maxId'] > 0){
					$where .=" AND a.publish_time > '".intval($var['maxId'])."'";
					$list = model('Feed')->getFollowingFeed($where);
					$content['count'] = $list['count'];
				}		
				break;
			case 'newall':	//所有人最新提问 -- 正在发生的 (检查使用的地方)
				if($var['maxId'] > 0){
					$map['feed_questionid'] = 0;
					$map['add_feedid'] = 0;
					$map['publish_time'] = array('gt',intval($var['maxId']));
					$map['is_del'] = 0;
					$map['is_audit'] = 1;
					$map['uid']   = array('neq',$GLOBALS['ts']['uid']);
					$list = model('Feed')->getList($map);    
					$content['count'] = $list['count'];
				}
				break;
			case 'space':	//用户个人空间 (检查使用的地方)
				if(!empty($var['feed_key'])){
					//关键字匹配 采用搜索引擎兼容函数搜索 后期可能会扩展为搜索引擎
					$list = model('Feed')->searchFeed($var['feed_key'],'space',$var['loadId'],$this->limitnums,'',$var['feed_type']);
				}else{
					if($var['loadId']>0){
						$map['publish_time'] = array('lt',intval($var['loadId']));
					}
					$map['feed_questionid'] = 0;
					$map['add_feedid'] = 0;
					$map['is_del'] = 0;
					if($GLOBALS['ts']['mid'] != $GLOBALS['ts']['uid']) $map['is_audit'] = 1;
					$list = model('Feed')->getUserList($map,$GLOBALS['ts']['uid'],  $var['feedApp'], $var['feed_type'],$this->limitnums);
					//print_r($list);
				}
				break;
			case 'channel':
				$where = ' (c.is_audit=1 OR c.is_audit=0) AND c.is_del = 0 AND c.feed_questionid=0 AND c.add_feedid=0 ';
				if($var['loadId'] > 0) { //非第一次
					$where .= " AND c.publish_time < '".intval($var['loadId'])."'";
				}
				if(!empty($var['feed_type'])) {
					$where .= " AND c.type = '".t($var['feed_type'])."'";
				}

				$list = D('ChannelFollow', 'channel')->getFollowingFeed($where, $this->limitnums, '' ,$var['fgid']);
				break;
			case 'question':// 我问题
				if(!empty($var['feed_key'])){
					//关键字匹配 采用搜索引擎兼容函数搜索 后期可能会扩展为搜索引擎
					$list = model('Feed')->searchFeed($var['feed_key'],'question',$var['loadId'],$this->limitnums);
				}else{
					if($var['loadId'] > 0){ //非第一次
						//$where .=" AND a.feed_id < '".intval($var['loadId'])."'";
						$LoadWhere = "AND publish_time < '".intval($var['loadId'])."'";
					}
					$current_uid=$GLOBALS['ts']['mid'];
					if($var['uid']!=null&&$var['uid']!='0') $current_uid = $var['uid'];
					$where =' uid='.$current_uid.' AND is_del = 0 AND feed_questionid=0 AND add_feedid=0 AND (is_audit=1 OR is_audit=0) '.$LoadWhere;
					$list = model('Feed')->getQuestionAndAnswer($where, $this->limitnums);
					//print_r($list);
				}
				break;
			case 'answer':	//个人回答列表
				if(!empty($var['feed_key'])){
					//关键字匹配 采用搜索引擎兼容函数搜索 后期可能会扩展为搜索引擎
					$list = model('Feed')->searchFeed($var['feed_key'],'answer',$var['loadId'],$this->limitnums,'',$var['feed_type']);
				}else{
					$LoadWhere='';
					if($var['loadId'] > 0){ //非第一次
						$LoadWhere = "AND publish_time < '".intval($var['loadId'])."'";
					}
					$current_uid=$GLOBALS['ts']['mid'];
					if($var['uid']!=null&&$var['uid']!='0') $current_uid = $var['uid'];
					$where =' uid='.$current_uid.' AND is_del = 0 AND feed_questionid!=0 AND add_feedid=0 AND (is_audit=1 OR is_audit=0) '.$LoadWhere;
					$list = model('Feed')->getAnswerList($where, $this->limitnums);
					//print_r($list);
				}
				break;
			case 'collection':	//个人收藏列表 ------------
				if($var['loadId'] > 0){ //非第一次
					$LoadWhere = "collection_id < '".intval($var['loadId'])."'";
					$map['source_id'] = array('lt',intval($var['loadId']));
				}
				$map['uid'] = $GLOBALS['ts']['uid'];
				// 安全过滤
				$t = t($_GET['t']);
				!empty($t) && $map['source_table_name'] = $t;
				$list = model('Collection')->getCollectionList1($map, $this->limitnums,'collection_id DESC');
				//print_r($list);
				/*$current_uid=$GLOBALS['ts']['mid'];
				if($var['uid']!=null&&$var['uid']!='0') $current_uid = $var['uid'];
				$where =' uid='.$current_uid.' AND is_del = 0 AND feed_questionid!=0 AND (is_audit=1 OR is_audit=0) ';
				$list = model('Feed')->getAnswerList($where, $this->limitnums);*/
				//print_r($list);
				break;
			case 'agree':	//个人赞同列表
				if($var['loadId'] > 0){ //非第一次
					$LoadWhere = "t.comment_count < '".intval($var['loadId'])."'";
				}
				$current_uid=$GLOBALS['ts']['mid'];
				if($var['uid']!=null && $var['uid']!='0') $current_uid = $var['uid'];
				$where =" `uid` = $current_uid AND `feed_questionid` != 0 AND `add_feedid`=0 and `comment_count` > 0";
				$list = model('Feed')->getCommentFeedList($where, $this->limitnums,$LoadWhere);
				//print_r($list);
				break;
			case 'newanswer':	//消息答案列表
				$LoadWhere='';
				if($var['loadId'] > 0){ //非第一次
					$LoadWhere = "AND publish_time < '".intval($var['loadId'])."'";
				}
				$where ="(is_audit=1 OR is_audit=0) AND is_del = 0 AND feed_quid = ".$GLOBALS['ts']['mid']." AND feed_questionid != 0 AND add_feedid=0 ".$LoadWhere;
				$list = model('Feed')->getAnswerList($where, $this->limitnums, 'publish_time desc', $var['newcount']);
				//print_r($list);
				break;	
			case 'newagreecomment':		//消息赞同列表
				$map['app_uid']=$GLOBALS['ts']['mid'];
				$map['comment_type']=1;
				$map['is_del']=0;
				if($var['loadId'] > 0){ //非第一次
					$map['comment_id'] = array('lt', intval($var['loadId']));
				}
				$list = model('Feed')->getNewCommentFeedListByType($map, $this->limitnums, 'comment_id DESC', $var['newcount']);
				//print_r($list);
				break;	
			case 'newopposecomment':		//消息反对列表
				$map['app_uid']=$GLOBALS['ts']['mid'];
				$map['comment_type']=2;
				$map['is_del']=0;
				if($var['loadId'] > 0){ //非第一次
					$map['comment_id'] = array('lt', intval($var['loadId']));
				}
				$list = model('Feed')->getNewCommentFeedListByType($map, $this->limitnums, 'comment_id DESC', $var['newcount']);
				//print_r($list);
				break;	
			case 'newcomment':		//消息评论列表
				$map['app_uid']=$GLOBALS['ts']['mid'];
				$map['comment_type']=0;
				$map['is_del']=0;
				if($var['loadId'] > 0){ //非第一次
					$map['comment_id'] = array('lt', intval($var['loadId']));
				}
				$list = model('Feed')->getNewCommentFeedList($map, $this->limitnums, 'comment_id DESC', $var['newcount']);
				//print_r($list);
				break;	
			case 'thank':	//感谢列表
				$current_uid=$GLOBALS['ts']['mid'];
				if($var['uid']!=null && $var['uid']!='0') $current_uid = $var['uid'];
				$where =" `uid` = ".$current_uid." AND `feed_questionid` != 0 and `add_feedid`=0 and `thank_count` > 0 ";
				if($var['loadId']>0){
					$where = $where.' publish_time < '.intval($var['loadId']);
				}
				$list = model('Feed')->getAnswerList($where, $this->limitnums);
				//print_r($list);
				break;
			case 'feedfollowing':	
				$current_uid=$GLOBALS['ts']['mid'];
				if($var['uid']!=null && $var['uid']!='0') $current_uid = $var['uid'];
				$where =" `uid` = ".$current_uid;
				if($var['loadId']>0){
					$where = $where.' and ctime < '.intval($var['loadId']);
				}
				$list = model('FeedFollowing')->getFeedFollowingList($where, $this->limitnums);
				//print_r($list);
				break;
			case 'invite':	//邀请我的
				$current_uid=$GLOBALS['ts']['mid'];
				if($var['loadId'] > 0){ //非第一次
					$LoadWhere = "invite_answer_id < '".intval($var['loadId'])."'";
				}
				$list =  model('Feed')->getInviteList($current_uid, $this->limitnums, $LoadWhere, $var['newcount']);
				//print_r($list);
				break;
		}
    	// 分页的设置
        isset($list['html']) && $var['html'] = $list['html'];
		$feedlist = array();
    	if(!empty($list['data'])) {
			switch($type) {
				case 'collection':
					$content['firstId'] = $var['firstId'] = $list['data'][0]['collection']['collection_id'];
					$content['lastId'] = $var['lastId'] = $list['data'][(count($list['data'])-1)]['collection']['collection_id'];
					break;
				case 'answer':
				case 'newanswer':
				case 'thank':
					$content['firstId'] = $var['firstId'] = $list['data'][0]['answer'][0]['publish_time'];
					$content['lastId'] = $var['lastId'] = $list['data'][(count($list['data'])-1)]['answer'][0]['publish_time'];
					break;
				case 'agree':
					$content['firstId'] = $var['firstId'] = $list['data'][0]['answer'][0]['comment_count'];
					$content['lastId'] = $var['lastId'] = $list['data'][(count($list['data'])-1)]['answer'][0]['comment_count'];
					break;
				case 'newagreecomment':
				case 'newopposecomment':
				case 'newcomment':
					$content['firstId'] = $var['firstId'] = $list['data'][0]['comment']['comment_id'];
					$content['lastId'] = $var['lastId'] = $list['data'][(count($list['data'])-1)]['comment']['comment_id'];
					break;
				case 'feedfollowing':
					$content['firstId'] = $var['firstId'] = $list['data'][0]['ctime'];
					$content['lastId'] = $var['lastId'] = $list['data'][(count($list['data'])-1)]['ctime'];
					$index = 0;
					foreach($list['data'] as $k => $v)
					{
						$feedlist['data'][$index]=$v['feed_data']['data'][0];
						$index++;
					}
					break;
				case 'invite':	//邀请我的
					$content['firstId'] = $var['firstId'] = $list['data'][0]['invite']['invite_answer_id'];
					$content['lastId'] = $var['lastId'] = $list['data'][(count($list['data'])-1)]['invite']['invite_answer_id'];
					break;
				default:
					$content['firstId'] = $var['firstId'] = $list['data'][0]['publish_time'];
					$content['lastId'] = $var['lastId'] = $list['data'][(count($list['data'])-1)]['publish_time'];
					break;	
			}
			
			if(count($feedlist) > 0) //关注问题的数据
				$var['data']= $feedlist['data'];
			else
				$var['data'] = $list['data'];
			
            //赞功能
            $feed_ids = getSubByKey($var['data'],'feed_id');
            $var['diggArr'] = model('FeedDigg')->checkIsDigg($feed_ids, $GLOBALS['ts']['mid']);
            
            $uids = array();
            foreach($var['data'] as &$v) {
            	switch ( $v['app'] ){
            		case 'weiba':
            			$v['from'] = getFromClient(0 , $v['app'] , '微吧');
            			break;
                    case 'tipoff':
                    $v['from'] = getFromClient(0 , $v['app'] , '爆料');
                    break;
            		default:
            			$v['from'] = getFromClient( $v['from'] , $v['app']);
            			break;
            	}
            	!isset($uids[$v['uid']]) && $v['uid'] != $GLOBALS['ts']['mid'] && $uids[] = $v['uid'];
            }
            if(!empty($uids)) {
            	$map = array();
            	$map['uid'] = $GLOBALS['ts']['mid'];
            	$map['fid'] = array('in',$uids);
            	$var['followUids'] = model('Follow')->where($map)->getAsFieldArray('fid');
            } else {
            	$var['followUids'] = array();
            }
    	}

		//print_r($var);
		
    	$content['pageHtml'] = $list['html'];
		//print(dirname(__FILE__));
	    // 渲染模版
		//$content['html'] = $this->renderFile(dirname(__FILE__)."/".$tpl, $var);
		$content['html'] = $this->renderFile("E:/WorkCode/QaBaseThinkSns/addons/widget/FeedListWidget/_FeedList.html", $var);
		
		//print(dirname(__FILE__)."/".$tpl);
		//return $var;
		//return dirname(__FILE__)."\\".$tpl;
	    return $content;
    }

    /**
     * 获取话题提问数据，渲染提问显示页面
     * @param array $var 提问数据相关参数
     * @param string $tpl 渲染的模板
     * @return array 获取提问相关模板数据
     */
    private function getTopicData($var,$tpl='FeedList.html') {
        $var['cancomment'] = isset($var['cancomment']) ? $var['cancomment'] : 1;
        //$var['cancomment_old_type'] = array('post','repost','postimage','postfile');
        $var['cancomment_old_type'] = array('post','repost','postimage','postfile','weiba_post','weiba_repost');
        $weiboSet = model('Xdata')->get('admin_Config:feed');
        $var = array_merge($var,$weiboSet);
        $var['remarkHash'] = model('Follow')->getRemarkHash($GLOBALS['ts']['mid']);
        $map = $list = array();
        $type = $var['new'] ? 'new'.$var['type'] : $var['type'];    //最新的提问与默认提问类型一一对应

        if($var['loadId'] > 0){ //非第一次
            $topics['topic_id'] = $var['topic_id'];
            $topics['feed_id'] = array('lt',intval($var['loadId']));
            $map['feed_id'] = array('in',getSubByKey(D('feed_topic_link')->where($topics)->field('feed_id')->select(),'feed_id'));
        }else{
            $map['feed_id'] = array('in',getSubByKey(D('feed_topic_link')->where('topic_id='.$var['topic_id'])->field('feed_id')->select(),'feed_id'));
        }
        if(!empty($var['feed_type'])){
            $map['type'] = t($var['feed_type']);
        }
        //$map['is_del'] = 0;
        $map['_string'] = ' (is_audit=1 OR is_audit=0 AND uid='.$GLOBALS['ts']['mid'].') AND is_del = 0 ';
        $list = model('Feed')->getList($map,$this->limitnums);
        //分页的设置
        isset($list['html']) && $var['html'] = $list['html'];
        
        if(!empty($list['data'])){
            $content['firstId'] = $var['firstId'] = $list['data'][0]['feed_id'];
            $content['lastId']  = $var['lastId'] = $list['data'][(count($list['data'])-1)]['feed_id'];
            $var['data'] = $list['data'];
            
            //赞功能
            $feed_ids = getSubByKey($var['data'],'feed_id');
            $var['diggArr'] = model('FeedDigg')->checkIsDigg($feed_ids, $GLOBALS['ts']['mid']);
            
            $uids = array();
            foreach($var['data'] as &$v){
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
            if(!empty($uids)){
                $map = array();
                $map['uid'] = $GLOBALS['ts']['mid'];
                $map['fid'] = array('in',$uids);
                $var['followUids'] = model('Follow')->where($map)->getAsFieldArray('fid');
            }else{
                $var['followUids'] = array();
            }
        }

        $content['pageHtml'] = $list['html'];
       
        //渲染模版
        $content['html'] = $this->renderFile(dirname(__FILE__)."/".$tpl,$var);
      
        return $content;
    }

    /**
     * 获取微吧帖子数据
     * @param  [varname] [description]
     */
    public function getPostDetail() {
        $post_id = intval($_POST['post_id']);
        $post_detail = D('weiba_post')->where('is_del=0 and post_id='.$post_id)->find();
        if($post_detail && D('weiba')->where('is_del=0 and weiba_id='.$post_detail['weiba_id'])->find()){
            $post_detail['post_url'] = U('weiba/Index/postDetail',array('post_id'=>$post_id));
            $author = model('User')->getUserInfo($post_detail['post_uid']);
            $post_detail['author'] = $author['space_link'];
            $post_detail['post_time'] = friendlyDate($post_detail['post_time']);
            $post_detail['from_weiba'] = D('weiba')->where('weiba_id='.$post_detail['weiba_id'])->getField('weiba_name');
            $post_detail['weiba_url'] = U('weiba/Index/detail',array('weiba_id'=>$post_detail['weiba_id']));
            return json_encode($post_detail);
        }else{
            echo 0;
        }
    }

    public function getTipoffDetail(){
        $tipoff_id = intval($_POST['tipoff_id']);
        $tipoff_detail = D('tipoff')->where('deleted=0 and archived=0 and tipoff_id='.$tipoff_id)->find();
        if($tipoff_detail){
            $tipoff_detail['tipoff_url'] = U('tipoff/Index/detail',array('id'=>$tipoff_id));
            $author = model('User')->getUserInfo($tipoff_detail['uid']);
            $tipoff_detail['author'] = $author['space_link'];
            $tipoff_detail['publish_time'] = friendlyDate($tipoff_detail['publish_time']);
            $tipoff_detail['from_category'] = D('tipoff_category')->where('tipoff_category_id='.$tipoff_detail['category_id'])->getField('title');
            $tipoff_detail['category_url'] = U('tipoff/Index/index',array('cid'=>$tipoff_detail['category_id']));
            return json_encode($tipoff_detail);
        }else{
            echo 0;
        }
    }
    
}