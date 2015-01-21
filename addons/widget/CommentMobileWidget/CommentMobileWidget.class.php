<?php
/**
  * 手机评论发布/显示框
  * @example W('CommentMobile',array('tpl'=>'detail','row_id'=>72,'order'=>'DESC','app_uid'=>'14983','cancomment'=>1,'cancomment_old'=>0,'showlist'=>1,'canrepost'=>1))                                  
  * @author zhangzc 
  * @version TS3.0
  */
class CommentMobileWidget extends Widget
{	
	private static $rand = 1;

    /**
     * @param string tpl 显示模版 默认为comment，一般使用detail表示详细资源页面的评论
     * @param integer row_id 评论对象所在的表的ID
     * @param string order 评论的排序，默认为ASC 表示从早到晚,应用中一般是DESC
     * @param integer app_uid 评论的对象的作者ID
     * @param integer cancomment 是否可以评论  默认为1,由应用中判断好权限之后传入给wigdet
     * @param integer cancomment_old 是否可以评论给原作者 默认为1,应用开发时统一使用0
     * @param integer showlist 是否显示评论列表 默认为1
     * @param integer canrepost 是否允许转发  默认为1,应用开发的时候根据应用需求设置1、0
     */
	public function render($data)
    {
		$var = array();
		// 默认配置数据
        $var['cancomment']  = 1;  //是否可以评论
        $var['canrepost']   = 1;  //是否允许转发
        $var['cancomment_old'] = 1; //是否可以评论给原作者
		$var['showlist'] = 1;         // 默认显示原评论列表
		$var['tpl'] 	 = 'Comment'; // 显示模板
		$var['app_name'] = 'public';
		$var['table']    = 'feed';
		$var['limit'] 	 = 10;
		$var['order']	 = 'DESC';
		$var['initNums'] = model('Xdata')->getConfig('weibo_nums','feed');
		$_REQUEST['p'] = intval($_GET['p']) ? intval($_GET['p']) : intval($_POST['p']);
		empty($data) && $data = $_POST;
		is_array($data) && $var = array_merge($var,$data);
		$var['hasAnswerComment'] = '0';
		//对答案只能赞成或者反对一次
		if($var['comment_type'] == 1 || $var['comment_type'] == 2)
		{
			$Commentwhere='`app`=\''.t($var['app_name']).'\' and `table`=\''.t($var['table']).'\' and `row_id`='.intval($var['row_id']).' and `uid`='.$this->mid.' and `is_del` = 0 and (`comment_type`=1 or `comment_type`=2)';
			$hasComment=model('Comment')->hasComment($Commentwhere);
			if($hasComment)
			{
				$var['hasAnswerComment'] = '1';
				/*$returnData = '<div class="answer2"><div class="aTop511"><p class="icon2" style="right:85px"></p></div><div class="aCenter2" style="width: 452px;"><div id="commentlist_'.intval($var['row_id']).'"></div></div><div class="aBottom511"></div></div>';
				if($var['comment_type'] == 2)
					$returnData = '<div class="answer2"><div class="aTop511"><p class="icon2" style="right:45px"></p></div><div class="aCenter2" style="width: 452px;"><div id="commentlist_'.intval($var['row_id']).'"></div></div><div class="aBottom511"></div></div>';	
				
				$return = array('status'=>2,'data'=>$returnData);
				return $var['isAjax'] == 1 ?  json_encode($return) : $return['data'];*/
			}
		}
		
		//评论自己的内容
		/*if($this->mid == $var['app_uid'])
		{
			$return = array('status'=>2,'data'=>'<div class="answer2"><div class="aTop2"><p class="icon2"></p></div><div class="aCenter2"><div id="commentlist_'.intval($var['row_id']).'"><div style="text-align:center; color: #999999;">暂无评论</div></div></div><div class="aBottom2"></div></div>');
			return $var['isAjax'] == 1 ?  json_encode($return) : $return['data'];
		}*/
		
        if($var['table'] == 'feed' && $this->mid != $var['app_uid']){
            $userPrivacy =  model('UserPrivacy')->getPrivacy($this->mid,$var['app_uid']);

            if($userPrivacy['comment_weibo'] == 1){
                $return = array('status'=>0,'data'=>L('PUBLIC_CONCENT_TIPES'));
                return $var['isAjax'] == 1 ?  json_encode($return) : $return['data'];
            }
            
        }
		if($var['showlist'] ==1){ //默认只取出前10条
			$map = array();
			$map['app'] 	= t($var['app_name']);
			$map['table']	= t($var['table']);
			$map['row_id']	= intval($var['row_id']);	//必须存在
			if($var['comment_type'] == 1 || $var['comment_type'] == 2)
			{
				$map['comment_type']	= intval($var['comment_type']);
			}
			if(!empty($map['row_id'])){	
				//分页形式数据
				$var['list'] = model('Comment')->getCommentList($map,'comment_id '.$var['order'],$var['limit']);
				//print_r($map);
			}
		}//渲染模版
        // 获取源资源作者用户信息
        $rowData = model('Feed')->get($var['row_id']);
        if($var['app_name'] == 'public'){
            $appRowData = model('Feed')->get($rowData['app_row_id']);
            $var['user_info'] = $appRowData['user_info'];
        }else{
            $modelArr = explode('_', $rowData['app_row_table']);
            $model = '';
            foreach($modelArr as $v) {
                $model .=ucfirst($v);
            }
            if(file_exists(SITE_PATH.'/apps/'.$var['app_name'].'/Lib/Model/'.$model.'Model.class.php')){
                $sourceInfo = D($model, $var['app_name'])->getSourceInfo($rowData['app_row_id']);
                $var['user_info'] = $sourceInfo['source_user_info'];
            }
        }
        // 获取资源类型
        $sourceInfo = model('Feed')->get($var['row_id']);
        $var['feedtype'] = $sourceInfo['type'];
        
        //转发权限判断
        $weiboSet = model('Xdata')->get('admin_Config:feed');
        if ( !CheckPermission('core_normal','feed_share') || !in_array( 'repost' , $weiboSet['weibo_premission'] ) ){
        	$var['canrepost'] = 0;
        }
		//print_r($var);
	    $content = $this->renderFile(dirname(__FILE__)."/".$var['tpl'].'.html',$var);
		self::$rand ++;
        $ajax = $var['isAjax'];
		unset($var,$data);
		
        //输出数据
        $return = array('status'=>1,'data'=>$content);

		return $ajax==1 ? json_encode($return) : $return['data'];
    }

