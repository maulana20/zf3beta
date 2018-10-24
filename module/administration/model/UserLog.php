<?php
namespace Administration\Model;

use Application\Model\Versa_Gateway_Adapter;

class UserLog extends Versa_Gateway_Adapter
{
	function add($user_id, $action)
	{
		$data = array();
		$data['user_id'] = $user_id;
		$data['userlog_date'] = time();
		$data['userlog_action'] = $action;
		$data['userlog_ip_address'] = $_SERVER["REMOTE_ADDR"];
		
		$this->init('tblUserLog')->insert($data);
	}
}
