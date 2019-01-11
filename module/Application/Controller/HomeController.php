<?php
namespace Application\Controller;

use Application\Controller\ParentController;

class HomeController extends ParentController
{
	public function indexAction()
	{
		$this->newsAction();
	}
	
	public function newsAction()
	{
		// $this->checkRole('USER');
		$html = '
		<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-6">
				<p><b>Fitur update saat ini :</b></p>
				<ul>
					<li type="square">Administration - Setting User</li>
					<li type="square">Administration - Cek Log</li>
					<li type="square">Administration - Setting Group</li>
					<li type="square">Accounting - Journal</li>
					<li type="square">Accounting - General Ledger</li>
					<li type="square">Accounting - Trial Balance</li>
					<li type="square">Accounting - Balance Sheet (Account)</li>
					<li type="square">Accounting - Setting Coa</li>
					<li type="square">Finance - General Cash Bank (development)</li>
					<li type="square">Finance - Inner Cash Bank (development)</li>
					<li type="square">Accounting - Posting</li>
					<li type="square">Accounting - Closing</li>
				</ul>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6">
				<p><b>Team Pendukung :</b></p>
				<ul><b>Busines Model</b>
					<li type="square">Pak Jogo</li>
					<li type="square">Team Finance</li>
				</ul>
				<ul><b>Referensi</b>
					<li type="square">GTASS</li>
					<li type="square">Accurate Report</li>
					<li type="square">RITS</li>
				</ul>
				<ul><b>Backend</b>
					<li type="square">ZF3</li>
				</ul>
				<ul><b>Database</b>
					<li type="square">SQL Server</li>
				</ul>
				<ul><b>Frontend</b>
					<li type="square">Vue.js</li>
				</ul>
				<ul><b>Mapping</b>
					<li type="square">Akbar</li>
				</ul>
			</div>
		</div>
		';
		$content['something'] = $html;
		$content['action'] = 'home';
		
		$this->printResponse('success', 'News success', $content);
		exit();
	}
}