    public function getCommentList(){
    	$map = array();
    	$map['app'] 	= t($_POST['app_name']);
    	$map['table']	= t($_POST['table']);
    	$map['row_id']	= intval($_POST['row_id']);	//必须存在
		$map['comment_type']	= t($_POST['comment_type']);
    	if(!empty($map['row_id'])){
    		//分页形式数据
			$var['comment_type'] = t($_POST['comment_type']);
    		$var['limit'] 	 = 10;
    		$var['order']	 = 'DESC';
    		$var['cancomment'] = $_POST['cancomment'];
    		$var['showlist'] = $_POST['showlist'];
    		$var['app_name'] = t($_POST['app_name']);
    		$var['table'] = t($_POST['table']);
    		$var['row_id'] = intval($_POST['row_id']);
    		$var['list'] = model('Comment')->getCommentList($map,'comment_id '.$var['order'],$var['limit']);
    	}
    	$content = $this->renderFile(dirname(__FILE__).'/commentList.html',$var);
    	exit($content);
    }
    /**
     * 添加评论的操作
     * @return array 评论添加状态和提示信息
     */
    public function addcomment()
    {
        // 返回结果集默认值
        $return = array('status'=>0,'data'=>L('PUBLIC_CONCENT_IS_ERROR'));
        // 获取接收数据
    	$data = $_POST;
        // 安全过滤
        foreach($data as $key => $val) {
            $data[$key] = t($data[$key]);
        }
        // 评论所属与评论内容
    	$data['app'] = $data['app_name'];
    	$data['table'] = $data['table_name'];
        $data['content'] = h($data['content']);
		$data['comment_type'] = $data['comment_type'];
		//返回评论类型
		$return['comment_type'] = $data['comment_type'];
		
        // 判断资源是否被删除
        switch ($data['table']) {
            case 'photo':
                $map['id'] = $data['row_id'];
                $isExist = M(ucfirst($data['table']),'photo')->where($map)->count();
                break;
            case 'event':
                $map['id'] = $data['row_id'];
                $isExist = M(ucfirst($data['table']),'event')->where($map)->count();
                break;
            case 'vote':
                $map['id'] = $data['row_id'];
                $isExist = M(ucfirst($data['table']),'vote')->where($map)->count();
                break;
            case 'blog':
                $map['id'] = $data['row_id'];
                $isExist = M(ucfirst($data['table']),'blog')->where($map)->count();
                break;
            case 'poster':
                $map['id'] = $data['row_id'];
                $isExist = M(ucfirst($data['table']),'poster')->where($map)->count();
                break;
            case 'develop':
                $map['develop_id'] = $data['row_id'];
                $isExist = M(ucfirst($data['table']),'develop')->where($map)->count();
                break;
            default:
/*                 $map[$data['table'].'_id'] = $data['row_id'];
                $map['is_del'] = 0;
                $isExist = model(ucfirst($data['table']))->where($map)->count(); */
                $isExist = 1;
                break;
        }
        
        // dump(model(ucfirst($data['table']))->getlastsql());exit();
        if($isExist == 0) {
            $return['status'] = 0;
            $return['data'] = '内容已被删除，评论失败';
            exit(json_encode($return));
        }
		
		//对答案只能赞成或者反对一次
		if($data['comment_type'] == 1 || $data['comment_type'] == 2)
		{
			$Commentwhere='`app`=\''.t($data['app_name']).'\' and `table`=\''.t($data['table']).'\' and `row_id`='.intval($data['row_id']).' and `uid`='.$this->mid.' and `is_del` = 0 and (`comment_type`=1 or `comment_type`=2)';
			$hasComment=model('Comment')->hasComment($Commentwhere);
			if($hasComment)
			{
				$return['status'] = 0;
				$return['data'] = '每个答案每人只能赞同或者反对一次';
				exit(json_encode($return));
			}
		}
    	// 添加评论操作
        // dump($data['comment_id'] = model('Comment')->addComment($data));
        // dump(model('Comment')->getlastsql());exit;
    	if($data['comment_id'] = model('Comment')->addComment($data)) {
            // 同步到微吧
            if($data['app'] == 'weiba'){
                $postDetail = D('weiba_post')->where('feed_id='.$data['row_id'])->find();
                if($postDetail) {
                    $datas['weiba_id'] = $postDetail['weiba_id'];
                    $datas['post_id'] = $postDetail['post_id'];
                    $datas['post_uid'] = $postDetail['post_uid'];
                    $datas['to_reply_id'] = $data['to_comment_id']?D('weiba_reply')->where('comment_id='.$data['to_comment_id'])->getField('reply_id'):0;
                    $datas['to_uid'] = $data['to_uid'];
                    $datas['uid'] = $this->mid;
                    $datas['ctime'] = time();
                    $datas['content'] = $data['content'];
                    $datas['comment_id'] = $data['comment_id'];
                    $datas['storey'] = model('comment')->where('comment_id='.$data['comment_id'])->getField('storey');
                    if(D('weiba_reply')->add($datas)) {
                        $map['last_reply_uid'] = $this->mid;
                        $map['last_reply_time'] = $datas['ctime'];
                        D('weiba_post')->where('post_id='.$datas['post_id'])->save($map);
                        // 回复统计数加1
                        D('weiba_post')->where('post_id='.$datas['post_id'])->setInc('reply_count'); 
                    }
                }
            }

    		$return['status'] = 1 ;
    		$return['data']	= $this->parseComment($data);

            $oldInfo = model('Source')->getSourceInfo($data['table'], !empty($data['app_row_id']) ? $data['app_row_id'] : $data['row_id'],false,$data['app']);
    		
            // 转发到我的提问
    		if($_POST['ifShareFeed'] == 1) {
                $commentInfo  = model('Source')->getSourceInfo($data['table'], $data['row_id'], false, $data['app']);
                $oldInfo = isset($commentInfo['sourceInfo']) ? $commentInfo['sourceInfo'] : $commentInfo; 
    			// 根据评论的对象获取原来的内容
    			$s['sid'] = $oldInfo['source_id'];
    			$s['app_name'] = $oldInfo['app'];
                if($commentInfo['feedType'] == 'post' || $commentInfo['feedType'] == 'postimage' || $commentInfo['feedType'] == 'postfile' || $commentInfo['feedType'] == 'postfile' || $commentInfo['feedType'] == 'weiba_post') {   //加入微吧类型，2012/11/15
                    if(empty($data['to_comment_id'])) {
                        $s['body'] = $data['content'];
                    } else {
                        $replyInfo = model('Comment')->setAppName($data['app'])->setAppTable($data['table'])->getCommentInfo(intval($data['to_comment_id']), false);
                        $replyScream = '//@'.$replyInfo['user_info']['uname'].' ：';
                        $s['body'] = $data['content'].$replyScream.$replyInfo['content'];
                    }
                } else {
                    $scream = '//@'.$commentInfo['source_user_info']['uname'].'：'.$commentInfo['source_content'];
                    if(empty($data['to_comment_id'])) {
                        $s['body'] = $data['content'].$scream;
                    } else {
                        $replyInfo = model('Comment')->setAppName($data['app'])->setAppTable($data['table'])->getCommentInfo(intval($data['to_comment_id']), false);
                        $replyScream = '//@'.$replyInfo['user_info']['uname'].' ：';
                        $s['body'] = $data['content'].$replyScream.$replyInfo['content'].$scream;
                    }
                }
    			$s['type']		= $oldInfo['source_table'];
    			$s['comment']   = $data['comment_old'];
                $s['comment_touid'] = $data['app_uid'];
                // 去掉回复用户@
                $lessUids = array();
                if(!empty($data['to_uid'])) {
                    $lessUids[] = $data['to_uid'];
                }
                // 如果为原创提问，不给原创用户发送@信息
                if($commentInfo['feedType'] == 'post' && empty($data['to_uid'])) {
                    $lessUids[] = $oldInfo['uid'];
                }
    			model('Share')->shareFeed($s,'comment', $lessUids);
    			model('Credit')->setUserCredit($this->mid,'forwarded_weibo');
    		} else {
                //是否评论给原来作者
                if($data['comment_old'] != 0) {
                    $commentInfo  = model('Source')->getSourceInfo($data['app_row_table'],$data['app_row_id'],false,$data['app']);
                    $oldInfo      = isset($commentInfo['sourceInfo']) ? $commentInfo['sourceInfo'] : $commentInfo;
                    //发表评论
                    $c['app']     = $data['app'];
                    //$c['table']   = $oldInfo['source_table'];
                    $c['table']   = 'feed'; //2013/3/27
                    $c['app_uid'] = !empty($oldInfo['source_user_info']['uid']) ? $oldInfo['source_user_info']['uid'] : $oldInfo['uid'];
                    $c['content'] = $data['content'];
                    $c['row_id']  = !empty($oldInfo['sourceInfo']) ? $oldInfo['sourceInfo']['source_id'] : $oldInfo['source_id'];
                    if($data['app']){
                        $c['row_id'] = $oldInfo['feed_id'];
                    }
                    $c['client_type'] = getVisitorClient();
                    // 去掉回复用户@
                    $lessUids = array();
                    if(!empty($data['to_uid'])) {
                        $lessUids[] = $data['to_uid'];
                    }
                    model('Comment')->addComment($c,false,false, $lessUids);
                }
            }
    	}

        exit(json_encode($return));
    }	
    
