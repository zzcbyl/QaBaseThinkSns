<?php
/**
 * 关注问题
 * @example W('FeedFollowing',array('fid'=>1,'tpl'=>'btn'))
 * @author Jason
 * @version TS3.0
 */
class FeedFollowingWidget extends Widget {
	
    /**
     * @param integer fid 资源ID
     * @param string tpl 渲染的模板 btn(无统计数)
     */
	public function render($data) {
		$var['tpl'] = 'btn';
		$var['type'] = 'btn';
		is_array($data) && $var = array_merge($var,$data);
		$var['FeedFollowing'] = model('FeedFollowing')->getFeedFollowing($var['fid']);
		$content = $this->renderFile (dirname(__FILE__)."/".$var['tpl'].'.html', $var );
		return $content;
	}	

    /**
	 * 添加关注记录
	 * @return array 关注状态和成功提示
	 */
	public function addFeedFollowing(){
		$return  = array('status'=>0,'data'=>'关注失败,您可能已经关注此资源');
		if(empty($_POST['fid'])){
			$return['data'] = '问题ID不能为空';
			echo json_encode($return);exit();
		}
		$data['feedid'] 	= intval($_POST['fid']);

		// 验证资源是否已经被删除
		$map['feed_id'] = $data['feedid'];
		$map['is_del'] = 0;
		$isExist = model('Feed')->where($map)->count();
		if(empty($isExist)) {
			$return = array('status'=>0, 'data'=>'问题已被删除，收藏失败');
			exit(json_encode($return));
		}
				
		if(model('FeedFollowing')->addFeedFollowing($data)) {
			$Qfeed = model('Feed')->field('following_count')->where('feed_id='.$data['feedid'])->select();
			$QupdData['following_count']=$Qfeed[0]['following_count'] + 1;
			$QupdResult = model('Feed')->where('feed_id='.$data['feedid'])->save($QupdData);
			$feedids=array($data['feedid']);
			model('Feed')->cleanCache($feedids);
			
			$return = array('status'=>1,'data'=>'关注成功');
		} else {
			$return = array('status'=>0,'data'=>'关注失败,您可能已经关注此资源');
		}
		exit(json_encode($return));
	}
	
	/**
	 * 取消关注
	 * @return array 成功取消的状态及错误提示
	 */
	public function delFeedFollowing(){
		$return  = array('status'=>0,'data'=>'取消关注失败');
		if(empty($_POST['fid'])){
			$return['data'] = '问题ID不能为空';
			echo json_encode($return);exit();
		}
		if( model('FeedFollowing')->delFeedFollowing(intval($_POST['fid']))){
			$Qfeed = model('Feed')->field('following_count')->where('feed_id='.$_POST['fid'])->select();
			$QupdData['following_count']=$Qfeed[0]['following_count'] - 1;
			$QupdResult = model('Feed')->where('feed_id='.$_POST['fid'])->save($QupdData);
			$feedids=array(intval($_POST['fid']));
			model('Feed')->cleanCache($feedids);
			
			$return = array('status'=>1,'data'=> '取消成功');
		}else{
			$return = array('status'=>0,'data'=> '取消关注失败');
		}
		exit(json_encode($return));
	}
}	