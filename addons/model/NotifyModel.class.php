<?php
/**
 * 消息通知节点模型 - 数据对象模型
 * @example
 * 使用Demo：
 * model('Notify')->sendNotify(14983,'register_active',array('siteName'=>'SOCIAX','name'=>'yangjs'));
 * @author jason <yangjs17@yeah.net>
 * @version TS3.0
 */
class NotifyModel extends Model {

	protected $tableName = 'notify_node';
	protected $fields = array(0=>'id',1=>'node',2=>'nodeinfo',3=>'appname',4=>'content_key',5=>'title_key',6=>'send_email',7=>'send_message',8=>'type');

	protected $_config = array();			// 配置字段

	/**
	 * 初始化方法，获取站点名称、系统邮箱、找回密码的URL
	 * @return void
	 */
	public function _initialize() {
		$site = empty($GLOBALS['ts']['site']) ? model('Xdata')->get('admin_Config:site') : $GLOBALS['ts']['site'];
		$this->_config['site'] = $site['site_name'];
		$this->_config['site_url'] = SITE_URL;
		$this->_config['kfemail'] = 'mailto:'.$site['sys_email'];
		$this->_config['findpass'] = U('public/Passport/findPassword');
	}

	/**
	 * 获取节点列表
	 * @return array 节点列表数据
	 */
	public function getNodeList() {
		// 缓存处理
		if($list = static_cache('notify_node')) {
			return $list;
		}
		if(($list = model('Cache')->get('notify_node')) == false) {
			$list = $this->getHashList('node', '*');
			model('Cache')->set('notify_node', $list);
		}
		static_cache('notify_node', $list);

		return $list;
	}

	/**
	 * 保存节点配置
	 * @param array $data 节点修改信息
	 * @return boolean 是否保存成功
	 */
	public function saveNodeList($data) {
		foreach($data as $k => $v) {
			$m = $s = array();
			$m['node'] = $k;
			$s['send_email'] = intval($v['send_email']);
			$s['send_message'] = intval($v['send_message']);
			$this->where($m)->save($s);
		}

		$this->cleanCache();
		return true;
	}

	/**
	 * 清除消息节点缓存
	 * @return void
	 */
	public function cleanCache() {
		model('Cache')->rm('notify_node');
		//更新语言包
		model('Lang')->initSiteLang();
	}

	/**
	 * 保存模版设置
	 * @param array $data 模板数据
	 * @return boolean 是否保存成功
	 */
	public function saveTpl($data) {
		foreach($data['lang'] as $k => $v) {
			if(empty($k)) continue;
			
			$m['key'] = $k;
			model('Lang')->where($m)->save($v);
		}
		$this->cleanCache();
		return true;
	}

	/**
	 * 分组返回指定用户的系统消息列表
	 * @param integer $uid 用户ID
	 * @return array 分组返回指定用户的系统消息列表
	 */
	public function getMessageList($uid) {
		$map['uid'] = $uid;
		$field = 'MAX(id) AS id, appname';
		if(!$list['group'] = D('')->table($this->tablePrefix.'notify_message')->where($map)->field($field)->group('appname')->findAll()) {
			return array();
		}
		$map['is_read'] = 0;
		$field = 'COUNT(id) AS nums,appname';
		$list['groupCount'] = D('')->table($this->tablePrefix.'notify_message')->where($map)->field($field)->group('appname')->findAll();
		foreach($list['group'] as $v) {
			$list['appInfo'][$v['appname']] = model('App')->getAppByName($v['appname']);
			$idHash[] = $v['id'];
		}
		$m['id'] = array('IN', $idHash);
		$list['messageInfo'] = D('')->table($this->tablePrefix.'notify_message')->where($m)->getHashList('id', '*');
		return $list;
 	}

 	/**
 	 * 获取指定应用指定用户下的系统消息列表
 	 * @param string $app 应用Key值
 	 * @param integer $uid 用户ID
 	 * @return array 指定应用指定用户下的系统消息列表
 	 */
	public function getMessageDetail($app, $uid) {		
		$map['appname'] = $app;
		$map['uid'] = $uid;
		$list = D('')->table($this->tablePrefix.'notify_message')->where($map)->order('id DESC')->findPage(20);
		return $list;
	}

	/**
	 * 更改指定用户的消息从未读为已读
	 * @param integer $uid 用户ID
	 * @param string $appname 应用Key值
	 * @return mix 更改失败返回false，更改成功返回消息ID
	 */
	public function setRead($uid, $appname = '') {
		$map['uid'] = $uid;
		!empty($appname) && $map['appname'] = $appname;
		$s['is_read'] = 1;
		return D('')->table($this->tablePrefix.'notify_message')->where($map)->save($s);
	}

