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
	
	function getCountSearch($search_txt, $column_choice, $partial, $start_date, $end_date)
	{
		if (!$search_txt) return NULL;
		$search_txt = explode("\n", $search_txt);
		$addChr = ($partial == 1) ? '%' : '';
		
		$select = $this->select();
		$select->from( ['a' => 'tblUserLog'] )
				->columns( ['count' => $this->expression('COUNT(*)')] )
				->join( ['b' => 'tblUser'], 'a.user_id = b.user_id', [])
		;
		
		$where = $this->where();
		switch ($column_choice) {
			case 'user' : foreach ($search_txt as $val) $where->OR->like('user_name', $addChr . $val . $addChr); break;
			default : foreach ($search_txt as $val) $where->OR->like($column_choice, $addChr . $val . $addChr);
		}
		$select->where( $where );
		$select->where('userlog_date >= ' . $start_date);
		$select->where('userlog_date <= ' . $end_date);
		//echo $select->getSqlString(); exit();
		
		$rowset = $this->init('tblUserLog')->selectWith($select)->current();
		
		return $rowset->count;
	}
	
	function getList($search_txt, $column_choice, $partial, $start_date, $end_date, $page, $max_page)
	{
		$result = NULL;
		$search_txt = explode("\n", $search_txt);
		$addChr = ($partial == 1) ? '%' : '';
		
		$select = $this->select();
		$select->from( ['a' => 'tblUserLog'] )
				->join( ['b' => 'tblUser'], 'a.user_id = b.user_id', ['user_name' => 'user_name'] )
				->order('userlog_id DESC')
		;
		
		$where = $this->where();
		switch ($column_choice) {
			case 'user' : foreach ($search_txt as $val) $where->OR->like('user_name', $addChr . $val . $addChr); break;
			default : foreach ($search_txt as $val) $where->OR->like($column_choice, $addChr . $val . $addChr);
		}
		$select->where( $where );
		$select->where('userlog_date >= ' . $start_date);
		$select->where('userlog_date <= ' . $end_date);
		//echo $select->getSqlString(); exit();
		
		$list = (empty($page)) ? $this->init('tblUserLog')->selectWith($select) : $this->paginator($select, $page, $max_page);
		foreach ($list as $value) {
			$result[] = (array) $value;
		}
		
		return $result;
	}
}
