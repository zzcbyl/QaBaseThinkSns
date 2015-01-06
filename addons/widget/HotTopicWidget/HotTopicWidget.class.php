<?php

class HotTopicWidget extends Widget {
	

	private $limitnums   = 10;

	public function render($data) {
		$topic = $this->getHotTopicData();
		
		return $topic;
	}
	
	/**
	获取热门话题
	*/
	private function getHotTopicData()
	{
		$where =' is_audit=1 AND is_del = 0 AND feed_questionid=0 ';
		$list = model('Feed')->getList($where,$this->limitnums,'answer_count desc, publish_time desc');
		$var['data'] = $list['data'];
		
		//print_r($var['data']);

		//渲染模版
		$content = $this->renderFile(dirname(__FILE__)."/HotTopic.html",$var);
		
		return $content;
	}
	
}

?>