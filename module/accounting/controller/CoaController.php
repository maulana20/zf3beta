<?php
namespace Accounting\Controller;

use Application\Controller\ParentController;
use Administration\Model\UserLog;
use Administration\Model\User;
use Accounting\Model\Coa;
use Accounting\Model\GroupAccount;

class CoaController extends ParentController
{
	public function indexAction()
	{
		$this->listAction();
	}
	
	public function listAction()
	{
		$this->checkRole('ACCOUNTING');
		$this->checkRole('COA');
		
		$coa = new Coa();
		$request = $this->getRequest();
		
		$coa_list = $coa->getList();
		$content['list'] = $coa_list;
		
		$this->printResponse('success', 'coa list success', $content);
	}
	
	public function listcashbankAction()
	{
		$this->checkRole('ACCOUNTING');
		$this->checkRole('COA');
		
		$coa = new Coa();
		$request = $this->getRequest();
		
		$coa_list = $coa->getListCashBank();
		$content['list'] = $coa_list;
		
		$this->printResponse('success', 'coa list cash bank success', $content);
	}
	
	public function addAction()
	{
		$this->checkRole('COA');
		
		$coa = new Coa();
		$request = $this->getRequest();
		
		$this->printResponse('success', 'Coa add', NULL);
	}
	
	public function ajaxaddAction()
	{
		$userLog = new UserLog();
		try {
			$response = NULL;
			if ($this->isInRole('COA')) {
				$coa = new Coa();
				
				$request = $this->getRequest();
				$coa_code = $request->getPost('code');
				$coa_name = $request->getPost('name');
				$coa_desc = $request->getPost('desc');
				$lod = $request->getPost('lod');
				$groupaccount_id = $request->getPost('groupaccount_id');
				
				if (empty($coa_code)) {
					$response['result'] = 'error';
					$response['reason'] = 'Coa code not found !';
				} else if (empty($coa_name)) {
					$response['result'] = 'error';
					$response['reason'] = 'Coa name not found !';
				} else if (empty($lod)) {
					$response['result'] = 'error';
					$response['reason'] = 'Lod not found !';
				} else if (empty($groupaccount_id)) {
					$response['result'] = 'error';
					$response['reason'] = 'Group account not found !';
				} else {
					if ($coa->isAlready($coa_code)) {
						$response['result'] = 'error';
						$response['reason'] = 'Coa code is already !';
					} else {
						$response['result'] = 'ok';
					}
				}
				
				if ($response['result'] == 'error') $this->printResponse('failed', $response['reason'], $response);
				
				$data = array();
				$data['coa_code'] = $coa_code;
				$data['coa_name'] = $coa_name;
				$data['coa_desc'] = $coa_desc;
				$data['lod'] = $lod;
				$data['groupaccount_id'] = $groupaccount_id;
				$data['user_id'] = $this->session->user_id;
				$data['coa_created'] = time();
				
				$coa->add($data);
				$userLog->add($this->session->user_id, 'Add coa => ' . $coa_code);
				
				$this->printResponse('success', 'coa has add !', NULL);
			}
		} catch (Exception $e) {
			$userLog->add($this->session->user_id, 'Error try (edit coa): '. $e->getMessage());
		}
	}
	
	public function editAction()
	{
		$this->checkRole('COA');
		
		$coa = new Coa();
		$request = $this->getRequest();
		
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) $this->printResponse( 'failed', 'Gagal mendapatkan id', ['flag' => 'alert', 'reason' => 'Gagal mendapatkan id'] );
		
		$list = $coa->getRow($id);
		$content['list'] = $list;
		
		$this->printResponse('success', 'Coa edit', $content);
	}
	
	public function ajaxeditAction()
	{
		$userLog = new UserLog();
		try {
			$response = NULL;
			if ($this->isInRole('COA')) {
				$coa = new Coa();
				
				$request = $this->getRequest();
				$coa_id = $request->getPost('id');
				$coa_code = $request->getPost('code');
				$coa_name = $request->getPost('name');
				$coa_desc = $request->getPost('desc');
				$lod = $request->getPost('lod');
				$groupaccount_id = $request->getPost('groupaccount_id');
				
				$coa_row = $coa->getRow($coa_id);
				
				if (empty($coa_id)) {
					$response['result'] = 'error';
					$response['reason'] = 'Id not found !';
				} else if (empty($coa_code)) {
					$response['result'] = 'error';
					$response['reason'] = 'Coa code not found !';
				} else if (empty($coa_name)) {
					$response['result'] = 'error';
					$response['reason'] = 'Coa name not found !';
				} else if (empty($lod)) {
					$response['result'] = 'error';
					$response['reason'] = 'Lod not found !';
				} else if (empty($groupaccount_id)) {
					$response['result'] = 'error';
					$response['reason'] = 'Group account not found !';
				} else {
					if ($coa_row['coa_code'] != $coa_code && $coa->isAlready($coa_code)) {
						$response['result'] = 'error';
						$response['reason'] = 'Coa code is already !';
					} else {
						$response['result'] = 'ok';
					}
				}
				
				if ($response['result'] == 'error') $this->printResponse('failed', $response['reason'], $response);
				
				$data = array();
				$data['coa_code'] = $coa_code;
				$data['coa_name'] = $coa_name;
				$data['coa_desc'] = $coa_desc;
				$data['lod'] = $lod;
				$data['groupaccount_id'] = $groupaccount_id;
				
				if ($response['result'] == 'ok') {
					$groupaccount = new GroupAccount();
					
					$groupaccount_row = $coa->getRow($coa_row['groupaccount_id']);
					$groupaccount_to_row = $coa->getRow($groupaccount_id);
					
					$description = NULL;
					if ($coa_row['coa_code'] != $coa_code) $description = $coa_row['coa_code'] . ' to ' . $coa_code; 
					if ($coa_row['coa_name'] != $coa_name) $description = $coa_code . ' ' . $coa_row['coa_name'] . ' to ' . $coa_name; 
					if ($coa_row['groupaccount_id'] != $groupaccount_id) $description = $coa_code . ' ' . $groupaccount_row['groupaccount_name'] . ' to ' . $groupaccount_to_row['groupaccount_name']; 
					
					$coa->update($coa_id, $data);
					$userLog->add($this->session->user_id, 'Edit coa => ' . (!empty($description) ? $description : $coa_code));
					
					$this->printResponse('success', 'coa has update !', NULL);
				}
			}
		} catch (Exception $e) {
			$userLog->add($this->session->user_id, 'Error try (edit coa): '. $e->getMessage());
		}
	}
}
