<?php
/**
 * 我的关注Widget
 * @example {:W('Following', array('uid'=>$mid))}
 * @author zhangzc 
 * @version 1.0 
 */
class FollowingWidget extends Widget {
	/**
	 * 模板渲染
	 * @param array $data 相关数据
	 */
	public function render($data) {
		$var['uid'] = intval($data['uid']);

		// 加载关注列表
		$sidebar_following_list = model ( 'Follow' )->getFollowingList ( $var['uid'], null, 12);

		foreach($sidebar_following_list['data'] as $v => $vv)
		{
			$user_info = model ( 'User' )->getUserInfo ( $vv['fid'] );
			$vv['user_info']=$user_info;
			$sidebar_following_list['data'][$v]=$vv;
		}
		
		
		// 渲染模版
		$content = $this->renderFile(dirname(__FILE__)."/following.html", $sidebar_following_list);
		// 输出数据
		return $content;

	}
}