	/**
	 * 获取指定用户未读消息的总数
	 * @param integer $uid 用户ID
	 * @return integer 指定用户未读消息的总数
	 */
	public function getUnreadCount($uid){
		$map['uid'] = $uid;
		$map['is_read'] = 0;
		return D('')->table($this->tablePrefix.'notify_message')->where($map)->count();
	}
	
	/**
	 * 发送消息入口，对已注册用户发送的消息都可以通过此函数
	 * @param array $toUid 接收消息的用户ID数组
	 * @param string $node 节点Key值
	 * @param array $config 配置数据
	 * @param intval $from 消息来源用户的UID
	 * @return void
	 */
	public function sendNotify($toUid, $node, $config, $from) {
		empty($config) && $config = array();
		$config = array_merge($this->_config,$config);
		
		$nodeInfo = $this->getNode($node);
		if(!$nodeInfo) {
			return false;
		}
		
		!is_array($toUid) && $toUid = explode(',', $toUid);
		$userInfo = model('User')->getUserInfoByUids($toUid);

		$data['node'] = $node;
		$data['appname'] = $nodeInfo['appname'];
		$data['title'] = L($nodeInfo['title_key'], $config);
		$data['body'] = L($nodeInfo['content_key'], $config);
		
		foreach($userInfo as $v) {
			$data['uid'] = $v['uid'];
			!empty($nodeInfo['send_message']) && $this->sendMessage($data);
			$data['email'] = $v['email'];
			
			if(!empty($nodeInfo['send_email'])){
				if(in_array($node,array('atme','comment','new_message'))){
					$map['key'] = $node.'_email';
					$map['uid'] = $v['uid'];
					$isEmail = D('user_privacy')->where($map)->getField('value');
					$isEmail==0 && $this->sendEmail($data);
				}else{
					$this->sendEmail($data);
				}
			}
		}
	}
	
	//TS2的兼容方法 同 sendNotify
	public function send($toUid, $node, $config, $from) {
		return $this->sendNotify($toUid, $node, $config, $from);
	}

	/**
	 * 获取指定节点信息
	 * @param string $node 节点Key值
	 * @return array 指定节点信息
	 */
	public function getNode($node) {
		$list = $this->getNodeList();
		return $list[$node];
	}
	
	/**
	 * 获取指定节点的详细信息
	 * @param string $node 节点Key值
	 * @param array $config 配置数据
	 * @return array 指定节点的详细信息
	 */
	public function getDataByNode($node, $config) {
		empty($config) && $config = array();
		$config = array_merge($this->_config, $config);
		$nodeInfo = $this->getNode($node);
		$d['title'] = L($nodeInfo['title_key'], $config);
		$d['body'] = L($nodeInfo['content_key'], $config);
		return $d;
	}

	/**
	 * 发送邮件，添加到消息队列数据表中
	 * @param array $data 消息的相关数据
	 * @return mix 添加失败返回false，添加成功返回新数据的ID
	 */
	public function sendEmail($data) {
		if(empty($data['email'])) {
			// TODO:邮箱格式验证
			return false;
		} 

		// TODO:用户隐私设置判断
		$s['uid'] = intval($data['uid']);
		$s['node'] = t($data['node']);
		$s['email'] = t($data['email']);
		$s['appname'] = t($data['appname']);
		$s['is_send'] = $s['sendtime'] = 0;
		$s['title'] = t($data['title']);
		$body = html_entity_decode($data['body']);
		$site = model('Xdata')->get('admin_Config:site');
		$s['body']= '<style>a.email_btn,a.email_btn:link,a.email_btn:visited{background:#0F8CA8;padding:5px 10px;color:#fff;width:80px;text-align:center;}</style><div style="width:700px;border:#C22B2B solid 2px;margin:0 auto; line-height:30px;"><div style="color:#bbb;background:#C22B2B;padding:10px;overflow:hidden;zoom:1">
					<div style="float:left;overflow:hidden;position:relative"><a><img style="border:0 none" src="'.$GLOBALS['ts']['site']['logo'].'"></a></div></div>
					<div style="background:#fff;padding:20px;min-height:300px;position:relative">		<div style="font-size:14px;">			
						            	<p style="padding:0;margin:0;font-size:14px">'.$body.'</p>
						            </div></div><div style="background:#fff;">
			            <div style="line-height:18px;text-align:center; height:35px;"><p style="color:#999;font-size:12px">'.$site['site_footer'].'</p></div>
			        </div></div>';
		$s['ctime'] = time();
		
		model('Mail')->send_email($s['email'],$s['title'],$s['body']);
		return D('')->table($this->tablePrefix.'notify_email')->add($s);
	}
	
