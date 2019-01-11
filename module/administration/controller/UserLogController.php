<?php
namespace Administration\Controller;

use Application\Controller\ParentController; 
use Administration\Model\UserLog;

class UserLogController extends ParentController
{
	function indexAction() 
	{
		$this->listAction();
	}
	
	function listAction() 
	{
		$this->checkRole('USER_LOG');
		$userLog = new UserLog();
		$request = $this->getRequest();
		$start_date = strtotime($request->getPost('start_date') . ' 00:00:00');
		$end_date = strtotime($request->getPost('end_date') . ' 23:59:59');
		$column_choice = $request->getPost('column_choice', 'userlog_action');
		$option = $request->getPost('option', 'per_pages');
		$search_txt = $request->getPost('search_txt', '');
		$partial = ($request->getPost('partial') == 'false') ? 0 : 1;
		if (!empty($search_txt)) {
			$search = explode("\r", str_replace("\n","\r", $search_txt));
			foreach ($search as $val) {
				if (trim($val) != '') $txt[] = trim($val);
			}
			if (!empty($txt)) $search_txt2 = implode("\n", $txt);
			$in_sql = array('%', '_');
			$in_windowOS = array('*', '?');
			$search_txt = str_ireplace($in_windowOS, $in_sql, $search_txt2);
			$page = $page_list = NULL;
			if ($option == 'per_pages') {
				$countList = $userLog->getCountSearch($search_txt, $column_choice, $partial, $start_date, $end_date);
				$page_list = ceil($countList / MAX_PAGE);
				$page = (int) $this->params()->fromQuery('page', 1);
				if (($page > $page_list) && ($page_list > 0)) $page = $page_list;
				$content['page_list'] = $page_list;
			}
			$list = array();
			$list = $userLog->getList($search_txt, $column_choice, $partial, $start_date, $end_date, $page, MAX_PAGE);
			if ($option != 'per_pages') $countList = count($list);
		}
		$content['page'] = ($option != 'per_pages') ? $page : 1;
		$content['column_choice'] = $column_choice;
		$content['option'] = $option;
		$content['search_txt'] = $search_txt2;
		$content['partial'] = $partial;
		$content['start_date'] = date('d-m-Y', $start_date);
		$content['end_date'] = date('d-m-Y', $end_date);
		
		if (!$search_txt) { $content['caption'] = "SEARCH . . .";
		} else if ($countList > 1) { $content['caption'] = 'Found ' . $countList . ' items';
		} else if (!empty($countList)) { $content['caption'] = 'Found ' . $countList . ' item';
		} else { $content['caption'] = 'There are no results to display.'; }
		
		$content['views'] = 'Searchlog';
		$content['list'] = $list;
		$content['bottom'] = 'Bottom';
		
		$this->printResponse('success', 'user log data', $content);
	}

}

