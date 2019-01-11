<?php
include_once realpath ('../../') . "/Startup.php";

use Administration\Model\Profile;
use Administration\Model\Group;

function checkProfile($profile_code)
{
	$profile = new Profile();
	$is_already = $profile->isAlready($profile_code);
	
	if ($is_already) {
		echo 'already ' . $profile_code . ' in profile';
	} else {
		$profile->add( ['profile_code' => $profile_code] );
		
		$group = new Group();
		$access_list = $group->getAccessAll();
		$group_id = $group->getId('Administrator');
		$group->update($group_id, ['group_access' => serialize($access_list)] );
		
		echo 'add ' . $profile_code . ' done';
	}
}

checkProfile('PERIOD');
