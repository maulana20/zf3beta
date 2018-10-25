<?php
include_once realpath ('../../') . "/Startup.php";
use Application\Model\Versa_Gateway_Adapter;

$access = 'ADMINISTRATION USER ADD_USER EDIT_USER AI_USER DELETE_USER INFO_USER RESET_PASSWORD USER_LOG';
$access_list = explode(' ', $access);

Versa_Gateway_Adapter::init('tblGroup')->update(['group_access' => serialize($access_list)], ['group_id' => 11]);

foreach ($access_list as $value) {
	Versa_Gateway_Adapter::init('tblProfile')->insert(array('profile_code' => $value));
}

echo 'update profile done';

// before
//$administration = 'a:143:{i:0;s:9:"_BATAVIA_";i:1;s:10:"_CITILINK_";i:2;s:8:"_GARUDA_";i:3;s:11:"_GARUDAAPI_";i:4;s:6:"_LION_";i:5;s:9:"_MERPATI_";i:6;s:11:"_SRIWIJAYA_";i:7;s:7:"_TIGER_";i:8;s:16:"ADD_MAX_DOWNLINE";i:9;s:8:"ADD_USER";i:10;s:14:"ADMINISTRATION";i:11;s:6:"AGENTS";i:12;s:11:"AGENTS_PPOB";i:13;s:14:"AGENTS_VOUCHER";i:14;s:12:"AI_INTERFACE";i:15;s:7:"AI_USER";i:16;s:10:"AIR_STATUS";i:17;s:14:"AIRLINES_TOOLS";i:18;s:9:"BANK_CODE";i:19;s:14:"BANK_INTERFACE";i:20;s:11:"BATAVIA_NTA";i:21;s:7:"BOOKING";i:22;s:13:"BUS_INTERFACE";i:23;s:13:"BUS_PRICE_NTA";i:24;s:17:"BUS_PRICE_PUBLISH";i:25;s:9:"BUS_TOOLS";i:26;s:8:"CASHFLOW";i:27;s:9:"CITY_CODE";i:28;s:13:"CITY_KAI_CODE";i:29;s:6:"CONFIG";i:30;s:11:"DELETE_DATA";i:31;s:11:"DELETE_USER";i:32;s:7:"DEPOSIT";i:33;s:11:"E_INTERFACE";i:34;s:12:"EDIT_DEPOSIT";i:35;s:10:"EDIT_GROUP";i:36;s:9:"EDIT_NEWS";i:37;s:9:"EDIT_USER";i:38;s:4:"FILE";i:39;s:11:"FILE_EXPORT";i:40;s:5:"GROUP";i:41;s:15:"HOTEL_INTERFACE";i:42;s:12:"HOTEL_ISSUED";i:43;s:9:"HOTEL_NTA";i:44;s:13:"HOTEL_PUBLISH";i:45;s:11:"HOTEL_TOOLS";i:46;s:5:"INBOX";i:47;s:9:"INFO_USER";i:48;s:9:"INTERFACE";i:49;s:17:"INTERFACE_DEPOSIT";i:50;s:23:"INTERNATIONAL_INTERFACE";i:51;s:23:"INTERNATIONAL_PRICE_NTA";i:52;s:27:"INTERNATIONAL_PRICE_PUBLISH";i:53;s:19:"INTERNATIONAL_TOOLS";i:54;s:7:"INVOICE";i:55;s:6:"ISSUED";i:56;s:11:"ISSUED_INFO";i:57;s:13:"KAI_CITY_CODE";i:58;s:13:"KAI_INTERFACE";i:59;s:10:"KAI_ISSUED";i:60;s:13:"KAI_PRICE_NTA";i:61;s:17:"KAI_PRICE_PUBLISH";i:62;s:9:"KAI_TOOLS";i:63;s:6:"MANAGE";i:64;s:13:"OFF_INTERFACE";i:65;s:6:"OUTBOX";i:66;s:9:"PASSENGER";i:67;s:15:"PELNI_INTERFACE";i:68;s:15:"PELNI_PRICE_NTA";i:69;s:19:"PELNI_PRICE_PUBLISH";i:70;s:11:"PELNI_TOOLS";i:71;s:12:"PENDING_INFO";i:72;s:11:"PPOB_BPJSKS";i:73;s:11:"PPOB_MANAGE";i:74;s:17:"PPOB_MULTIFINANCE";i:75;s:8:"PPOB_NTA";i:76;s:9:"PPOB_PDAM";i:77;s:8:"PPOB_PLN";i:78;s:12:"PPOB_PRODUCT";i:79;s:19:"PPOB_TELEKOMUNIKASI";i:80;s:11:"PPOB_TELKOM";i:81;s:12:"PPOB_TVKABEL";i:82;s:9:"PRICE_NTA";i:83;s:13:"PRICE_PUBLISH";i:84;s:13:"PROVINCE_CODE";i:85;s:8:"RCONTROL";i:86;s:8:"REGISTER";i:87;s:14:"REPORT_KARTUKU";i:88;s:14:"REPORT_NICEPAY";i:89;s:7:"REPORTS";i:90;s:14:"RESET_PASSWORD";i:91;s:5:"RULES";i:92;s:5:"SALES";i:93;s:10:"SENDANSWER";i:94;s:19:"SET_COMPANY_PROFILE";i:95;s:9:"SET_EMAIL";i:96;s:9:"SET_GROUP";i:97;s:14:"SET_PRICE_NAME";i:98;s:9:"SET_STYLE";i:99;s:7:"SUMMARY";i:100;s:10:"SUPER_USER";i:101;s:19:"THEMEPARK_INTERFACE";i:102;s:19:"THEMEPARK_PRICE_NTA";i:103;s:23:"THEMEPARK_PRICE_PUBLISH";i:104;s:15:"THEMEPARK_TOOLS";i:105;s:9:"TICKETING";i:106;s:5:"TOOLS";i:107;s:10:"TOOLS_PPOB";i:108;s:13:"TOOLS_VOUCHER";i:109;s:8:"TOP_USER";i:110;s:13:"TOPUP_KARTUKU";i:111;s:13:"TOPUP_MAYBANK";i:112;s:13:"TOPUP_NICEPAY";i:113;s:10:"TOPUP_OCBC";i:114;s:11:"TOPUP_SALDO";i:115;s:14:"TOUR_INTERFACE";i:116;s:14:"TOUR_PRICE_NTA";i:117;s:18:"TOUR_PRICE_PUBLISH";i:118;s:13:"TOUR_PROVIDER";i:119;s:10:"TOUR_TOOLS";i:120;s:7:"TRAFFIC";i:121;s:6:"UBONUS";i:122;s:9:"UCASHFLOW";i:123;s:5:"ULOAN";i:124;s:4:"USER";i:125;s:8:"USER_LOG";i:126;s:8:"USUMMARY";i:127;s:14:"VENDOR_INVOICE";i:128;s:17:"VOUCHER_INTERFACE";i:129;s:14:"VOUCHER_ISSUED";i:130;s:14:"VOUCHER_MANAGE";i:131;s:22:"VOUCHER_MANAGE_PENDING";i:132;s:15:"VOUCHER_NOMINAL";i:133;s:11:"VOUCHER_NTA";i:134;s:15:"VOUCHER_PARSING";i:135;s:15:"VOUCHER_PRODUCT";i:136;s:15:"VOUCHER_PUBLISH";i:137;s:14:"VOUCHER_VENDOR";i:138;s:21:"WATERB_AIRLINES_TOOLS";i:139;s:16:"WATERB_CITY_CODE";i:140;s:16:"WATERB_INTERFACE";i:141;s:16:"WATERB_PRICE_NTA";i:142;s:20:"WATERB_PRICE_PUBLISH";}';
//var_dump(unserialize($administration));