<?php
/**
 * 提问列表
 * @example {:W('PYQTopicList',array('type'=>'all'))}
 * @author jason
 * @version TS3.0
 */
class PYQTopicListWidget extends Widget {
	
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
    
        $content = $this->getData($var,'_FeedList.html');
		
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
		if(!empty($_REQUEST['p']) || intval($_REQUEST['load_count']) == 4) {
			unset($_REQUEST['loadId']);
			$this->limitnums = 40;
		} else {
			$return = array('status'=>-1,'msg'=>L('PUBLIC_LOADING_ID_ISNULL'));
			$_REQUEST['loadId'] = intval($_REQUEST['loadId']);
			$this->limitnums = 10;
		}
		
    	/*if(!empty($_REQUEST['p']) ) {
    		unset($_REQUEST['loadId']);
    		$this->limitnums = 40;
    	} else {
    		$return = array('status'=>-1,'msg'=>L('PUBLIC_LOADING_ID_ISNULL'));
            $_REQUEST['loadId'] = intval($_REQUEST['loadId']);
    		$this->limitnums = 10;
    	}*/

    	$content = $this->getData($_REQUEST,'_FeedList.html');
		
		/*$return = array('status'=>1,'msg'=>L('PUBLIC_SUCCESS_LOAD'));
		$return['html'] = '||'.json_encode($content).'||';
		exit(json_encode($return));*/
		
        // 查看是否有更多数据


    	if(empty($content['html'])) {
            // 没有更多的
			$return = array('status'=>0,'msg'=>'没有了');
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
    	$map = $list = array();
		$type = $var['type'];	// 最新的提问与默认提问类型一一对应
		switch($type) {
			case 'all':// 朋友圈
				//$current_uid=$GLOBALS['ts']['mid'];
				//if($var['uid']!=null&&$var['uid']!='0') $current_uid = $var['uid'];
				$where =' topic_state=1 ';
				if($var['loadId'] > 0){ //非第一次
					$where .= " and topic_dt < '".intval($var['loadId'])."'";
				}
				
				$list =  model('topic')->getTopicList($where, $this->limitnums);
				//print_r($list);
				
				break;
			case 'my':// 我的朋友圈
				$current_uid= $var['uid'];
				//if($var['uid']!=null&&$var['uid']!='0') $current_uid = $var['uid'];
				$where =' topic_state=1 and  topic_uid ='.$current_uid;
				if($var['loadId'] > 0){ //非第一次
					$where .= " and topic_dt < '".intval($var['loadId'])."'";
				}
				
				$list =  model('topic')->getMyTopicList($where, $this->limitnums, $current_uid);
				//print_r($list);
				
				break;
		}
    	// 分页的设置
        isset($list['html']) && $var['html'] = $list['html'];

    	if(!empty($list['data'])) {
			$content['firstId'] = $var['firstId'] = $list['data'][0]['topic_dt'];
			$content['lastId'] = $var['lastId'] = $list['data'][(count($list['data'])-1)]['topic_dt'];
			$var['data'] = $list['data'];
    	}
		
    	$content['pageHtml'] = $list['html'];
	    // 渲染模版
		$content['html'] = $this->renderFile(dirname(__FILE__)."/".$tpl, $var);

	    return $content;
    }

    public function delTopic()
	{
		// 返回数据格式
		$return = array('status'=>1, 'data'=>'');
		$topicid = $_GET['topicID'];
		
		if(empty($topicid))
		{
			$return = array('status'=>0,'data'=>'参数错误');
			exit(json_encode($return));
		}
		
		$map['topic_state'] = 2;
		$data = model('topic')->where('`topic_id` = '.$topicid.' and `topic_uid` = '.$GLOBALS['ts']['mid'])->save($map);
		if($data>0)
		{
			$return['data'] = $data;
		}
		else
		{
			$return = array('status'=>0,'data'=>'删除失败');
		}
		
		exit(json_encode($return));	
	}
}