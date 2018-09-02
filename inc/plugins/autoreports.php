<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.");
}

$plugins->add_hook('report_do_report_end', 'autoreports_report');

function autoreports_info()
{
global $pversion, $lang;

	$lang->load('autoreports');
	$pversion = "1.0";
	return array(
		"name"			=> $lang->autoreports_name,
		"description"	=> $lang->autoreports_desc,
		"website"		=> "https://oseax.com",
		"author"		=> "Wires <i>(AndreRl)</i>",
		"authorsite"	=> "https://oseax.com",
		"version"		=> $pversion,
		"guid" 			=> "",
		"codename"		=> "",
		"compatibility" => "18*"
	);
}

function autoreports_install()
{
global $db, $mybb, $lang;
$lang->load('autoreports');

$setting_group = array(
    'name' => 'autoreports',
    'title' => $lang->autoreports_title,
    'description' => $lang->autoreports_desc,
    'disporder' => 5, 
    'isdefault' => 0
);
$gid = $db->insert_query("settinggroups", $setting_group);

$setting_array = array(

    'autoreports_enable' => array(
        'title' => $lang->autoreports_enable,
        'description' => $lang->autoreports_enabledesc,
        'optionscode' => 'yesno',
        'value' => '1', 
        'disporder' => 1
    ),

    'autoreports_applygroup' => array(
        'title' => $lang->autoreports_applygroups,
        'description' => $lang->autoreports_applygroupsdesc,
        'optionscode' => "groupselect",
        'value' => 2,
        'disporder' => 2
    ),

    'autoreports_staffchoice' => array(
        'title' => $lang->autoreports_staffchoice,
        'description' => $lang->autoreports_staffchoicedesc,
        'optionscode' => 'yesno',
        'value' => 1,
        'disporder' => 3
    ),
	
    'autoreports_staffselect' => array(
        'title' => $lang->autoreports_staffselect,
        'description' => $lang->autoreports_staffselectdesc,
        'optionscode' => 'groupselect',
        'value' => '4',
        'disporder' => 4
    ),
	
    'autoreports_reportcount' => array(
        'title' => $lang->autoreports_reportcount,
        'description' => $lang->autoreports_reportcountdesc,
        'optionscode' => 'numeric',
        'value' => 5,
        'disporder' => 5
    ),
	
    'autoreports_actionrequired' => array(
        'title' => $lang->autoreports_actionrequired,
        'description' => $lang->autoreports_actionrequireddesc,
        'optionscode' => 'numeric',
        'value' => 15,
        'disporder' => 6
    ),

    'autoreports_postcount' => array(
        'title' => $lang->autoreports_postcount,
        'description' => $lang->autoreports_postcountdesc,
        'optionscode' => 'numeric',
        'value' => 10,
        'disporder' => 7
    ),

    'autoreports_joindate' => array(
        'title' => $lang->autoreports_joindate,
        'description' => $lang->autoreports_joindatedesc,
        'optionscode' => 'select\n0=3 Days\n1=A Week\n2=Two Weeks',
        'value' => 1,
        'disporder' => 8
    ),
	
    'autoreports_enablewarnings' => array(
        'title' => $lang->autoreports_enablewarnings,
        'description' => $lang->autoreports_warningsdesc,
        'optionscode' => 'yesno',
        'value' => 1,
        'disporder' => 9
    ),
	
    'autoreports_warningpoints' => array(
        'title' => $lang->autoreports_warningpoints,
        'description' => $lang->autoreports_warningpointsdesc,
        'optionscode' => 'numeric',
        'value' => 1,
        'disporder' => 10
    ),
	
    'autoreports_warnexpire' => array(
        'title' => $lang->autoreports_warnexpire,
        'description' => $lang->autoreports_warnexpiredesc,
        'optionscode' => 'select\n0=One Week\n1=Two Weeks\n2=One Month',
        'value' => 1,
        'disporder' => 11
    ),
	
    'autoreports_friendlyuid' => array(
        'title' => $lang->autoreports_friendlyuid,
        'description' => $lang->autoreports_friendlyuiddesc,
        'optionscode' => 'numeric',
        'value' => 1,
        'disporder' => 12
    ),
	
    'autoreports_friendlysubject' => array(
        'title' => $lang->autoreports_friendlysubject,
        'description' => $lang->autoreports_friendlysubjectdesc,
        'optionscode' => 'text',
        'value' => "Reported: Friendly Reminder",
        'disporder' => 13
    ),
	
    'autoreports_friendlymessage' => array(
        'title' => $lang->autoreports_friendlymessage,
        'description' => $lang->autoreports_friendlymessagedesc,
        'optionscode' => 'textarea',
        'value' => 'Hello $username,
		
		One or more of your posts have been reported. Please improve the content in your posts.
		
		Kind Regards,
		Forum Staff',
        'disporder' => 14
    ),
	
		// Now for the spam settings...
		
    'autoreports_enablespam' => array(
        'title' => $lang->autoreports_enablespam,
        'description' => $lang->autoreports_enablespamdesc,
        'optionscode' => 'yesno',
        'value' => 1,
        'disporder' => 15
    ),
	
    'autoreports_reportid' => array(
        'title' => $lang->autoreports_reportid,
        'description' => $lang->autoreports_reportiddesc,
        'optionscode' => 'numeric',
        'value' => 4,
        'disporder' => 16
    ),
	
    'autoreports_purgespammer' => array(
        'title' => $lang->autoreports_purgespammer,
        'description' => $lang->autoreports_purgespammerdesc,
        'optionscode' => 'yesno',
        'value' => 1,
        'disporder' => 17
    ),

);

foreach($setting_array as $name => $setting)
{
    $setting['name'] = $name;
    $setting['gid'] = $gid;

    $db->insert_query('settings', $setting);
}
	
rebuild_settings();

$db->add_column("reportedcontent", "reporteduid", "int default '0' AFTER uid");

}

