<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\Permissions\Acl\Acl;;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Administration\Model\Group;
use Administration\Model\User;

define('MAX_PAGE', 10);
define('EXPIRED', 900);
define('VERSION', '1.0');

define('AGENT_CLIENT', 'demo');
define('URL_LOGIN', 'http://atris.vpstiket.com');
define('TRAVEL_NAME', 98);
define('TITLE_SITE', 'VAS');
define('DOMAIN_AVAILABLE', 'http://client.vpstiket.com~http://m-atris.vpstiket.com');

class ParentController extends AbstractActionController
{
	public $session = NULL;
	public $view = NULL;
	public $menu = array(
		array('caption' => 'File', 'href' => '#', 'access' => 'FILE', 'node' => array(
			array('caption' => 'Home Page', 'href' => '#', 'onclick' => "loadView(event,'/home/homepage');", 'access' => ''),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'Export to', 'href' => '#', 'access' => 'FILE_EXPORT', 'node' => array(
				array('caption' => 'Office Word', 'href' => '#', 'onclick' => "popUpWin('/export/word', 300, 125);", 'access' => ''),
				array('caption' => 'Office Excel', 'href' => '#', 'onclick' => "popUpWin('/export/excel', 300, 125);", 'access' => ''),
			)),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'Control', 'href' => '#', 'access' => 'RCONTROL', 'node' => array(
				array('caption' => 'Domestic Airlines', 'href' => '#', 'onclick' => "loadView(event, '/controldomestic/airlines');", 'access' => ''),
				array('caption' => 'Domestic Airlines 2', 'href' => '#', 'onclick' => "loadView(event, '/controldomestic/airlinesrc2');", 'access' => ''),
			)),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'Log out', 'href' => '/admin/logout', 'access' => ''),
		)),
		array('caption' => 'Agents', 'href' => '#', 'access' => 'AGENTS', 'node' => array(
			array('caption' => 'News for agents', 'href' => '#', 'onclick' => "loadView(event,'/home/newsagent');", 'access' => ''),
			array('caption' => 'News from developer', 'href' => '#', 'onclick' => "loadView(event,'/home/newsstaff');", 'access' => 'SUPER_USER'),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'Airlines', 'href' => '#', 'access' => 'BOOKING', 'node' => array(
				array('caption' => 'AirAsia Issued', 'href' => '#', 'onclick' => "loadView(event, '/bookingissued/list');", 'access' => 'ISSUED'),
				array('caption' => 'Booking', 'href' => '#', 'onclick' => "loadView(event,'/bookingairlines/list');", 'access' => ''),
				array('caption' => 'Lion Issued', 'href' => '#', 'onclick' => "loadView(event, '/bookingissued/listlion');", 'access' => ''),
				array('caption' => 'Booking V2', 'href' => '#', 'onclick' => "loadView(event,'/bookairlines/list');", 'access' => ''),
				array('caption' => 'Ticketing Queue', 'href' => '#','onclick' => "loadView(event,'/ticketingairlines/list');", 'access' => 'TICKETING'),
				array('caption' => 'Manage Ticket', 'href' => '#', 'onclick' => "loadView(event,'/manageairlines/list');", 'access' => 'MANAGE'),
			)),
			array('caption' => 'International IATA', 'href' => '#', 'access' => '', 'node' => array(
				array('caption' => 'Booking', 'href' => '#', 'onclick' => "loadView(event, '/bookinginternational/list');", 'access' => ''),
				array('caption' => 'Multiple City Booking', 'href' => '#', 'onclick' => "loadView(event, '/bookinginternational/multiplecitylist');", 'access' => ''),
			)),
			array('caption' => 'Waterb', 'href' => '#', 'access' => '', 'node' => array(
				array('caption' => 'Booking', 'href' => '#', 'onclick' => "loadView(event,'/bookingairlines/listwaterb');", 'access' => ''),
			)),
			array('caption' => 'Kereta Api', 'href' => '#', 'access' => 'KAI_ISSUED', 'node' => array(
				array('caption' => 'Kai Issued', 'href' => '#', 'onclick' => "loadView(event, '/bookingissued/listkai');", 'access' => 'KAI_ISSUED'),
				array('caption' => 'Beli', 'href' => '#', 'onclick' => "loadView(event, '/bookkai/list');", 'access' => 'KAI_ISSUED'),
			)),
			array('caption' => 'Hotel', 'href' => '#', 'access' => 'HOTEL_ISSUED', 'node' => array(
				array('caption' => 'Issued', 'href' => '#', 'onclick' => "loadView(event,'/bookinghotel/list');", 'access' => 'HOTEL_ISSUED'),
				)),
			array('caption' => 'Tour', 'href' => '#', 'access' => '', 'node' => array(
				//array('caption' => 'Package', 'href' => '#', 'onclick' => "loadView(event,'/tour/package');", 'access' => ''),
				//array('caption' => 'Tour Dalam Negeri', 'href' => '#', 'onclick' => "loadView(event,'/tour/package/tag/dalamnegeri');", 'access' => ''),
				array('caption' => 'Tour Luar Negeri', 'href' => '#', 'onclick' => "loadView(event,'/tour/package/tag/luarnegeri');", 'access' => ''),
				array('caption' => 'Tour Dari Negara Ke Negara', 'href' => '#', 'onclick' => "loadView(event,'/tour/package/tag/negarakenegara');", 'access' => ''),
				array('caption' => 'Tour Premium', 'href' => '#', 'onclick' => "loadView(event,'/tour/package/tag/premium');", 'access' => ''),
				array('caption' => 'Backpacker', 'href' => '#', 'onclick' => "loadView(event,'/tour/package/tag/backpacker');", 'access' => ''),
				array('caption' => 'Holyland', 'href' => '#', 'onclick' => "loadView(event,'/tour/package/tag/holyland');", 'access' => ''),
				)),
			array('caption' => 'Pembelian', 'href' => '#', 'access' => 'AGENTS_VOUCHER', 'node' => array(
				// array('caption' => 'Order', 'href' => '#', 'onclick' => "loadView(event,'/orderpulsa/list');", 'access' => 'VOUCHER_ISSUED'),
				array('caption' => 'Pulsa', 'href' => '#', 'onclick' => "loadView(event,'/ordervti/list');", 'access' => 'VOUCHER_ISSUED'),
				array('caption' => 'Top Up', 'href' => '#', 'onclick' => "loadView(event,'/ordervti/listtopup');", 'access' => 'TOPUP_SALDO'),
				array('caption' => 'PLN Prabayar', 'href' => '#', 'onclick' => "loadView(event,'/ordervti/listplnpra/');", 'access' => 'VOUCHER_ISSUED'),
				array('caption' => 'Voucher Game', 'href' => '#', 'onclick' => "loadView(event,'/ordervti/listgame');", 'access' => 'VOUCHER_ISSUED'),
				array('caption' => 'Manage', 'href' => '#', 'onclick' => "loadView(event,'/managepulsa/list');", 'access' => 'VOUCHER_MANAGE'),
				array('caption' => 'Manage Pending', 'href' => '#', 'onclick' => "loadView(event,'/managepulsapending/list');", 'access' => 'VOUCHER_MANAGE_PENDING'),
				)),
			array('caption' => 'Pembayaran', 'href' => '#', 'access' => 'AGENTS_PPOB', 'node' => array(
				array('caption' => 'PDAM', 'href' => '#', 'onclick' => "loadView(event,'/orderppob/list/type/PDAM');", 'access' => 'PPOB_PDAM'),
				array('caption' => 'PLN Pascabayar', 'href' => '#', 'onclick' => "loadView(event,'/orderppob/list/type/PLN POSTPAID');", 'access' => 'PPOB_PLN'),
				array('caption' => 'GSM/CDMA Pascabayar', 'href' => '#', 'onclick' => "loadView(event,'/orderppob/list/type/TELEKOMUNIKASI');", 'access' => 'PPOB_TELEKOMUNIKASI'),
				array('caption' => 'Telkom Group', 'href' => '#', 'onclick' => "loadView(event,'/orderppob/list/type/TELKOM');", 'access' => 'PPOB_TELKOM'),
				array('caption' => 'TV Kabel', 'href' => '#', 'onclick' =>  "loadView(event,'/orderppob/list/type/TV Berlangganan');", 'access' => 'PPOB_TVKABEL'),
				array('caption' => 'BPJS Kesehatan', 'href' => '#', 'onclick' => "loadView(event,'/orderppob/list/type/BPJSKS');", 'access' => 'PPOB_BPJSKS'),
				array('caption' => 'Multifinance', 'href' => '#', 'onclick' => "loadView(event,'/orderppob/list/type/MULTIFINANCE');", 'access' => 'PPOB_MULTIFINANCE'),
				array('caption' => 'Manage Pembayaran', 'href' => '#', 'onclick' => "loadView(event,'/managepayment/list');", 'access' => 'PPOB_MANAGE'),
				)),
			array('caption' => 'Tiket', 'href' => '#', 'access' => '', 'node' => array(
				array('caption' => 'WATERBOM', 'href' => '#', 'onclick' => "loadView(event,'/bookingthemepark/list');", 'access' => ''),
				array('caption' => 'XXI', 'href' => '#', 'onclick' => "loadView(event,'/orderppob/underdevelopment');", 'access' => ''),
				)),
			array('caption' => 'Transportasi', 'href' => '#', 'access' => 'AGENTS_PPOB', 'node' => array(
				array('caption' => 'TAXI', 'href' => '#', 'onclick' => "loadView(event,'/orderppob/underdevelopment');", 'access' => ''),
				array('caption' => 'OJEK', 'href' => '#', 'onclick' => "loadView(event,'/orderppob/underdevelopment');", 'access' => ''),
				)),
			array('caption' => 'Haji & Umroh', 'href' => '#', 'access' => 'AGENTS_PPOB', 'node' => array(
				array('caption' => 'Haji', 'href' => '#', 'onclick' => "loadView(event,'/orderppob/underdevelopment');", 'access' => ''),
				array('caption' => 'Umroh', 'href' => '#', 'onclick' => "loadView(event,'/orderppob/underdevelopment');", 'access' => ''),
				)),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'Booking Search', 'href' => '#', 'access' => '', 'node' => array(
				array('caption' => 'Airlines', 'href' => '#', 'onclick' => "loadView(event,'/ticketingairlines/search');", 'access' => ''),
				array('caption' => 'International', 'href' => '#', 'onclick' => "loadView(event,'/ticketingairlines/searchinternational');", 'access' => ''),
				array('caption' => 'Waterb', 'href' => '#', 'onclick' => "loadView(event,'/ticketingairlines/searchwaterb');", 'access' => ''),
				array('caption' => 'Kai', 'href' => '#', 'onclick' => "loadView(event,'/ticketingairlines/searchkai');", 'access' => ''),
				array('caption' => 'Hotel', 'href' => '#', 'onclick' => "loadView(event,'/ticketinghotel/search');", 'access' => ''),
				array('caption' => 'Theme Park', 'href' => '#', 'onclick' => "loadView(event,'/ticketingthemepark/search');", 'access' => ''),
				array('caption' => 'Tour', 'href' => '#', 'onclick' => "loadView(event,'/ticketingtours/search');", 'access' => ''),
			)),
			array('caption' => 'Search Voucher', 'href' => '#', 'onclick' => "loadView(event,'/searchvoucher/list');", 'access' => 'VOUCHER_ISSUED'),
			array('caption' => 'Search Pembayaran', 'href' => '#', 'onclick' => "loadView(event,'/searchpayment/list');", 'access' => ''),
			array('caption' => 'Retrieve', 'href' => '#', 'access' => '', 'node' => array(
				array('caption' => 'Garuda Gerai', 'href' => '#', 'onclick' => "loadView(event,'/retrieve/garudagerai');", 'access' => ''),
			)),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'Reschedule Request', 'href' => '#', 'onclick' => "loadView(event,'/ticketingairlines/addrescheduleb');", 'access' => ''),
			array('caption' => 'Refund Request', 'href' => '#', 'onclick' => "loadView(event,'/ticketingairlines/addrefundb');", 'access' => ''),
		)),
		array('caption' => 'Reports', 'href' => '#', 'access' => 'REPORTS', 'node' => array(
			array('caption' => 'Cash & Loan Flow', 'href' => '#', 'onclick' => "loadView(event,'/cashflow/list');", 'access' => 'CASHFLOW'),
			array('caption' => 'Staff Cash Flow', 'href' => '#', 'onclick' => "loadView(event,'/depositforstaff/listforstaff');", 'access' => 'SUPER_USER'),
			array('caption' => 'Bank Cash Flow', 'href' => '#', 'onclick' => "loadView(event,'/cashflow/bank');", 'access' => 'SUPER_USER'),
			array('caption' => 'B2C Payment', 'href' => '#', 'onclick' => "loadView(event,'/b2cpayment/list');", 'access' => 'SUPER_USER'),
			array('caption' => 'Topup OCBC', 'href' => '#', 'onclick' => "loadView(event,'/topup/searchapp');", 'access' => 'TOPUP_OCBC'),
			array('caption' => 'Topup OCBC old', 'href' => '#', 'onclick' => "loadView(event,'/topup/searchappold');", 'access' => 'TOPUP_OCBC'),
			array('caption' => 'Topup Maybank', 'href' => '#', 'onclick' => "loadView(event,'/maybankva/list');", 'access' => 'TOPUP_MAYBANK'),
			array('caption' => 'NicePay', 'href' => '#', 'onclick' => "loadView(event,'/ionpay/list');", 'access' => 'REPORT_NICEPAY'),
			array('caption' => 'Kartuku', 'href' => '#', 'onclick' => "loadView(event,'/kartuku/list');", 'access' => 'REPORT_KARTUKU'),
			array('caption' => 'Hotel', 'href' => '#', 'access' => 'HOTEL_ISSUED', 'node' => array(
				array('caption' => 'Cancel List', 'href' => '#', 'onclick' => "loadView(event,'/bookinghotel/cancellist');", 'access' => 'HOTEL_ISSUED'),
			)),
			array('caption' => 'Summary', 'href' => '#', 'access' => 'SUMMARY', 'node' => array(
				array('caption' => 'Info', 'href' => '#', 'onclick' => "loadView(event,'/summary/list');", 'access' => ''),
				array('caption' => 'Info Voucher', 'href' => '#', 'onclick' => "loadView(event,'/managepulsa/summary');", 'access' => ''),
				array('caption' => 'Info Deposit', 'href' => '#', 'onclick' => "loadView(event,'/summary/infodeposit');", 'access' => ''),
				array('caption' => 'Total Transaction', 'href' => '#', 'onclick' => "loadView(event,'/summary/transactionlist');", 'access' => ''),
				array('caption' => 'Total Bonus', 'href' => '#', 'onclick' => "loadView(event,'/summary/totalbonus');", 'access' => ''),					
				array('caption' => 'Total (NTA, Publish, Bonus)', 'href' => '#', 'onclick' => "loadView(event,'/summary/totalpnb');", 'access' => 'SUPER_USER'),
				array('caption' => 'Fraud', 'href' => '#', 'onclick' => "loadView(event,'/fraud/list');", 'access' => ''),
				array('caption' => 'Issued Info', 'href' => '#', 'onclick' => "loadView(event,'/summary/issuedinfo');", 'access' => 'ISSUED_INFO'),
				array('caption' => 'Pending Info', 'href' => '#', 'onclick' => "loadView(event,'/summary/pendinginfo');", 'access' => 'PENDING_INFO'),
				array('caption' => 'Statistik Route', 'href' => '#', 'onclick' => "loadView(event,'/summary/statistikroute');", 'access' => ''),
			)),
			
			array('caption' => 'Top User', 'href' => '#', 'onclick' => "loadView(event,'/summary/topuser');", 'access' => 'TOP_USER'),
			array('caption' => 'Top Coupon', 'href' => '#', 'onclick' => "loadView(event,'/summary/topcoupon');", 'access' => 'TOP_USER'),
			array('caption' => 'Top Airlines Trx', 'href' => '#', 'onclick' => "loadView(event,'/summary/topairlinestrx');", 'access' => 'TOP_USER'),
			//array('caption' => 'Invoice', 'href' => '#', 'onclick' => "loadView(event,'/invoice/list');", 'access' => 'INVOICE'),
			array('caption' => 'Interface Deposit', 'href' => '#', 'access' => 'INTERFACE_DEPOSIT', 'node' => array(
				array('caption' => 'Airlines Deposit', 'href' => '#', 'onclick' => "loadView(event,'/interface/deposit');", 'access' => ''),
				array('caption' => 'Voucher Deposit', 'href' => '#', 'onclick' => "loadView(event,'/depositpulsa/deposit');", 'access' => ''),
				array('caption' => 'Hotel Deposit', 'href' => '#', 'onclick' => "loadView(event,'/bookinghotel/konsorsiumdeposit');", 'access' => ''),
			)),
			array('caption' => 'Warning Message', 'href' => '#', 'onclick' => "loadView(event,'/traffic/list');", 'access' => 'TRAFFIC'),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'Loan', 'href' => '#', 'onclick' => "loadView(event,'/user/loan');", 'access' => 'ULOAN'),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'User Summary', 'href' => '#', 'access' => 'USUMMARY', 'node' => array(
				array('caption' => 'Simple', 'href' => '#', 'onclick' => "loadView(event,'/summary/usersimple');", 'access' => ''),
				array('caption' => 'Detail', 'href' => '#', 'onclick' => "loadView(event,'/summary/userdetail');", 'access' => ''),
				array('caption' => 'Compact', 'href' => '#', 'onclick' => "loadView(event,'/summary/usercompact');", 'access' => ''),
				array('caption' => 'Compact 2', 'href' => '#', 'onclick' => "loadView(event,'/summary/usercompact2');", 'access' => ''),
				array('caption' => 'Summary Upline', 'href' => '#', 'onclick' => "loadView(event,'/summary/summaryupline');", 'access' => ''),
			)),
			array('caption' => 'User Bonus', 'href' => '#', 'onclick' => "loadView(event,'/userbonus/user');", 'access' => 'UBONUS'),
			array('caption' => 'User Cash Flow', 'href' => '#', 'onclick' => "loadView(event,'/cashflow/user');", 'access' => 'UCASHFLOW'),
			array('caption' => 'Search Topup', 'href' => '#', 'onclick' => "loadView(event,'/banktransfer/search');", 'access' => ''),
			array('caption' => 'Trace Email & Mobile', 'href' => '#', 'onclick' => "loadView(event,'/user/traceemailmobile');", 'access' => ''),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'Refund Request', 'href' => '#', 'onclick' => "loadView(event,'/ticketingairlines/refundairlinelist');", 'access' => ''),
			array('caption' => 'Reschedule Request', 'href' => '#', 'onclick' => "loadView(event,'/ticketingairlines/reschedulelist');", 'access' => ''),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'Bank History', 'href' => '#', 'onclick' => "loadView(event,'/bankhistory/list');", 'access' => 'SUPER_USER'),
			array('caption' => 'Developer Invoice', 'href' => '#', 'onclick' => "loadView(event,'/summary/invoice');", 'access' => 'VENDOR_INVOICE'),
			array('caption' => 'Voucher Invoice', 'href' => '#', 'onclick' => "loadView(event,'/managepulsa/invoice');", 'access' => 'SUPER_USER'),
			array('caption' => 'Tax Invoice', 'href' => '#', 'onclick' => "loadView(event,'/alfatax/invoice');", 'access' => 'SUPER_USER'),
			array('caption' => 'Alfa Payment', 'href' => '#', 'onclick' => "loadView(event,'/paymentalfa/list');", 'access' => 'SUPER_USER'),
			array('caption' => 'Report Withdrawal API', 'href' => '#', 'onclick' => "loadView(event,'/withdrawalapi/list');", 'access' => 'SUPER_USER'),
		)),
		array('caption' => 'Administration', 'href' => '#', 'access' => 'ADMINISTRATION', 'node' => array(
			array('caption' => 'User List', 'href' => '#', 'onclick' => "loadView(event,'/user/list');", 'access' => 'USER'),
			array('caption' => 'Sales List', 'href' => '#', 'onclick' => "loadView(event,'/sales/list');", 'access' => 'SALES'),
			array('caption' => 'Group List', 'href' => '#', 'onclick' => "loadView(event,'/group/list');", 'access' => 'GROUP'),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'Request Register', 'href' => '#', 'onclick' => "loadView(event,'/register/list');", 'access' => 'REGISTER'),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'My Profile', 'href' => '#', 'onclick' => "loadView(event,'/user/profile');", 'access' => ''),
			array('caption' => 'Request Deposit', 'href' => '#', 'access' => '', 'node' => array(
				array('caption' => 'Bank Transfer', 'href' => '#', 'onclick' => "loadView(event,'/banktransfer/list');", 'access' => ''),
				array('caption' => 'Kartuku', 'href' => '#', 'onclick' => "window.open('/kartuku/kartukuredirect', '_blank');", 'access' => 'TOPUP_KARTUKU'),
				array('caption' => 'NicePay VA', 'href' => '#', 'onclick' => "window.open('/ionpay/paymentform', '_blank');", 'access' => 'TOPUP_NICEPAY'),
			)),				
			array('caption' => 'Change Password', 'href' => '#', 'onclick' => "loadView(event,'/user/changepassword');", 'access' => ''),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'Show User Log', 'href' => '#', 'onclick' => "loadView(event,'/userlog/list');", 'access' => 'USER_LOG'),
		)),
		array('caption' => 'Tools', 'href' => '#', 'access' => 'TOOLS', 'node' => array(
			array('caption' => 'Company Profile', 'href' => '#', 'onclick' => "loadView(event,'/companyprofile/companyprofile');", 'access' => 'SET_COMPANY_PROFILE'),
			array('caption' => 'Edit News', 'href' => '#', 'onclick' => "loadView(event,'/news/list');", 'access' => 'EDIT_NEWS'),
			array('caption' => 'Add Max Downline', 'href' => '#', 'onclick' => "loadView(event,'/user/addmaxdownline');", 'access' => 'ADD_MAX_DOWNLINE'),
			array('caption' => 'Set Register', 'href' => '#', 'onclick' => "loadView(event,'/register/email');", 'access' => 'SET_EMAIL'),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'Bank', 'href' => '#', 'access' => 'BANK_CODE', 'node' => array(
				array('caption' => 'Bank Code', 'href' => '#', 'onclick' => "loadView(event,'/bank/list');", 'access' => 'BANK_CODE'),
				array('caption' => 'Bank Acc Owner', 'href' => '#', 'onclick' => "loadView(event,'/bankaccowner/list');", 'access' => ''),
				array('caption' => 'Bank Interface', 'href' => '#', 'onclick' => "loadView(event,'/bankaccount/list');", 'access' => 'BANK_INTERFACE'),			
			)),
			array('caption' => 'Province Code', 'href' => '#', 'onclick' => "loadView(event,'/province/list');", 'access' => 'PROVINCE_CODE'),
			array('caption' => '---', 'access' => ''),
			array('caption' => 'Airlines', 'href' => '#', 'access' => 'AIRLINES_TOOLS', 'node' => array(
				array('caption' => 'City Code', 'href' => '#', 'onclick' => "loadView(event,'/city/list');", 'access' => 'CITY_CODE'),
				//array('caption' => 'City Villa', 'href' => '#', 'onclick' => "loadView(event,'/villa');", 'access' => 'CITY_CODE'),
				//array('caption' => 'Airlines Status', 'href' => '#', 'onclick' => "loadView(event,'/interface/list');", 'access' => 'AIR_STATUS'),
				array('caption' => 'Price NTA', 'href' => '#', 'onclick' => "loadView(event,'/surchargenta/list');", 'access' => 'PRICE_NTA'),
				array('caption' => 'Price Publish', 'href' => '#', 'onclick' => "loadView(event,'/surchargepublish/list');", 'access' => 'PRICE_PUBLISH'),
				array('caption' => 'Price NTA International', 'href' => '#', 'onclick' => "loadView(event,'/surchargenta/listinternational');", 'access' => 'PRICE_NTA'),
				array('caption' => 'Price Publish International', 'href' => '#', 'onclick' => "loadView(event,'/surchargepublish/listinternational');", 'access' => 'PRICE_PUBLISH'),

				array('caption' => 'Interface', 'href' => '#', 'onclick' => "loadView(event,'/airlines/list');", 'access' => 'INTERFACE'),
				//array('caption' => 'Batavia NTA', 'href' => '#', 'onclick' => "loadView(event,'/batavia/nta');", 'access' => 'BATAVIA_NTA'),
			)),
			array('caption' => 'International IATA', 'href' => '#', 'access' => 'SUPER_USER', 'node' => array(
				array('caption' => 'Price NTA', 'href' => '#', 'onclick' => "loadView(event,'/surchargenta/listinternationalsabre');", 'access' => ''),
				array('caption' => 'Price Publish', 'href' => '#', 'onclick' => "loadView(event,'/surchargepublish/listinternationalsabre');", 'access' => ''),
				array('caption' => 'Interface', 'href' => '#', 'onclick' => "loadView(event,'/airlines/listinternational');", 'access' => ''),
			)),
			array('caption' => 'Waterb', 'href' => '#', 'access' => 'WATERB_AIRLINES_TOOLS', 'node' => array(
				array('caption' => 'City Code', 'href' => '#', 'onclick' => "loadView(event,'/city/listwaterb');", 'access' => 'WATERB_CITY_CODE'),
				//array('caption' => 'Waterb Status', 'href' => '#', 'onclick' => "loadView(event,'/airlines/listwaterb');", 'access' => 'AIR_STATUS'),
				array('caption' => 'Price NTA', 'href' => '#', 'onclick' => "loadView(event,'/surchargenta/listwaterb');", 'access' => 'WATERB_PRICE_NTA'),
				array('caption' => 'Price Publish', 'href' => '#', 'onclick' => "loadView(event,'/surchargepublish/listwaterb');", 'access' => 'WATERB_PRICE_PUBLISH'),
				array('caption' => 'Interface', 'href' => '#', 'onclick' => "loadView(event,'/airlines/listwaterb');", 'access' => 'WATERB_INTERFACE'),
			)),
			array('caption' => 'KAI', 'href' => '#', 'access' => 'SUPER_USER', 'node' => array(
				array('caption' => 'City Code', 'href' => '#', 'onclick' => "loadView(event,'/city/listkai');", 'access' => ''),
				//array('caption' => 'Waterb Status', 'href' => '#', 'onclick' => "loadView(event,'/airlines/listwaterb');", 'access' => 'AIR_STATUS'),
				array('caption' => 'Price NTA', 'href' => '#', 'onclick' => "loadView(event,'/surchargenta/listkai');", 'access' => ''),
				array('caption' => 'Price Publish', 'href' => '#', 'onclick' => "loadView(event,'/surchargepublish/listkai');", 'access' => ''),
				array('caption' => 'Interface', 'href' => '#', 'onclick' => "loadView(event,'/airlines/listkai');", 'access' => ''),
			)),
			array('caption' => 'Hotel', 'href' => '#', 'access' => 'HOTEL_TOOLS', 'node' => array(
				array('caption' => 'Price NTA', 'href' => '#', 'onclick' => "loadView(event,'/surchargenta/listhotel');", 'access' => 'HOTEL_NTA'),
				array('caption' => 'Price Publish', 'href' => '#', 'onclick' => "loadView(event,'/surchargepublish/listhotel');", 'access' => 'HOTEL_PUBLISH'),
				array('caption' => 'Interface', 'href' => '#', 'onclick' => "loadView(event,'/airlines/listhotel');", 'access' => 'HOTEL_INTERFACE'),
				)),
			array('caption' => 'Voucher Pulsa', 'href' => '#', 'access' => 'TOOLS_VOUCHER', 'node' => array(
				//array('caption' => 'Product', 'href' => '#', 'onclick' => "loadView(event,'/productv/list');", 'access' => 'VOUCHER_PRODUCT'),
				//array('caption' => 'Nominal', 'href' => '#', 'onclick' => "loadView(event,'/nominalv/list');", 'access' => 'VOUCHER_NOMINAL'),
				array('caption' => 'Price Publish', 'href' => '#', 'onclick' => "loadView(event,'/pricevpublish/list');", 'access' => 'VOUCHER_PUBLISH'),
				array('caption' => 'Price NTA', 'href' => '#', 'onclick' => "loadView(event,'/pricevnta/list')", 'access' => 'VOUCHER_NTA'),
				// array('caption' => 'Parsing', 'href' => '#', 'onclick' => "loadView(event,'/parsingv/list')", 'access' => 'VOUCHER_PARSING'),
				array('caption' => 'Vendor', 'href' => '#', 'onclick' => "loadView(event,'/vendorv/list');", 'access' => 'VOUCHER_VENDOR'),
				// array('caption' => 'Interface', 'href' => '#', 'onclick' => "loadView(event,'/interfacev/list');", 'access' => 'VOUCHER_INTERFACE'),
			)),
			array('caption' => 'Tour', 'href' => '#', 'access' => 'SUPER_USER', 'node' => array(
				array('caption' => 'Provider', 'href' => '#', 'onclick' => "loadView(event,'/tourprovider/list');", 'access' => 'SUPER_USER'),
				array('caption' => 'Price NTA', 'href' => '#', 'onclick' => "loadView(event,'/surchargenta/listtour');", 'access' => 'SUPER_USER'),
				array('caption' => 'Price Publish', 'href' => '#', 'onclick' => "loadView(event,'/surchargepublish/listtour');", 'access' => 'SUPER_USER'),
				array('caption' => 'Interface', 'href' => '#', 'onclick' => "loadView(event,'/airlines/listtour');", 'access' => 'SUPER_USER'),
			)),
			array('caption' => 'ThemePark', 'href' => '#', 'access' => 'SUPER_USER', 'node' => array(
			//	array('caption' => 'Barcode', 'href' => '#', 'onclick' => "loadView(event,'/themepark/list');", 'access' => 'SUPER_USER'),
				array('caption' => 'Price NTA', 'href' => '#', 'onclick' => "loadView(event,'/surchargenta/listthemepark');", 'access' => 'SUPER_USER'),
				array('caption' => 'Price Publish', 'href' => '#', 'onclick' => "loadView(event,'/surchargepublish/listthemepark');", 'access' => 'SUPER_USER'),
				array('caption' => 'Interface', 'href' => '#', 'onclick' => "loadView(event,'/airlines/listthemepark');", 'access' => 'SUPER_USER'),
			)),
			array('caption' => 'Pembayaran', 'href' => '#', 'access' => 'TOOLS_PPOB', 'node' => array(
				array('caption' => 'Product', 'href' => '#', 'onclick' => "loadView(event,'/ppobproduct/list');", 'access' => 'PPOB_PRODUCT'),
				array('caption' => 'Price NTA', 'href' => '#', 'onclick' => "loadView(event,'/ppobnta/list');", 'access' => 'PPOB_NTA'),
			)),
			array('caption' => '---', 'access' => ''),
		)),
		array('caption' => 'Config', 'href' => '#', 'access' => 'CONFIG', 'node' => array(
			array('caption' => 'Rules', 'href' => '#', 'onclick' => "loadView(event,'/rules/list');", 'access' => 'RULES'),
			array('caption' => 'Style', 'href' => '#', 'onclick' => "loadView(event,'/style/list');", 'access' => 'SET_STYLE'),
		)),
		array('caption' => 'Help', 'href' => '#', 'node' => array(
			//array('caption' => 'Manual', 'href' => "#", 'target' => '_new', 'access' => ''),
			array('caption' => 'Manual', 'href' => '#', 'onclick' => "openManual()", 'access' => ''),
			array('caption' => 'Manual News', 'href' => '#', 'onclick' => "loadView(event,'/news/manual');", 'access' => 'SUPER_USER'),
			array('caption' => 'Manual Topup', 'href' => '#', 'access' => '', 'node' => array(
				array('caption' => 'Topup via BCA', 'href' => '#', 'onclick' => "loadView(event,'/manual/topupocbcbca');", 'access' => ''),
				array('caption' => 'Topup via Kartuku', 'href' => '#', 'onclick' => "loadView(event,'/manual/topupkartuku');", 'access' => ''),
				array('caption' => 'Topup via Nicepay', 'href' => '#', 'onclick' => "loadView(event,'/manual/topupnicepay');", 'access' => ''),
			)),
			
			array('caption' => '---', 'acess' => ''),
			array('caption' => 'v1.9.21', 'href' => '#', 'access' => ''),
		)),
	);
	
	function getAccessMenu($menu)
	{
		$result = NULL;
		if (!empty($menu)) {
			$j = 0;
			$count = count($menu);
			for ($i = 0; $i < $count; $i++) {
				if ($menu[$i]['caption'] == 'Manual') //$menu[$i]['onclick'] = "openManual('" . base64_encode(session_id()) . "')";
				if (!$this->isInRole('SUPER_USER')) {
					$news = new News();
					$news_row = $news->getRow(ALIAS);
					if (($news_row['user_id'] != $this->session->user_id) && (in_array($menu[$i]['caption'], array('Tools', 'Edit News')))){
						continue;
					}
				}
				
				if ((!empty($menu[$i]['access'])) && (!$this->isInRole($menu[$i]['access']))) continue;
				
				$result[$j] = $menu[$i];
				if (!empty($menu[$i]['node'])) {
					$access = $this->getAccessMenu($menu[$i]['node']);
					if (!empty($access)) {
						$result[$j]['node'] = $access;
					} else {
						unset($result[$j]['node']);
					}
				}
				$j++;
			}
		}
		
		return $result;
	}
	
	public function init(ModuleManager $manager)
	{
		$eventManager = $manager->getEventManager();
		$sharedEventManager = $eventManager->getSharedManager();
		$sharedEventManager->attach(__NAMESPACE__, 'dispatch', [$this, 'onDispatch'], 100);
	}
	
	public function onDispatch(MvcEvent $event)
	{
		$controller = $event->getTarget();
		$controllerClass = get_class($controller);
		$moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
		
		if ($moduleNamespace == __NAMESPACE__) {
			$viewModel = $event->getViewModel();
			$viewModel->setTemplate('layout/layout');
		}
		$this->setUp();
		AbstractActionController::onDispatch($event);
	}
	
	public function setUp()
	{
try {
		$this->view = new ViewModel();
		$this->session = new Container('namespace');
		
		if ($this->session->user_id == 1) {
			$this->session->setExpirationSeconds(1800);
		} else {
			$this->session->setExpirationSeconds(EXPIRED);
		}
		
		if (!isset($this->session->acl)) {
			$group = new Group();
			$acl = new Acl();
			$access_all = $group->getAccessAll();
			foreach ($access_all as $a) {
				$acl->addRole(new Role($a));
			}
			$this->session->acl = serialize($acl);
		} else {
			$user = new User();
			if ($this->getEvent()->getRouteMatch()->getMatchedRouteName() != 'admin') {
				$user->updateLifeTime($this->session->user_id, time()+ EXPIRED);
			}
		}
} catch (Exception $e) {
	echo $e->getMessage();
	exit();
}
	}
	
	public function checkRole($role)
	{
		if (!$this->isInRole($role)) {
			if (!empty($this->session->user_id)) {
				$user = new User();
				$user->updateLifeTime($this->session->user_id, time());
			}
			$this->destroyRole();
			echo 'gak ada access check role woyy !!'; exit();
			//$this->_transfer('default', 'admin', 'noaccess');
		}
	}
	
	public function checkpopRole($role)
	{
		if (!$this->isInRole($role)) {
			if (!empty($this->session->user_id)) {
				$user = new User();
				$user->updateLifeTime($this->session->user_id, time());
			}
			$this->destroyRole();
			echo 'gak ada access check pop role woyy !!'; exit();
			//$this->_transfer('default', 'admin', 'nopopup');
		}
	}
	
	public function isInRole($role)
	{
		$acl = unserialize($this->session->acl);
		return ($acl->isAllowed($role));
	}
	
	public function setRole($allow)
	{
		$group = new Group();
		$acl = new Acl();
		$access_all = $group->getAccessAll();
		
		foreach ($access_all as $a) {
			$acl->addRole(new Role($a));
		}
		foreach ($allow as $a) {
			$acl->allow($a);
		}
		
		$this->session->acl = serialize($acl);
	}
	
	public function destroyRole()
	{
		$this->session->getManager()->destroy();
		//Zend_Session::expireSessionCookie();
	}
	
	public function printResponse($status, $message = NULL, $content = NULL)
	{
		$response = NULL;
		$response['status'] = $status;
		$response['message'] = $message;
		$response['content'] = $content;
		
		echo json_encode($response);
		exit();
	}
}
