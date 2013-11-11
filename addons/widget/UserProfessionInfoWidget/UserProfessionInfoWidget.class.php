<?php
/**
 * 微博列表
 * @example {:W('UserProfessionInfo',array('mid'=>$mid, 'uid'=>$uid))}
 * @author jason
 * @version TS3.0
 */
class UserProfessionInfoWidget extends Widget {

	/**
	 * @param string type 获取哪类微博 following:我关注的 space：
	 * @param string feed_type 微博类型
	 * @param string feed_key 微博关键字
	 * @param integer fgid 关注的分组id
	 * @param integer gid 群组id
	 * @param integer loadnew 是否加载更多 1:是  0:否
	 */
	public function render($data) {
		$mid = intval ( $data ['mid'] );
		$uid = intval ( $data ['uid'] );
		$ProfessionList = model('UserProfession')->getUserProfessionList($uid);
		$ProfessionList['data']=$ProfessionList;
		//print_r($ProfessionList);

		$year=array();
		for ($i=60; $i>=0; $i--) {
			if(intval(1990+$i)>intval(date("Y")))
				continue;
			$year[$i]=array(val=>(1990+$i));
		}
		$ProfessionList['BeginYear']=$year;
		$ProfessionList['mid']=$mid;
		$ProfessionList['uid']=$uid;
		// 渲染模版
		$content = $this->renderFile(dirname(__FILE__)."/professioninfo.html", $ProfessionList);
		// 输出数据
		return $content;
	}
	
	
}