	/**
	 * 发送邮件，添加到消息队列数据表中
	 * @param array $data 消息的相关数据
	 * @return mix 添加失败返回false，添加成功返回新数据的ID
	 */
	public function sendActivityEmail($data) {
		if(empty($data['email'])) {
			// TODO:邮箱格式验证
			return false;
		} 

		// TODO:用户隐私设置判断
		$s['uid'] = intval($data['uid']);
		$s['node'] = t($data['node']);
		$s['email'] = t($data['email']);
		$s['appname'] = t($data['appname']);
		$s['is_send'] = $s['sendtime'] = 0;
		$s['title'] = t($data['title']);
		$body = html_entity_decode($data['body']);
		$site = model('Xdata')->get('admin_Config:site');
		$s['body']= '<style>a.email_btn,a.email_btn:link,a.email_btn:visited{background:#0F8CA8;padding:5px 10px;color:#fff;width:80px;text-align:center;}</style><div style="width:700px;border:#C22B2B solid 2px;margin:0 auto; line-height:30px;"><div style="color:#bbb;background:#C22B2B;padding:10px;overflow:hidden;zoom:1">
					<div style="float:left;overflow:hidden;position:relative"><a><img style="border:0 none" src="'.$GLOBALS['ts']['site']['logo'].'"></a></div></div>
					<div><img src="http://www.luqinwenda.com/addons/theme/stv1/_static/image/quna_20140501.jpg"></div>
					<div style="background:#fff;padding:20px;min-height:300px;position:relative">		<div style="font-size:16px;">			
						            	<p style="padding:0;margin:0;font-size:16px">'.$body.'</p>
						            </div></div><div style="background:#fff;">
			            <div style="line-height:18px;text-align:center; height:35px;"><p style="color:#999;font-size:12px">'.$site['site_footer'].'</p></div>
			        </div></div>';
		$s['ctime'] = time();
		
		model('Mail')->send_email($s['email'],$s['title'],$s['body']);
		return D('')->table($this->tablePrefix.'notify_email')->add($s);
	}

	/**
	 * 发送系统消息，给指定用户
	 * @param array $data 发送系统消息相关数据
	 * @return mix 发送失败返回false，发送成功返回新的消息ID
	 */
	public function sendMessage($data) {
		if(empty($data['uid'])) {
			return false;
		}
		$s['uid'] = intval($data['uid']);
		$s['node'] = t($data['node']);
		$s['appname'] = t($data['appname']);
		$s['is_read'] = 0;
		$s['title'] = t($data['title']);
		$s['body'] = h($data['body']);
		$s['ctime'] = time();
		return D('')->table($this->tablePrefix.'notify_message')->add($s);
	}

	/**
	 * 删除通知
	 * @param integer $id 通知ID
	 * @return mix 删除失败返回false，删除成功返回删除的通知ID
	 */
	public function deleteNotify($id) {
		$map['uid'] = $GLOBALS['ts']['mid'];		// 仅仅只能删除登录用户自己的通知
		$map['id'] = intval($id);
		return D('')->table($this->tablePrefix.'notify_message')->where($map)->delete();	
	}

	/**
	 * 发送邮件队列中的数据，每次执行默认发送10封邮件
	 * @param integer $sendNums 发送邮件的个数，默认为10
	 * @return array 返回取出的数据个数与实际发送邮件的数据个数
	 */
	public function sendEmailList($sendNums = 10) {
		set_time_limit(0);
		$mail = model('Mail');
		$find['is_send'] = 0;
		$list = D('')->table($this->tablePrefix.'notify_email')->where($find)->limit($sendNums)->order('id ASC')->findAll();
		$r['count'] = count($list);
		$r['nums'] = 0;
		ob_start();
		foreach($list as $v) {
			$map['id'] = $v['id'];
			$save['is_send'] = 1;
			$save['sendtime'] = time();
			D('')->table($this->tablePrefix.'notify_email')->where($map)->save($save);
			$r['nums']++;
			$mail->send_email($v['email'], $v['title'], $v['body']);
			// TODO:现在默认为全部发送成功，如果后期发送失败要修改回来的话再根据$v['id']修改is_send
		}
		ob_end_clean();
		return $r;
	}

