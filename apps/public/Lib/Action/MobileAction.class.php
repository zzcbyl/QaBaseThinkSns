<?php
/**
 * 手机页面
 * @author zhangzc
 * @version TS3.0
 */
class MobileAction extends Action
{
	public function all()
	{
		// 安全过滤
		$d['type'] = t($_GET['type']) ? t($_GET['type']) : 'all';
		$d['feed_type'] = t($_GET['feed_type']) ? t($_GET['feed_type']) : '';
		$d['feed_key'] = t($_GET['feed_key']) ? t($_GET['feed_key']) : '';
		// 设置标题与关键字信息
		switch($d['type']) {
			case 'question':
				$this->setTitle('我的问题');
				$this->setKeywords('我的问题');
				break;
			case 'answer':
				$this->setTitle('我的答案');
				$this->setKeywords('我的答案');
				break;
			case 'following':
				$this->setTitle('我的关注');
				$this->setKeywords('我的关注');
				break;
			
			case 'channel':
				$this->setTitle('我关注的频道');
				$this->setKeywords('我关注的频道');
				break;
			default:
				$this->setTitle('我的首页');
				$this->setKeywords('我的首页');
		}
		$this->assign($d);
		
		$this->display();
	}
	
}