    /**
     * 删除评论
     * @return bool true or false
     */
    public function delcomment()
    {
    	// if( !CheckPermission('core_normal','comment_del') && !CheckPermission('core_admin','comment_del')){
    	// 	return false;
    	// }
    	$comment_id = intval($_POST['comment_id']);
        $comment = model('Comment')->getCommentInfo($comment_id);
        // 不存在时
        if(!$comment){
			$return['status'] = 0;
			$return['data'] = '评论不存在';
			exit(json_encode($return));
        }
		$return['comment_type'] = $comment['comment_type'];
        // 非作者时
        if($comment['uid']!=$this->mid){
            // 没有管理权限不可以删除
            if(!CheckPermission('core_admin','comment_del')){
				$return['status'] = 0;
				$return['data'] = '删除失败';
				exit(json_encode($return));
            }
        // 是作者时
        }else{
            // 没有前台权限不可以删除
            if(!CheckPermission('core_normal','comment_del')){
				$return['status'] = 0;
				$return['data'] = '删除失败';
				exit(json_encode($return));
            }
        }
    	if(!empty($comment_id)) {
			$result = model('Comment')->deleteComment($comment_id,$this->mid);
			model('Feed')->cleanCache(array(intval($comment['row_id'])));
			
			$return['status'] = 1;
			$return['data'] = '删除成功';
			exit(json_encode($return));
    	}
		$return['status'] = 0;
		$return['data'] = '删除失败';
		exit(json_encode($return));
    }
    
    /**
     * 渲染评论页面 在addcomment方法中调用
     */
    public function parseComment($data)
    {
        $data['userInfo'] = model('User')->getUserInfo($GLOBALS['ts']['uid']);
        // 获取用户组信息
        $data['userInfo']['groupData'] = model('UserGroupLink')->getUserGroupData($GLOBALS['ts']['uid']);
    	$data['content'] = preg_html($data['content']);
    	$data['content'] = parse_html($data['content']);
        $data['storey'] = model('Comment')->where('comment_id='.$data['comment_id'])->getField('storey');
        $data['iscommentdel'] = CheckPermission('core_normal','comment_del');
 	   	return $this->renderFile(dirname(__FILE__)."/_parseComment.html", $data);
	}
}