	/**
	 * 发送系统消息，给用户组或全站用户
	 * @param array $user_group 用户组ID
	 * @param string $content 发送信息内容
	 * @return boolean 是否发送成功
	 */
	public function sendSysMessae($user_group, $content) {
    	set_time_limit(0);
    	$ctime = time();
    	$user_group = intval($user_group);
    	if(!empty($user_group)) { 
    		// 判断组中是否存在用户
    		$m['user_group_id'] = $user_group;
    		$c = model('UserGroupLink')->where($m)->count();
    		if($c > 0) {
    			// 针对用户组
    			$sql = "INSERT INTO ".$this->tablePrefix."notify_message (`uid`,`node`,`appname`,`title`,`body`,`ctime`,`is_read`)
    				SELECT uid,'sys_notify','public','','{$content}','{$ctime}','0' 
    				FROM ".$this->tablePrefix."user_group_link WHERE user_group_id = {$user_group} ";
    		} else {
    			return true;
    		}		
    	} else {
    		// 全站用户
    		$sql = "INSERT INTO ".$this->tablePrefix."notify_message (`uid`,`node`,`appname`,`title`,`body`,`ctime`,`is_read`)
    				SELECT uid,'sys_notify','public','','{$content}','{$ctime}','0' 
    				FROM ".$this->tablePrefix."user WHERE is_del=0 ";
    	}

    	D('')->query($sql);
    	return true;
	}

	/*** API使用 ***/
	/**
	 * 返回指定用户的未读消息列表，没有since_id 和 max_id 的时候返回未读消息
	 * @param integer $uid 用户ID
	 * @param integer $since_id 开始的组件ID，默认为0
	 * @param integer $max_id 最大主键ID，默认为0
	 * @param integer $limit 结果集数目，默认为20
	 * @return array 指定用户的未读消息列表
	 */
	public function getUnreadListForApi($uid, $since_id = 0, $max_id = 0, $limit = 20) {
		// 未读消息
		$map['uid'] = $uid;
		$map['is_read'] = 0;
		$list = D('')->table($this->tablePrefix.'notify_message')->where($map)->order('id DESC')->findAll();
		$count = count($list);
		
		if(!empty($since_id) || empty($max_id)) {
			$where =' uid = '.$uid;
			if(!empty($since_id)) {
				$where .= ' AND id > '.$since_id;
			}
			if(!empty($max_id)) {
				$where .= ' AND id < '.$max_id;
			}
			$list = D('')->table($this->tablePrefix.'notify_message')->where($where)->order('id DESC')->limit($limit)->findAll();
		}

		return array('list'=>$list,'count'=>$count);
	}


		/**
	 * 系统对用户发送通知
	 * @param string|int|array $receive 接收人ID 多个时以英文的","分割或传入数组
	 * @param string           $type    通知类型, 必须与模版的类型相同, 使用下划线分割应用.
	 * 					   				如$type = "weibo_follow"定位至/apps/weibo/Language/cn/notify.php的"weibo_follow"
	 * @param array            $data
	 * @return void
	 */
	public function sendIn( $receive , $type , $data  ) {
		return $this->__put( $receive , $type , $data  , 0 , true );
	}
	
		/**
	 +----------------------------------------------------------
	 * Description 通知发送处理
	 +----------------------------------------------------------
	 * @author Nonant nonant@thinksns.com
	 +----------------------------------------------------------
	 * @param $type    通知类型
	 * @param $receive 通知接收者的用户ID,类型可为 数字、字符串、数组
	 * @param $title   通知标题
	 * @param $body    通知内容
	 * @param $from    通知发送者UID
	 * @param $system  是否为系统通知
	 +----------------------------------------------------------
	 * @return Boolen
	 +----------------------------------------------------------
	 * Create at  2010-9-13 下午04:24:53
	 +----------------------------------------------------------
	 */
	private function __put($receive,$type,$data,$from=0,$system=false) {
		global $ts;
		$receive = $this->_parseUser( $receive ); if(!$receive) return false;
		$from = ( $system==false &&  $from==0 ) ? $ts['user']['uid'] : $from ;
		$data      = addslashes(serialize( $data ));
		$time       = time();

		//优化大批量发送通知，讲数据切割处理，每次插入100条
		$receive	=	array_chunk($receive, 100)  ;
		foreach ($receive as $receive_chunck){

			foreach ($receive_chunck as $k=>$v){
				if($v==$from) continue;
				$sqlArr[] = "($from,$v,'$type','$data',$time)";
			}

			if( $sqlArr ){
				$sql = "INSERT INTO ".C('DB_PREFIX')."notify (`from`,`receive`,`type`,`data`,`ctime`) values ".implode(',',$sqlArr);
				$result[] = M('Notify')->execute($sql);

			}

			unset($sql,$sqlArr,$receive_chunck);
		}

		return $result;
	}

		//解析传入的用户ID
	private function _parseUser($touid){
		if( is_numeric($touid) ){
			$sendto[] = $touid;
		}elseif ( is_array($touid) ){
			$sendto = $touid;
		}elseif (strpos($touid,',') !== false){
			$touid = array_unique(explode(',',$touid));
			foreach ($touid as $key=>$value){
				$sendto[] = $value;
			}
		}else{
			$sendto = false;
		}
		return $sendto;
	}


}	