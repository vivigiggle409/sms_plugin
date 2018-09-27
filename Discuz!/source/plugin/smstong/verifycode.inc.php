<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: verifycode.inc.php 18582 2010-06-25 16:01:10Z ѽѽ���� $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
define('NOROBOT', TRUE);

require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');
require_once(DISCUZ_ROOT.'./source/discuz_version.php');

loadcache('plugin');

if($_GET['action'] == 'checkmobile') {

	$mobile = trim($_GET['mobile']);
	
	if(!ismobile($mobile) || empty($mobile)) {
		ownshowmessage(lang('plugin/smstong', 'smstong_mobilereg_mobile_invalid'));
	}

	$count = DB::result_first("SELECT count(mobile) FROM ".DB::table('common_member_profile')." WHERE mobile='".trim($mobile)."'");
	if($count >= $_G['cache']['plugin']['smstong']['accountlimit'])
		ownshowmessage(str_replace('{accountlimit}', $_G['cache']['plugin']['smstong']['accountlimit'], lang('plugin/smstong', 'smstong_mobilereg_accountlimit')));

} elseif($_GET['action'] == 'getregverifycode') {

	if($_GET['formhash'] != $_G['formhash']) {
		ownshowmessage(lang('messgae', 'submit_invalid'));
	}

	if($_G['cache']['plugin']['smstong']['openverifycode'] == 1) {
		require_once libfile('class/seccode');

		if(DISCUZ_VERSION == 'X1.5' || DISCUZ_VERSION == 'X2' || DISCUZ_VERSION == 'X2.5' || DISCUZ_VERSION == 'X3' || $_GET['inmobile'] == 'yes') {
			if(!check_seccode($_GET['seccodeverify'], $_GET['idhash'])){
				ownshowmessage(lang('plugin/smstong', 'smstong_mobilereg_verifycode_seccode'));
			}
		}
		else
		{
			if(!check_seccode($_GET['seccodeverify'], 'c'.$_GET['idhash'], 1, '')){
				ownshowmessage(lang('plugin/smstong', 'smstong_mobilereg_verifycode_seccode'));
			}
		}
	}

	$mobile = trim($_GET['mobile']);

	if($_G['cache']['plugin']['smstong']['mobilereg'] == 0) {
		ownshowmessage(lang('plugin/smstong', 'smstong_mobilereg_closed'));
	}

	if(!ismobile($mobile)) {
		ownshowmessage(lang('plugin/smstong', 'smstong_mobilereg_mobile_invalid'));
	}
	else {
		$mobilegap = $_G['cache']['plugin']['smstong']['mobilegap'];
		$sended = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND status=1 AND getip='$_G[clientip]' AND dateline>'$_G[timestamp]'-$mobilegap");
		
		if ($sended)
		{
			ownshowmessage(str_replace('{mobilegap}', $mobilegap, lang('plugin/smstong', 'smstong_mobilereg_mobile_sended')));
		}
		else
		{
			$count = DB::result_first("SELECT count(mobile) FROM ".DB::table('common_member_profile')." WHERE mobile='$mobile'");
			if($count >= $_G['cache']['plugin']['smstong']['accountlimit']) {
				ownshowmessage(str_replace('{accountlimit}', $_G['cache']['plugin']['smstong']['accountlimit'], lang('plugin/smstong', 'smstong_mobilereg_accountlimit')));
			} else {
				
				$verifycode = DB::result_first("SELECT verifycode FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND status=1 AND getip='$_G[clientip]'");

				if ($verifycode)
				{
					$content = $_G['cache']['plugin']['smstong']['mobileregmsg'];
					$rp = array('$mobile', '$verifycode');
					$sm = array($mobile, $verifycode);
					$content = str_replace($rp, $sm, $content);

					$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $content);
					
					if($ret === true)
					{	
						DB::query("UPDATE ".DB::table('common_verifycode')." SET dateline='$_G[timestamp]' WHERE mobile='$mobile' AND status=1 AND getip='$_G[clientip]'");
						
						ownshowmessage(str_replace('{mobile}', $mobile, lang('plugin/smstong', 'smstong_mobilereg_getverifycode_succeed')));
					}
					else
					{
						ownshowmessage(str_replace('{ret}', $ret, lang('plugin/smstong', 'smstong_mobilereg_getverifycode_failured')));
					}
				}
				else
				{
					$verifycode = rand(100000,999999);

					$verifycode = str_replace('1989','9819',$verifycode);
					$verifycode = str_replace('1259','9521',$verifycode);
					$verifycode = str_replace('12590','09521',$verifycode);
					$verifycode = str_replace('10086','68001',$verifycode);

					$content = $_G['cache']['plugin']['smstong']['mobileregmsg'];
					$rp = array('$mobile', '$verifycode');
					$sm = array($mobile, $verifycode);
					$content = str_replace($rp, $sm, $content);

					$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $content);
					
					if($ret === true)
					{
						$verifycode_data = array(
						'mobile' => $mobile,
						'getip' => $_G['clientip'],
						'verifycode' => $verifycode,
						'dateline' => TIMESTAMP,
						);
						DB::insert('common_verifycode', $verifycode_data);
						
						ownshowmessage(str_replace('{mobile}', $mobile, lang('plugin/smstong', 'smstong_mobilereg_getverifycode_succeed')));
					}
					else
					{
						ownshowmessage(str_replace('{ret}', $ret, lang('plugin/smstong', 'smstong_mobilereg_getverifycode_failured')));
					}
				}
			}
		}
	}
}  elseif($_GET['action'] == 'getregvoicecode') {

	if($_GET['formhash'] != $_G['formhash']) {
		ownshowmessage(lang('messgae', 'submit_invalid'));
	}

	if($_G['cache']['plugin']['smstong']['openverifycode'] == 1) {
		require_once libfile('class/seccode');

		if(DISCUZ_VERSION == 'X1.5' || DISCUZ_VERSION == 'X2' || DISCUZ_VERSION == 'X2.5' || DISCUZ_VERSION == 'X3' || $_GET['inmobile'] == 'yes') {
			if(!check_seccode($_GET['seccodeverify'], $_GET['idhash'])){
				ownshowmessage(lang('plugin/smstong', 'smstong_mobilereg_verifycode_seccode'));
			}
		}
		else
		{
			if(!check_seccode($_GET['seccodeverify'], 'c'.$_GET['idhash'], 1, '')){
				ownshowmessage(lang('plugin/smstong', 'smstong_mobilereg_verifycode_seccode'));
			}
		}
	}

	$mobile = trim($_GET['mobile']);

	if($_G['cache']['plugin']['smstong']['mobilereg'] == 0) {
		ownshowmessage(lang('plugin/smstong', 'smstong_mobilereg_closed'));
	}

	if(!ismobile($mobile)) {
		ownshowmessage(lang('plugin/smstong', 'smstong_mobilereg_mobile_invalid'));
	}
	else {
		$mobilegap = $_G['cache']['plugin']['smstong']['mobilegap'];
		$sended = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND status=1 AND getip='$_G[clientip]' AND dateline>'$_G[timestamp]'-$mobilegap");
		
		if ($sended)
		{
			ownshowmessage(str_replace('{mobilegap}', $mobilegap, lang('plugin/smstong', 'smstong_mobilereg_mobile_sended')));
		}
		else
		{
			$count = DB::result_first("SELECT count(mobile) FROM ".DB::table('common_member_profile')." WHERE mobile='$mobile'");
			if($count >= $_G['cache']['plugin']['smstong']['accountlimit']) {
				ownshowmessage(str_replace('{accountlimit}', $_G['cache']['plugin']['smstong']['accountlimit'], lang('plugin/smstong', 'smstong_mobilereg_accountlimit')));
			} else {
				
				$verifycode = DB::result_first("SELECT verifycode FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND status=1 AND getip='$_G[clientip]'");

				if ($verifycode)
				{
					$content = '&#x60A8;&#x7684;&#x9A8C;&#x8BC1;&#x7801;&#x662F;:$verifycode';
					$rp = array('$verifycode');
					$sm = array($verifycode);
					$content = str_replace($rp, $sm, $content);

					$ret = voicecode($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $verifycode, $content);
					
					if($ret === true)
					{	
						DB::query("UPDATE ".DB::table('common_verifycode')." SET dateline='$_G[timestamp]' WHERE mobile='$mobile' AND status=1 AND getip='$_G[clientip]'");
						
						ownshowmessage(str_replace('{mobile}', $mobile, lang('plugin/smstong', 'smstong_mobilereg_getverifycode_succeed')));
					}
					else
					{
						ownshowmessage(str_replace('{ret}', $ret, lang('plugin/smstong', 'smstong_mobilereg_getverifycode_failured')));
					}
				}
				else
				{
					$verifycode = rand(100000,999999);

					$verifycode = str_replace('1989','9819',$verifycode);
					$verifycode = str_replace('1259','9521',$verifycode);
					$verifycode = str_replace('12590','09521',$verifycode);
					$verifycode = str_replace('10086','68001',$verifycode);

					$content = '&#x60A8;&#x7684;&#x9A8C;&#x8BC1;&#x7801;&#x662F;:$verifycode';
					$rp = array('$verifycode');
					$sm = array($verifycode);
					$content = str_replace($rp, $sm, $content);

					$ret = voicecode($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $verifycode, $content);
					
					if($ret === true)
					{
						$verifycode_data = array(
						'mobile' => $mobile,
						'getip' => $_G['clientip'],
						'verifycode' => $verifycode,
						'dateline' => TIMESTAMP,
						);
						DB::insert('common_verifycode', $verifycode_data);
						
						ownshowmessage(str_replace('{mobile}', $mobile, lang('plugin/smstong', 'smstong_mobilereg_getverifycode_succeed')));
					}
					else
					{
						ownshowmessage(str_replace('{ret}', $ret, lang('plugin/smstong', 'smstong_mobilereg_getverifycode_failured')));
					}
				}
			}
		}
	}
} elseif($_GET['action'] == 'checkregverifycode') {

	$mobile = trim($_GET['mobile']);
	$verifycode = trim($_GET['verifycode']);

	if(!ismobile($mobile)) {
		ownshowmessage(lang('plugin/smstong', 'smstong_mobilereg_mobile_invalid'));
	}

	if(!$verifycode) {
		ownshowmessage(lang('plugin/smstong', 'smstong_mobilereg_verifycode_empty'));
	}

	$result = array();
	$query = DB::query("SELECT * FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' and verifycode='$verifycode' and getip='$_G[clientip]' and status=1");

	if($verify = DB::fetch($query)) {
		if(!empty($verify['id']) && ($_G['timestamp'] < $verify['dateline']+$_G['cache']['plugin']['smstong']['periodofvalidity'])) {
			$result = $verify;
		}
	}
	if(empty($result)) {
		dsetcookie('clientinfo', 'false');
		ownshowmessage(lang('plugin/smstong', 'smstong_mobilereg_mobile_verifycode_invalid'));
	} else {
		dsetcookie('clientinfo', 'true');
	}

} elseif($_GET['action'] == 'bindmobile') {

	if(submitcheck('bindmobilesubmit', 0, 0, 0)) {

		if($_GET['formhash'] != $_G['formhash']) {
			showmessage('submit_invalid', '', array(), array('handle' => false));
		}

		if($_G['cache']['plugin']['smstong']['openverifycode'] == 1 && ($_POST['flag'] == 2)) {
			require_once libfile('class/seccode');
			if(DISCUZ_VERSION == 'X1.5' || DISCUZ_VERSION == 'X2' || DISCUZ_VERSION == 'X2.5' || DISCUZ_VERSION == 'X3') {
				if(!check_seccode($_GET['seccodeverify'], $_GET['idhash'])){
					showmessage('smstong:smstong_mobilereg_verifycode_seccode', '', array(), array('handle' => false));
				}
			}
			else
			{
				if(!check_seccode($_GET['seccodeverify'], 'c'.$_GET['idhash'], 1, '')){
					showmessage('smstong:smstong_mobilereg_verifycode_seccode', '', array(), array('handle' => false));
				}
			}
		}
		
		$mobile = trim($_GET['newmobile']);
		$verifycode = trim($_GET['verifycode']);

		if($_G['cache']['plugin']['smstong']['mobilebind'] == 0) {
			showmessage('smstong:smstong_mobilebind_closed');
		}

		if(empty($mobile)) {
			showmessage('smstong:smstong_mobilebind_mobile_empty');
		}
		
		if(!ismobile($mobile)) {
			showmessage('smstong:smstong_mobilereg_mobile_invalid');
		}
		elseif ($_POST['flag'] == "2") {

			if ($_GET['oldmobile'] == $_GET['newmobile']) {
				ownshowmessage(lang('plugin/smstong', 'smstong_mobilebind_mobile_exists'));
			}

			$count = DB::result_first("SELECT count(mobile) FROM ".DB::table('common_member_profile')." WHERE mobile='$mobile'");
			if($count >= $_G['cache']['plugin']['smstong']['accountlimit']) {
				ownshowmessage(str_replace('{accountlimit}', $_G['cache']['plugin']['smstong']['accountlimit'], lang('plugin/smstong', 'smstong_mobilereg_accountlimit')));
			} 

			$mobilegap = $_G['cache']['plugin']['smstong']['mobilegap'];
			$sended = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND (status=3 or status=4) AND getip='$_G[clientip]' AND dateline>'$_G[timestamp]'-$mobilegap");
			
			if ($sended)
			{
				showmessage('smstong:smstong_mobilereg_mobile_sended', '', array('mobilegap' => $mobilegap));
			}
			else
			{
				$verifycode = DB::result_first("SELECT verifycode FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND status=3 AND getip='$_G[clientip]'");

				if ($verifycode)
				{
					$content = $_G['cache']['plugin']['smstong']['mobilebindmsg'];
					$rp = array('$mobile', '$verifycode');
					$sm = array($mobile, $verifycode);
					$content = str_replace($rp, $sm, $content);

					$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $content);
					
					if($ret === true)
					{
						DB::query("UPDATE ".DB::table('common_verifycode')." SET dateline='$_G[timestamp]' WHERE mobile='$mobile' AND status=3 AND getip='$_G[clientip]'");

						showmessage('smstong:smstong_mobilebind_sendsms_succeed');
					}
					else
					{
						showmessage('smstong:smstong_mobilebind_sendsms_failured', '', array('ret' => $ret));
					}
				}
				else
				{
					$verifycode = rand(100000,999999);

					$verifycode = str_replace('1989','9819',$verifycode);
					$verifycode = str_replace('1259','9521',$verifycode);
					$verifycode = str_replace('12590','09521',$verifycode);
					$verifycode = str_replace('10086','68001',$verifycode);

					$content = $_G['cache']['plugin']['smstong']['mobilebindmsg'];
					$rp = array('$mobile', '$verifycode');
					$sm = array($mobile, $verifycode);
					$content = str_replace($rp, $sm, $content);

					$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $content);

					if($ret === true)
					{
						$verifycode_data = array(
						'mobile' => $mobile,
						'getip' => $_G['clientip'],
						'verifycode' => $verifycode,
						'dateline' => TIMESTAMP,
						'reguid' => $_G['uid'],
						'status' => 3,
						);
						DB::insert('common_verifycode', $verifycode_data);

						showmessage('smstong:smstong_mobilebind_sendsms_succeed');
					}
					else
					{
						showmessage('smstong:smstong_mobilebind_sendsms_failured', '', array('ret' => $ret));
					}
				}
			}
		}
		elseif ($_POST['flag'] == "1") {
			if(empty($verifycode)) {
				showmessage('smstong:smstong_mobilebind_verifycode_empty');
			}

			$periodofvalidity = $_G['cache']['plugin']['smstong']['periodofvalidity'];
			$verify = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND verifycode='$verifycode' AND getip='$_G[clientip]' AND status=3 AND dateline>'$_G[timestamp]'-$periodofvalidity");
			
			if (!$verify)
			{
				showmessage('smstong:smstong_mobilereg_mobile_verifycode_invalid');
			}
			else
			{
				$fields = array();
				$fields['mobile'] = $mobile;
				//DB::query("UPDATE ".DB::table('common_member_profile')." SET mobile='$mobile' WHERE uid=$_G[uid]");
				DB::update('common_member_profile', $fields, array('uid'=>$_G['uid']));

				DB::query("UPDATE ".DB::table('common_verifycode')." SET reguid=$_G[uid],regdateline='$_G[timestamp]',status=4 WHERE mobile='$mobile' AND verifycode='$verifycode' AND getip='$_G[clientip]' AND status=3 AND dateline>'$_G[timestamp]'-$periodofvalidity");

				DB::query("UPDATE ".DB::table('common_member')." SET mobilestatus=1 WHERE uid=$_G[uid]");

				$usergroup = DB::result_first("SELECT type FROM ".DB::table('common_usergroup')." WHERE groupid='$_G[groupid]'");
				$groupid = $_G['cache']['plugin']['smstong']['mobilegroup'];

				if ($usergroup['type'] == 'm' && !empty($groupid)) {
					DB::query("UPDATE ".DB::table('common_member')." SET groupid='$groupid' WHERE uid=$_G[uid]");
				}
			}

			showmessage('smstong:smstong_mobilebind_succeed', 'home.php?mod=spacecp&ac=profile&op=contact');
		}
	} else {

		$periodofvalidity = $_G['cache']['plugin']['smstong']['periodofvalidity'];

		$verifycodes = DB::fetch_first("SELECT mobile,getip,dateline FROM ".DB::table('common_verifycode')." WHERE getip='$_G[clientip]' AND status=3 AND dateline>'$_G[timestamp]'-$periodofvalidity order by id desc");

		$bindsendtime = intval($verifycodes['dateline']);

		$mobilegap = intval($_G['cache']['plugin']['smstong']['mobilegap']);
		$interval = time() - $bindsendtime;
		$lastsecond = $mobilegap - $interval;

		$sendedmobile = substr($verifycodes['mobile'], 0, 4).'****'.substr($verifycodes['mobile'], 8, 3);

		$_G['sechashi'] = !empty($_G['cookie']['sechashi']) ? $_G['sechash'] + 1 : 0;
		$sechash = 'S'.($_G['inajax'] ? 'A' : '').$_G['sid'].$_G['sechashi'];

		include template('../../source/plugin/smstong/template/bindmobile');
		exit();
	}

} elseif($_GET['action'] == 'bindmobilevoicecode') {

	if(submitcheck('bindmobilesubmit', 0, 0, 0)) {

		if($_GET['formhash'] != $_G['formhash']) {
			showmessage('submit_invalid', '', array(), array('handle' => false));
		}

		if($_G['cache']['plugin']['smstong']['openverifycode'] == 1 && ($_POST['flag'] == 2)) {
			require_once libfile('class/seccode');
			if(DISCUZ_VERSION == 'X1.5' || DISCUZ_VERSION == 'X2' || DISCUZ_VERSION == 'X2.5' || DISCUZ_VERSION == 'X3') {
				if(!check_seccode($_GET['seccodeverify'], $_GET['idhash'])){
					showmessage('smstong:smstong_mobilereg_verifycode_seccode', '', array(), array('handle' => false));
				}
			}
			else
			{
				if(!check_seccode($_GET['seccodeverify'], 'c'.$_GET['idhash'], 1, '')){
					showmessage('smstong:smstong_mobilereg_verifycode_seccode', '', array(), array('handle' => false));
				}
			}
		}
		
		$mobile = trim($_GET['newmobile']);
		$verifycode = trim($_GET['verifycode']);

		if($_G['cache']['plugin']['smstong']['mobilebind'] == 0) {
			showmessage('smstong:smstong_mobilebind_closed');
		}

		if(empty($mobile)) {
			showmessage('smstong:smstong_mobilebind_mobile_empty');
		}
		
		if(!ismobile($mobile)) {
			showmessage('smstong:smstong_mobilereg_mobile_invalid');
		}
		elseif ($_POST['flag'] == "2") {

			if ($_GET['oldmobile'] == $_GET['newmobile']) {
				ownshowmessage(lang('plugin/smstong', 'smstong_mobilebind_mobile_exists'));
			}

			$count = DB::result_first("SELECT count(mobile) FROM ".DB::table('common_member_profile')." WHERE mobile='$mobile'");
			if($count >= $_G['cache']['plugin']['smstong']['accountlimit']) {
				ownshowmessage(str_replace('{accountlimit}', $_G['cache']['plugin']['smstong']['accountlimit'], lang('plugin/smstong', 'smstong_mobilereg_accountlimit')));
			} 

			$mobilegap = $_G['cache']['plugin']['smstong']['mobilegap'];
			$sended = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND (status=3 or status=4) AND getip='$_G[clientip]' AND dateline>'$_G[timestamp]'-$mobilegap");
			
			if ($sended)
			{
				showmessage('smstong:smstong_mobilereg_mobile_sended', '', array('mobilegap' => $mobilegap));
			}
			else
			{
				$verifycode = DB::result_first("SELECT verifycode FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND status=3 AND getip='$_G[clientip]'");

				if ($verifycode)
				{
					$content = '&#x60A8;&#x7684;&#x9A8C;&#x8BC1;&#x7801;&#x662F;:$verifycode';
					$rp = array('$verifycode');
					$sm = array($verifycode);
					$content = str_replace($rp, $sm, $content);

					$ret = voicecode($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $verifycode, $content);
					
					if($ret === true)
					{
						DB::query("UPDATE ".DB::table('common_verifycode')." SET dateline='$_G[timestamp]' WHERE mobile='$mobile' AND status=3 AND getip='$_G[clientip]'");

						showmessage('smstong:smstong_mobilebind_sendsms_succeed');
					}
					else
					{
						showmessage('smstong:smstong_mobilebind_sendsms_failured', '', array('ret' => $ret));
					}
				}
				else
				{
					$verifycode = rand(100000,999999);

					$verifycode = str_replace('1989','9819',$verifycode);
					$verifycode = str_replace('1259','9521',$verifycode);
					$verifycode = str_replace('12590','09521',$verifycode);
					$verifycode = str_replace('10086','68001',$verifycode);

					$content = '&#x60A8;&#x7684;&#x9A8C;&#x8BC1;&#x7801;&#x662F;:$verifycode';
					$rp = array('$verifycode');
					$sm = array($verifycode);
					$content = str_replace($rp, $sm, $content);

					$ret = voicecode($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $verifycode, $content);

					if($ret === true)
					{
						$verifycode_data = array(
						'mobile' => $mobile,
						'getip' => $_G['clientip'],
						'verifycode' => $verifycode,
						'dateline' => TIMESTAMP,
						'reguid' => $_G['uid'],
						'status' => 3,
						);
						DB::insert('common_verifycode', $verifycode_data);

						showmessage('smstong:smstong_mobilebind_sendsms_succeed');
					}
					else
					{
						showmessage('smstong:smstong_mobilebind_sendsms_failured', '', array('ret' => $ret));
					}
				}
			}
		}
	}
} elseif($_GET['action'] == 'exportmobile') {
	$query = DB::query("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE status=2 or status=4");

	while($v = DB::fetch($query)) {
		foreach($v as $key => $value) {
			$value = preg_replace('/\s+/', ' ', $value);
			$detail .= strlen($value) > 11 && is_numeric($value) ? '['.$value.'],' : $value.',';
		}
	}

	$detail = trim($detail, ","); 
	
	$filename = "mobile_".date('Ymd', TIMESTAMP).'.txt';

	ob_end_clean();

	header('Content-Encoding: none');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$filename);
	header('Pragma: no-cache');
	header('Expires: 0');

	if($_G['charset'] != 'gbk') {
		$detail = diconv($detail, $_G['charset'], 'GBK');
	}

	echo $detail;
	exit();

} elseif($_GET['action'] == 'exportverify') {
	if ($_GET['status'] == -1) {
		$status = "WHERE v.status>=0";
	} else {
		$status = "WHERE v.status=".$_GET['status'];
	}
	$query = DB::query("SELECT v.id,v.mobile,v.getip,v.verifycode,v.dateline,m.username,v.regdateline,v.status FROM ".DB::table('common_verifycode')." v LEFT JOIN ".DB::table('common_member')." m on v.reguid=m.uid $status");

	while($v = DB::fetch($query)) {
		foreach($v as $key => $value) {
			$value = preg_replace('/\s+/', ' ', $value);
			$detail .= strlen($value) == 10 && is_numeric($value) ?  date('Y-m-d H:i:s', $value).',' : $value.',';
		}
		$detail = $detail."\n";
	}

	$detail = "id,mobile,getip,verifycode,dateline,reguid,regdateline,status\n".$detail;
	
	$filename = "verify_".date('Ymd', TIMESTAMP).'.csv';

	ob_end_clean();

	header('Content-Encoding: none');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$filename);
	header('Pragma: no-cache');
	header('Expires: 0');

	if($_G['charset'] != 'gbk') {
		$detail = diconv($detail, $_G['charset'], 'GBK');
	}

	echo $detail;
	exit();

} elseif($_GET['action'] == 'exportsended') {
	$query = DB::query("SELECT id,mobile,content,addtime,senttime,count,status,remark,refno,port FROM sms_send");

	while($v = DB::fetch($query)) {
		foreach($v as $key => $value) {
			$value = preg_replace('/\s+/', ' ', $value);
			$detail .= strlen($value) > 11 && is_numeric($value) ? '['.$value.'],' : $value.',';
		}
		$detail = $detail."\n";
	}

	$detail = "id,mobile,content,addtime,senttime,count,status,remark,refno,port\n".$detail;
	
	$filename = "sended_".date('Ymd', TIMESTAMP).'.csv';

	ob_end_clean();

	header('Content-Encoding: none');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$filename);
	header('Pragma: no-cache');
	header('Expires: 0');

	if($_G['charset'] != 'gbk') {
		$detail = diconv($detail, $_G['charset'], 'GBK');
	}

	echo $detail;
	exit();

} elseif($_GET['action'] == 'exportrecved') {
	$query = DB::query("SELECT * FROM sms_recv");

	while($v = DB::fetch($query)) {
		foreach($v as $key => $value) {
			$value = preg_replace('/\s+/', ' ', $value);
			$detail .= strlen($value) > 11 && is_numeric($value) ? '['.$value.'],' : $value.',';
		}
		$detail = $detail."\n";
	}

	$detail = "id,mobile,content,recvtime,port,senttime,remark\n".$detail;
	
	$filename = "recved_".date('Ymd', TIMESTAMP).'.csv';

	ob_end_clean();

	header('Content-Encoding: none');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$filename);
	header('Pragma: no-cache');
	header('Expires: 0');

	if($_G['charset'] != 'gbk') {
		$detail = diconv($detail, $_G['charset'], 'GBK');
	}

	echo $detail;
	exit();

} elseif($_GET['action'] == 'exporthistory') {
	$query = DB::query("SELECT * FROM sms_history");

	while($v = DB::fetch($query)) {
		foreach($v as $key => $value) {
			$value = preg_replace('/\s+/', ' ', $value);
			$detail .= strlen($value) > 11 && is_numeric($value) ? '['.$value.'],' : $value.',';
		}
		$detail = $detail."\n";
	}

	$detail = "id,mobile,content,addtime,senttime,count,status,remark,refno,port\n".$detail;
	
	$filename = "history_".date('Ymd', TIMESTAMP).'.csv';

	ob_end_clean();

	header('Content-Encoding: none');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$filename);
	header('Pragma: no-cache');
	header('Expires: 0');

	if($_G['charset'] != 'gbk') {
		$detail = diconv($detail, $_G['charset'], 'GBK');
	}

	echo $detail;
	exit();

} elseif(($_GET['type'] == 'voicecode') && ($_GET['action'] == 'post' || $_GET['action'] == 'fastpost' || $_GET['action'] == 'reply' || $_GET['action'] == 'vfastpost')) {

	if(submitcheck('verifymobilesubmit', 0, 0, 0)) {

		if($_GET['formhash'] != $_G['formhash']) {
			showmessage('submit_invalid', '', array(), array('handle' => false));
		}

		if($_G['cache']['plugin']['smstong']['openverifycode'] == 1 && ($_POST['flag'] == 2)) {
			require_once libfile('class/seccode');
			if(DISCUZ_VERSION == 'X1.5' || DISCUZ_VERSION == 'X2' || DISCUZ_VERSION == 'X2.5' || DISCUZ_VERSION == 'X3') {
				if(!check_seccode($_GET['seccodeverify'], $_GET['idhash'])){
					showmessage('smstong:smstong_mobilereg_verifycode_seccode', '', array(), array('handle' => false));
				}
			}
			else
			{
				if(!check_seccode($_GET['seccodeverify'], 'c'.$_GET['idhash'], 1, '')){
					showmessage('smstong:smstong_mobilereg_verifycode_seccode', '', array(), array('handle' => false));
				}
			}
		}
		
		$mobile = trim($_GET['newmobile']);
		$verifycode = trim($_GET['verifycode']);

		if(empty($mobile)) {
			showmessage('smstong:smstong_mobilebind_mobile_empty');
		}
		
		if(!ismobile($mobile)) {
			showmessage('smstong:smstong_mobilereg_mobile_invalid');
		}
		elseif ($_POST['flag'] == "2") {

			$mobilegap = $_G['cache']['plugin']['smstong']['mobilegap'];
			$sended = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND (status=6 or status=7) AND getip='$_G[clientip]' AND dateline>'$_G[timestamp]'-$mobilegap");
			
			if ($sended)
			{
				showmessage('smstong:smstong_mobilereg_mobile_sended', '', array('mobilegap' => $mobilegap));
			}
			else
			{
				$verifycode = DB::result_first("SELECT verifycode FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND status=6 AND getip='$_G[clientip]'");

				if ($verifycode)
				{
					$content = '&#x60A8;&#x7684;&#x9A8C;&#x8BC1;&#x7801;&#x662F;:$verifycode';
					$rp = array('$verifycode');
					$sm = array($verifycode);
					$content = str_replace($rp, $sm, $content);

					$ret = voicecode($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $verifycode, $content);
					
					if($ret === true)
					{
						DB::query("UPDATE ".DB::table('common_verifycode')." SET dateline='$_G[timestamp]' WHERE mobile='$mobile' AND status=6 AND getip='$_G[clientip]'");
						
						showmessage('smstong:smstong_mobilebind_sendsms_succeed');
					}
					else
					{
						showmessage('smstong:smstong_mobilebind_sendsms_failured', '', array('ret' => $ret));
					}
				}
				else
				{
					$verifycode = rand(100000,999999);

					$verifycode = str_replace('1989','9819',$verifycode);
					$verifycode = str_replace('1259','9521',$verifycode);
					$verifycode = str_replace('12590','09521',$verifycode);
					$verifycode = str_replace('10086','68001',$verifycode);

					$content = '&#x60A8;&#x7684;&#x9A8C;&#x8BC1;&#x7801;&#x662F;:$verifycode';
					$rp = array('$verifycode');
					$sm = array($verifycode);
					$content = str_replace($rp, $sm, $content);

					$ret = voicecode($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $verifycode, $content);

					if($ret === true)
					{
						$verifycode_data = array(
						'mobile' => $mobile,
						'getip' => $_G['clientip'],
						'verifycode' => $verifycode,
						'dateline' => TIMESTAMP,
						'reguid' => $_G['uid'],
						'status' => 6,
						);
						DB::insert('common_verifycode', $verifycode_data);

						showmessage('smstong:smstong_mobilebind_sendsms_succeed');
					}
					else
					{
						showmessage('smstong:smstong_mobilebind_sendsms_failured', '', array('ret' => $ret));
					}
				}
			}
		}
	}
}  elseif($_GET['action'] == 'post' || $_GET['action'] == 'fastpost' || $_GET['action'] == 'reply' || $_GET['action'] == 'vfastpost') {

	if(submitcheck('verifymobilesubmit', 0, 0, 0)) {

		if($_GET['formhash'] != $_G['formhash']) {
			showmessage('submit_invalid', '', array(), array('handle' => false));
		}

		if($_G['cache']['plugin']['smstong']['openverifycode'] == 1 && ($_POST['flag'] == 2)) {
			require_once libfile('class/seccode');
			if(DISCUZ_VERSION == 'X1.5' || DISCUZ_VERSION == 'X2' || DISCUZ_VERSION == 'X2.5' || DISCUZ_VERSION == 'X3') {
				if(!check_seccode($_GET['seccodeverify'], $_GET['idhash'])){
					showmessage('smstong:smstong_mobilereg_verifycode_seccode', '', array(), array('handle' => false));
				}
			}
			else
			{
				if(!check_seccode($_GET['seccodeverify'], 'c'.$_GET['idhash'], 1, '')){
					showmessage('smstong:smstong_mobilereg_verifycode_seccode', '', array(), array('handle' => false));
				}
			}
		}
		
		$mobile = trim($_GET['newmobile']);
		$verifycode = trim($_GET['verifycode']);

		if(empty($mobile)) {
			showmessage('smstong:smstong_mobilebind_mobile_empty');
		}
		
		if(!ismobile($mobile)) {
			showmessage('smstong:smstong_mobilereg_mobile_invalid');
		}
		elseif ($_POST['flag'] == "2") {

			$mobilegap = $_G['cache']['plugin']['smstong']['mobilegap'];
			$sended = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND (status=6 or status=7) AND getip='$_G[clientip]' AND dateline>'$_G[timestamp]'-$mobilegap");
			
			if ($sended)
			{
				showmessage('smstong:smstong_mobilereg_mobile_sended', '', array('mobilegap' => $mobilegap));
			}
			else
			{
				$verifycode = DB::result_first("SELECT verifycode FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND status=6 AND getip='$_G[clientip]'");

				if ($verifycode)
				{
					$content = $_G['cache']['plugin']['smstong']['verifymobilemsg'];
					$rp = array('$mobile', '$verifycode');
					$sm = array($mobile, $verifycode);
					$content = str_replace($rp, $sm, $content);

					$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $content);
					
					if($ret === true)
					{
						DB::query("UPDATE ".DB::table('common_verifycode')." SET dateline='$_G[timestamp]' WHERE mobile='$mobile' AND status=6 AND getip='$_G[clientip]'");
						
						showmessage('smstong:smstong_mobilebind_sendsms_succeed');
					}
					else
					{
						showmessage('smstong:smstong_mobilebind_sendsms_failured', '', array('ret' => $ret));
					}
				}
				else
				{
					$verifycode = rand(100000,999999);

					$verifycode = str_replace('1989','9819',$verifycode);
					$verifycode = str_replace('1259','9521',$verifycode);
					$verifycode = str_replace('12590','09521',$verifycode);
					$verifycode = str_replace('10086','68001',$verifycode);

					$content = $_G['cache']['plugin']['smstong']['verifymobilemsg'];
					$rp = array('$mobile', '$verifycode');
					$sm = array($mobile, $verifycode);
					$content = str_replace($rp, $sm, $content);

					$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $content);

					if($ret === true)
					{
						$verifycode_data = array(
						'mobile' => $mobile,
						'getip' => $_G['clientip'],
						'verifycode' => $verifycode,
						'dateline' => TIMESTAMP,
						'reguid' => $_G['uid'],
						'status' => 6,
						);
						DB::insert('common_verifycode', $verifycode_data);

						showmessage('smstong:smstong_mobilebind_sendsms_succeed');
					}
					else
					{
						showmessage('smstong:smstong_mobilebind_sendsms_failured', '', array('ret' => $ret));
					}
				}
			}
		}
		elseif ($_POST['flag'] == "1") {
			if(empty($verifycode)) {
				showmessage('smstong:smstong_verifymobile_verifycode_empty');
			}

			$periodofvalidity = $_G['cache']['plugin']['smstong']['periodofvalidity'];
			$verify = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND verifycode='$verifycode' AND getip='$_G[clientip]' AND status=6 AND dateline>'$_G[timestamp]'-$periodofvalidity");
				
			if (!$verify)
			{
				showmessage('smstong:smstong_mobilereg_mobile_verifycode_invalid');
			}
			else
			{
				if ($_GET['action'] == "post") {
					showmessage('smstong:smstong_post_verifycode_right', '', '', array('extrajs' => '<script type="text/javascript">document.getElementById("postform").action=document.getElementById("postform").action+"&verifycode='.$verifycode.'&mobile='.$mobile.'";var message = wysiwyg ? html2bbcode(getEditorContents()) : document.getElementById("postform").message.value;if(document.getElementById("postform").parseurloff.checked) {message = parseurl(message);} document.getElementById("postform").message.value = message; if($(editorid + \'_attachlist\')) { $(\'postbox\').appendChild($(editorid + \'_attachlist\')); $(editorid + \'_attachlist\').style.display = \'none\';} if($(editorid + \'_imgattachlist\')) { $(\'postbox\').appendChild($(editorid + \'_imgattachlist\')); $(editorid + \'_imgattachlist\').style.display = \'none\';} document.getElementById("postform").submit();</script>'));
				} else if ($_GET['action'] == "fastpost") {
					showmessage('smstong:smstong_post_verifycode_right', '', '', array('extrajs' => '<script type="text/javascript">document.getElementById("fastpostform").action=document.getElementById("fastpostform").action+"&verifycode='.$verifycode.'&mobile='.$mobile.'";document.getElementById("fastpostform").submit();</script>'));
				} else if ($_GET['action'] == "reply") {
					showmessage('smstong:smstong_post_verifycode_right', '', '', array('extrajs' => '<script type="text/javascript">document.getElementById("postform").action=document.getElementById("postform").action+"&verifycode='.$verifycode.'&mobile='.$mobile.'";document.getElementById("postform").submit();</script>'));
				} else if ($_GET['action'] == "vfastpost") {
					showmessage('smstong:smstong_post_verifycode_right', '', '', array('extrajs' => '<script type="text/javascript">document.getElementById("vfastpostform").action=document.getElementById("vfastpostform").action+"&verifycode='.$verifycode.'&mobile='.$mobile.'";document.getElementById("vfastpostform").submit();</script>'));
				}
			}
		}
	} else {

		$periodofvalidity = $_G['cache']['plugin']['smstong']['periodofvalidity'];

		$verifycodes = DB::fetch_first("SELECT mobile,getip,dateline FROM ".DB::table('common_verifycode')." WHERE getip='$_G[clientip]' AND status=6 AND dateline>'$_G[timestamp]'-$periodofvalidity order by id desc");

		$bindsendtime = intval($verifycodes['dateline']);

		$mobilegap = intval($_G['cache']['plugin']['smstong']['mobilegap']);
		$interval = time() - $bindsendtime;
		$lastsecond = $mobilegap - $interval;

		$sendedmobile = substr($verifycodes['mobile'], 0, 4).'****'.substr($verifycodes['mobile'], 8, 3);

		$_G['sechashi'] = !empty($_G['cookie']['sechashi']) ? $_G['sechash'] + 1 : 0;
		$sechash = 'S'.($_G['inajax'] ? 'A' : '').$_G['sid'].$_G['sechashi'];

		include template('../../source/plugin/smstong/template/verifymobile');
		exit();
	}
} elseif($_GET['action'] == 'sendcustomecontent') {

	if($_GET['formhash'] != $_G['formhash']) {
			showmessage('submit_invalid', '', array(), array('handle' => false));
	}

	$mobile = DB::result_first("SELECT mobile FROM ".DB::table('common_member_profile')." WHERE uid=$_G[uid]");

	if(!ismobile($mobile)) {
		ownshowmessage(lang('plugin/smstong', 'smstong_mobilereg_mobile_invalid'));
	}

	$content = $_G['cache']['plugin']['smstong']['customecontent'];
	$rp = array('$username');
	$sm = array($_G['username']);
	$content = str_replace($rp, $sm, $content);

	$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $content);

	if($ret === true)
	{
		showmessage(str_replace('{mobile}', $mobile, lang('plugin/smstong', 'smstong_sendcustomecontent_sendsms_succeed')), '', array(), array('alert' => 'right'));
	}
	else
	{
		showmessage('smstong:smstong_sendcustomecontent_sendsms_failured', '', array('ret' => $ret));
	}

} elseif($_GET['action'] == 'phonearea') {//plugin.php?id=smstong:verifycode&action=phonearea&mobile=13333333333

	$mobile = trim($_GET['mobile']);

	$result = httprequest("http://cx.chanyoo.cn/?username=".$_G['cache']['plugin']['smstong']['smsusername']."&password=".$_G['cache']['plugin']['smstong']['smspassword']."&mobile=".$mobile);

	if ($result == 'error' || $result == 'false')
		{echo "getPhoneNumInfoExtCallback('".$result."');";exit();}
	else
		{echo "getPhoneNumInfoExtCallback(".$result.");";exit();}

}

showmessage('succeed', '', array(), array('handle' => false));

function ownshowmessage($message) {
	$inmobile = $_GET['inmobile'];

	if ($inmobile != 'yes') {
		showmessage($message);
	} else {
		echo $message;exit();
	}
}

?>