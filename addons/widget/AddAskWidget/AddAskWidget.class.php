<?php
/**
 * 提问发布框
 * @example {:W('AddAsk',array('title'=>'追问', 'feedID'=>$feedID))}
 * @author jason
 * @version TS3.0
 */
class AddAskWidget extends Widget {
	
	private static $rand = 1;
	
	/**
	 * 渲染提问发布框模板
	 * @example
	 * $data['send_type'] string 提问发送类型
	 * $data['app_name'] string 发布提问所在的应用名称
	 * $data['initHtml'] string 发布提问框中的默认内容
	 * $data['cancomment'] integer 是否可以评论 
	 *$data['channelID']  发布到某个频道的id
	 * @param array $data 发布提问框的配置参数
	 * @return string 渲染后的模板内容
	 */	
	public function render($data) {
		$var = array();
		//频道id
		$var['channelID'] = $data['channelID'];
		$var['title'] = $data['title'];
		$var['initHtml'] = '';
		$var['post_event'] ='post_feed';
		$var['cancomment'] = 0;
		is_array($data) && $var = array_merge($var,$data);
		!$var['send_type'] && $var['send_type'] = 'send_weibo';
		$weiboSet = model('Xdata')->get('admin_Config:feed');
		$var['initNums'] = $weiboSet['weibo_nums'];
		$var['weibo_type'] = $weiboSet['weibo_type'];
		$var['weibo_premission'] = $weiboSet['weibo_premission'];
		!$var['type'] && $var['type'] = 'post';
		!$var['app_name'] && $var['app_name'] = 'public';
		!$var['prompt'] && $var['prompt'] = '发布成功';
		$var['time'] = $_SERVER['REQUEST_TIME'];
		$var['topicHtml'] = t($data['topicHtml']).' ';//空格 控制话题提示
		// 获取安装的应用列表
		$var['hasChannel'] = model('App')->isAppNameOpen('channel');
		// 权限控制
		$type = array('face', 'at', 'image', 'video', 'file', 'topic', 'contribute');
		foreach($type as $value) {
			!isset($var['actions'][$value]) && $var['actions'][$value] = true; 
		}
		// 渲染模版
		$content = $this->renderFile(dirname(__FILE__)."/addask.html", $var);
		
		self::$rand++;
		unset($var, $data);
		// 输出数据
		return $content;
	}
}