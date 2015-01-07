<?php
/**
 * 第三方关联数据 - 数据对象模型
 * @author jason <yangjs17@yeah.net>
 * @version TS3.0
 */
class LoginLogsModel extends Model {

	protected $tableName = 'login_logs';
	protected $fields = array('login_logs_id','uid',' ip','ctime','_pk'=>'login_logs_id');

	
	
	
}