function autoreports_is_installed()
{
    global $mybb;
	
    if($mybb->settings['autoreports_enable'])
    {
        return true;
    }
    return false;
}

function autoreports_uninstall()
{
global $db;

$db->delete_query('settinggroups', "name = 'autoreports'");

$db->delete_query('settings', "name IN ('autoreports_enable','autoreports_applegroup','autoreports_staffchoice','autoreports_staffselect',
'autoreports_reportcount','autoreports_actionrequired','autoreports_enablespam','autoreports_reportid','autoreports_postcount',
'autoreports_joindate','autoreports_purgespammer','autoreports_friendlyuid','autoreports_friendlysubject','autoreports_friendlymessage',
'autoreports_enablewarnings', 'autoreports_warningpoints', 'autoreports_warnexpire')");

rebuild_settings();

$db->drop_column("reportedcontent", "reporteduid");

}

function autoreports_activate()
{
// Handle Upgrade & Notifications


}

function autoreports_deactivate()
{

}

function autoreports_report()
{
global $db, $mybb, $report, $new_report, $valid, $score, $cache, $lang;
$lang->load('autoreports');

if($mybb->settings['autoreports_enable'] != 1)
{
	return;
}

if($mybb->settings['autoreports_staffchoice'] == 1)
{
	
	$usergroup = $mybb->settings['autoreports_staffselect'];

	$query = $db->query("
	SELECT s.sid, s.uid
	FROM ".TABLE_PREFIX."sessions s
	LEFT JOIN ".TABLE_PREFIX."users u ON (s.uid=u.uid)
	WHERE u.usergroup = '".$usergroup."'
	");
	$online = $db->num_rows($query);
	
	if($online >= 1)
	{
		return;
	}
}

$type = '';
$valid = 0;

while($mybb->settings['autoreports_enablespam'] == 1 && ($mybb->settings['autoreports_reportid'] == $new_report['reasonid'] || $mybb->settings['autoreports_reportid'] == $report['reasonid']))
{

	$type = "spam";
	// Rest of spam detection here....

	$query = $db->simple_select("reportedcontent", "*", "reasonid = '".$mybb->settings['autoreports_reportid']."' AND type = 'post'", array(
		"order_by" => 'rid',
		"order_dir" => 'DESC',
		"limit" => 1
	));
	
	$count = $db->num_rows($query);

	if($count == 0)
	{
		return;
	}
	
	$result = $db->fetch_array($query);
	if($result['reports'] >= $mybb->settings['autoreports_reportcount']) {

		autoreports_genuine($mybb->user['uid'], $type);
		
	} else {
		return;
	}
	
	switch ($valid)
	{
		case 1:
			continue;
			break;
		case 0:
			return;
			break;
		default:
			return;
	}

	// Determine if reported user is a spammer
	$score = 0;
	$type = 'testuser';
	$post = get_post($result['id']);
	
	autoreports_genuine($post['uid'], $type);
	
	$modlogdata['fid'] = $post['fid'];
	$modlogdata['tid'] = $post['tid'];
	$modlogdata['pid'] = $post['pid'];

	require_once MYBB_ROOT."inc/class_moderation.php";
	$moderation = new Moderation;		
	
	$list = array(1, 0);
	if(in_array($score, $list))
	{
			$moderation->unapprove_posts(array($result['id']));
			$action = $lang->autoreports_unapprove.$score;
			autoreports_log($modlogdata, $action);
			return;
	} else {
		
		if($mybb->settings['autoreports_purgespammer'] != 1)
		{
			$moderation->unapprove_posts(array($result['id']));
			autoreports_moderateuser($post['uid']);
			
			$action = $lang->autoreports_unapprovemoderate.$score;
			autoreports_log($modlogdata, $action);
			return;
		}
		
		autoreports_purgespammer($post['uid']);
		$user = get_user($post['uid']);
		autoreports_log($modlogdata, $lang->autoreports_purged);
		$cache->update_reportedcontent();
	}
	
	return;
}

// General detection here...
	$type = "general";
	
	$query = $db->simple_select("reportedcontent", "*", "type = 'post'", array(
		"order_by" => 'rid',
		"order_dir" => 'DESC',
		"limit" => 1
	));
	
	$count = $db->num_rows($query);

	if($count == 0)
	{
		return;
	}
	// At this line I got bored...
	$result = $db->fetch_array($query);
	if($result['reports'] >= $mybb->settings['autoreports_reportcount']) {

		autoreports_genuine($mybb->user['uid'], $type);
		
	} else {
		return;
	}
	

	switch ($valid)
	{
		case 1:
			continue;
			break;
		case 0:
			return;
			break;
		default:
			return;
	}
	// Reporter is fine.. What about the reported user?
	$post = get_post($result['id']);
	
	$modlogdata['fid'] = $post['fid'];
	$modlogdata['tid'] = $post['tid'];
	$modlogdata['pid'] = $post['pid'];

	require_once MYBB_ROOT."inc/class_moderation.php";
	$moderation = new Moderation;		
	$moderation->unapprove_posts(array($post['pid']));
	
/*	$query = $db->query("
	SELECT p.uid
	FROM ".TABLE_PREFIX."reportedcontent r
	LEFT JOIN ".TABLE_PREFIX."posts p ON (r.id=p.pid)
	WHERE r.id = '".$result['id']."'
	");
	$reporteduid = $db->fetch_field($query, "uid");*/
	
	//Populate column with UID
	$uidcolumn = array(
		"reporteduid" => $post['uid']
	);
	$db->update_query("reportedcontent", $uidcolumn, "id = '".$post['pid']."'");
	$query = $db->simple_select("reportedcontent", "*", "reporteduid = '".$post['uid']."'");
	$count = $db->num_rows($query);
	
	$user = get_user($post['uid']);
	
	if($count >= $mybb->settings['autoreports_actionrequired'] && $user['warningpoints'] != 0)
	{
		autoreports_moderateuser($post['uid']);
		$action = $lang->autoreports_gen_unapprovemoderate;
		autoreports_log($modlogdata, $action);
		
		
	} else if($count >= $mybb->settings['autoreports_actionrequired'] && $user['warningpoints'] == 0)
	{
		if($mybb->settings['autoreports_enablewarnings'] != 1)
		{
			$uid = $mybb->settings['autoreports_friendlyuid'];
			$touid = $post['uid'];
			$uname = $post['username'];
			$subject = $mybb->settings['autoreports_friendlysubject'];
			$message = $mybb->settings['autoreports_friendlymessage'];
			autoreports_pm($uid, $touid, $uname, $subject, $message);
			$action = $lang->autoreports_gen_unapprovefriendly;
			autoreports_log($modlogdata, $action);
		}
		// Warn user
		if($user['warningpoints'] >= $mybb->settings['maxwarningpoints'])
		{
			return;
		}
		
		switch ($mybb->settings['autoreports_warnexpire'])
		{
			case "Two Weeks":
				$time = 1209600 + TIME_NOW;
				break;
			case "One Month":
				$time = 2628003 + TIME_NOW;
				break;
			default:
				$time = 604800 + TIME_NOW;
		}
		
		$warning = array(
			"uid" => $post['uid'],
			"tid" => $post['tid'],
			"pid" => $post['pid'],
			"title" => $lang->autoreports_gen_automated,
			"points" => (int)$mybb->settings['autoreports_warningpoints'],
			"dateline" => TIME_NOW,
			"issuedby" => 1,
			"expires" => $time,
			"expired" => 0,
			"revokereason" => '',
			"notes" => $lang->autoreports_gen_automated
		);
		$action = $lang->autoreports_gen_warned;
		$db->insert_query("warnings", $warning);
		
		$points = $user['warningpoints'] + $mybb->settings['autoreports_warningpoints'];
		
		$update = array(
			"warningpoints" => $upoints,
			);
		$db->update_query("users", $update, "uid = '".$user['uid']."' ");
		autoreports_log($modlogdata, $action);
	}
	else
	{
		$action = $lang->autoreports_gen_unapproved;
		autoreports_log($modlogdata, $action);
	}
			
	return;
}

function autoreports_pm($fromuid, $touid, $uname, $subject, $message)
{
global $db;
	include_once MYBB_ROOT.'inc/datahandlers/pm.php';
	
	$keyword = array('$username');
	$replace = array($uname);
	$message = str_replace($keyword, $replace, $message);
	
    $pmhandler = new PMDataHandler();
	$pmhandler->admin_override = true;
    $pm = array(
		'subject' => $subject,
		'message' => $message,
        'fromid' => $fromuid,
        'toid' => $touid,
		'uid' => $touid,
		'do' => '',
		'pmid' => ''
			
        );

    $pm['options'] = array(
        'signature' => '1',
        'disablesmilies' => '0',
        'savecopy' => '0',
        'readreceipt' => '0'
        );

    $pmhandler->set_data($pm);

    // Now let the pm handler do all the hard work.
    if(!$pmhandler->validate_pm())
    {
		// Problem sending PM
	} 
	else 
	{
        $pminfo = $pmhandler->insert_pm(); 
	}
	
}

function autoreports_genuine($uid, $type)
{
	global $mybb, $valid, $score;
	
	// Check if this user is at least valid... We'll check the rest later
	$user = get_user($uid);
	
		// Define join dates
	switch ($mybb->settings['autoreports_joindate'])
	{
		case "3 Days":
			$time = 259200;
			break;
		case "Two Weeks":
			$time = 1209600;
			break;
		default:
			$time = 604800;
	}
	
	$time = TIME_NOW - $time;
	if($user['regdate'] > $time || $user['postnum'] < $mybb->settings['autoreports_postcount'] || $user['warningpoints'] != 0)
	{
		// Different result for reported spammer
		if($type == "testuser")
		{
			if($user['regdate'] > $time)
			{
				++$score;
			}
			if($mybb->settings['autoreports_postcount'] > $user['postnum'])
			{
				++$score;
			}
			if($user['warningpoints'] != 0)
			{
				++$score;
			}
			
			return $score;
		}
		// They do not meet the criteria - return 
		return;
	} else {
		$valid = 1;
		return $valid;
	}
}

function autoreports_purgespammer($uid)
{
	global $db, $mybb, $lang;
	$lang->load('autoreports');
	
	require_once MYBB_ROOT.'inc/datahandlers/user.php';
	$userhandler = new UserDataHandler('delete');
	
	$userhandler->delete_content($uid);
	$userhandler->delete_posts($uid);
	
	$user = get_user($uid);
	$query = $db->simple_select("banned", "uid", "uid = '".$uid."'");
	if($db->num_rows($query) > 0)
	{
		$banupdate = array(
			"reason" => $lang->autoreports_banreason
		);
		
		$db->update_query('banned', $banupdate, "uid = '{$uid}'");
	}
	else
	{	
		$addgroups = $user['additionalgroups'];
		$insert = array(
			"uid" => $uid,
			"gid" => (int)$mybb->settings['purgespammerbangroup'],
			"oldgroup" => $user['usergroup'],
			"oldadditionalgroups" => "$addgroups",
			"olddisplaygroup" => $user['displaygroup'],
			"admin" => 1,
			"dateline" => TIME_NOW,
			"bantime" => "---",
			"lifted" => 0,
			"reason" => $lang->autoreports_banreason
		);
		$db->insert_query('banned', $insert);
		
		$change = array(
			"usergroup" => (int)$mybb->settings['purgespammerbangroup'],
		);
		$db->update_query('users', $change, "uid = '".$uid."'");
	}
}

/*function autoreports_warnuser($uid, $points)
{
global $mybb, $db;
	
	$user = get_user($uid);
	
	if($user['warningpoints'] >= $mybb->settings['maxwarningpoints'])
	{
		// Ban user
	}
	
}*/

function autoreports_moderateuser($uid)
{
	global $db;
	
	$insert = array(
		"moderateposts"     => 1,
		"moderationtime"    => 0
	);
	$db->update_query("users", $insert, "uid = '".$uid."'");
}

function autoreports_log($data, $action)
{
	global $mybb, $db;
	
	$fid = 0;
	if(isset($data['fid']))
	{
		$fid = (int)$data['fid'];
		unset($data['fid']);
	}
	$tid = 0;
	if(isset($data['tid']))
	{
		$tid = (int)$data['tid'];
		unset($data['tid']);
	}
	$pid = 0;
	if(isset($data['pid']))
	{
		$pid = (int)$data['pid'];
		unset($data['pid']);
	}
	// Any remaining extra data - we my_serialize and insert in to its own column
	if(is_array($data))
	{
		$data = my_serialize($data);
	}
	$sql_array = array(
		"uid" => $mybb->user['uid'],
		"dateline" => TIME_NOW,
		"fid" => (int)$fid,
		"tid" => $tid,
		"pid" => $pid,
		"action" => $db->escape_string($action),
		"data" => $db->escape_string($data),
		"ipaddress" => ''
	);

		$db->insert_query("moderatorlog", $sql_array);
}