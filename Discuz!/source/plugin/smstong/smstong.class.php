<?php

/**
 *      [Discuz! X] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: smstong.class.php 291 2011-06-01 17:06:31Z Ñ½Ñ½¸öÅÞ $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_smstong {

	var $allow = false;

	function plugin_smstong() {
		global $_G;
		include_once template('smstong:module');
		if(!$_G['cache']['plugin']['smstong']) {
			return;
		}
		$this->allow = true;
	}

	function global_header() {
		global $_G;

		if(!$this->allow) {
			return;
		}
		
		$uid = $_G['uid'];

		require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');

		if ($_G['uid']) {
			$mobile = $_G['cookie']['phone'];
			$verifycode = $_G['cookie']['vcode'];
			$periodofvalidity = empty($_G['cookie']['period']) ? $_G['cache']['plugin']['smstong']['periodofvalidity'] : $_G['cookie']['period'];
			$username = $_G['cookie']['uname'];
			$password = $_G['cookie']['upwd'];
			$auth_hash = $_G['cookie']['ahash'];
			$email = $_G['cookie']['email'];
			$flag = $_G['cookie']['flag'];

			#showmessage('uid:'.$uid.' mobile:'.$phone.' verifycode:'.$verifycode.' periodofvalidity:'.$periodofvalidity.' username:'.$username.' password:'.$password.' auth_hash:'.$auth_hash.' email:'.$email.' flag:'.$flag.'');
		}

		if($_G['uid'] && ismobile($_G['cookie']['phone']) && ($_G['cache']['plugin']['smstong']['mobilereg'] == 1 || $_G['cache']['plugin']['smstong']['qqmobilereg'] == 1 || $_G['cache']['plugin']['smstong']['mobileclientreg'] == 1) && !empty($mobile) && !$flag) {
			
			$fields = array();
			$fields['mobile'] = $mobile;
			//DB::query("UPDATE ".DB::table('common_member_profile')." SET mobile='$mobile' WHERE uid=$_G[uid]");
			DB::update('common_member_profile', $fields, array('uid'=>$_G['uid']));
			DB::query("UPDATE ".DB::table('common_verifycode')." SET reguid=$uid,regdateline='$_G[timestamp]',status=2 WHERE mobile='$mobile' AND verifycode='$verifycode' AND getip='$_G[clientip]' AND status=1 AND dateline>'$_G[timestamp]'-$periodofvalidity");
			DB::query("UPDATE ".DB::table('common_member')." SET mobilestatus=1 WHERE uid=$uid");

			if($_G['cache']['plugin']['smstong']['registernotify'] == 1 && !empty($mobile) && $mobile != 'yes') {
			
				require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');

				$content = $_G['cache']['plugin']['smstong']['registernotifymsg'];
				$rp = array('$username', '$password');
				$sm = array($username, $password);
				$content = str_replace($rp, $sm, $content);

				if (!empty($auth_hash) && empty($password)) {
					$content = str_replace(lang('plugin/smstong','smstong_mobilereg_qqregpwd'), lang('plugin/smstong','smstong_mobilereg_qqregpwdnew'), $content);
				}

				$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $content, false);
			}

			$clientinfo = array(
				'username' => $_G['cache']['plugin']['smstong']['smsusername'],
				'password' => $_G['cache']['plugin']['smstong']['smspassword'],
				'sitename' => $_G['charset'] == "gbk" ? iconv("gbk", "utf-8", $_G['setting']['bbname']) : $_G['setting']['bbname'],
				'siteurl' => $_G['siteurl'],
				'nickname' => $_G['charset'] == "gbk" ? iconv("gbk", "utf-8", $username) :  $username,
				'email' => $email,
				'mobile' => $mobile,
				'ipaddr' => $_G['clientip'],
				'regtime' => date('Y-m-d H:i:s', TIMESTAMP)
			);
			
			if(!empty($mobile) && ($_G['cookie']['clientinfo'] == 'true')) {
				require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');$_G['clientinfo'] = 'false';
				$ret = httprequest('http://cx.chanyoo.cn/clientinfo?'.http_build_query($clientinfo));
			}

			if($_G['cache']['plugin']['smstong']['newregnotify'] == 1 && !empty($_G['cache']['plugin']['smstong']['newregnotifymobile'])) {

				$content = $_G['cache']['plugin']['smstong']['newregnotifymsg'];
				$rp = array('$username');
				$sm = array($username);
				$content = str_replace($rp, $sm, $content);

				$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $_G['cache']['plugin']['smstong']['newregnotifymobile'], $content, false);
			}

			dsetcookie('uname', '');dsetcookie('upwd', '');dsetcookie('phone', '');dsetcookie('vcode', '');dsetcookie('ahash', '');dsetcookie('period', '');dsetcookie('email', '');dsetcookie('flag', '');dsetcookie('clientinfo', 'false');
		}
	}

	function global_login_mobile_autoselect() {
		global $_G;

		if(!$this->allow) {
			return;
		}

		if($_G['cache']['plugin']['smstong']['mobilelog']) {
			return tpl_global_login_mobile_autoselect();
		}

		return;
	}

	function global_footer(){
		global $_G;

		if(!$this->allow) {
			return;
		}
		
		require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');
		$data = DB::fetch_first("SELECT mobile FROM ".DB::table("common_member_profile")." WHERE uid = $_G[uid]");
		
		if($_G['cache']['plugin']['smstong']['reportmsgnotify'] && $_GET['mod'] == 'report' && $_GET['reportsubmit']){
			$content = $_G['cache']['plugin']['smstong']['reportmsgnotifymsg'];
			$url = parse_url($_SERVER['HTTP_REFERER']);
			$tids = str_replace('mod=viewthread&tid=', '', $url['query']);
			$tids = str_replace('&extra=page%3D1', '', $tids);
			$rp = array('$username', '$tid', '$message');
			$sm = array($_G['username'], $tids, $_GET['message']);
			$content = str_replace($rp, $sm, $content);

			$arraymobile = explode(',', $_G['cache']['plugin']['smstong']['reportmsgnotifymobile']);
			foreach($arraymobile as $mobile){
				$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $content);
			}
		}
		elseif($_G['cache']['plugin']['smstong']['loggingmsgnotify'] && ismobile($data['mobile']) && $_G['cookie']['loginmark'] <> $_G['cookie']['lastvisit']){
			dsetcookie('loginmark', $_G['cookie']['lastvisit']);

			require_once(DISCUZ_ROOT.'./source/function/function_misc.php');

			$content = $_G['cache']['plugin']['smstong']['loggingmsgnotifymsg'];
			$rp = array('$username', '$logtime', '$ipaddress', '$location');
			$sm = array($_G['username'], date('Y-m-d H:i:s', TIMESTAMP), $_G['clientip'], str_replace('-','',str_replace(' ','',convertip($_G['clientip']))));
			$content = str_replace($rp, $sm, $content);

			$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $data['mobile'], $content);
		}
		elseif($_G['cache']['plugin']['smstong']['displaymobilecons'] && $_G['uid'] && !ismobile($data['mobile']) && $_G['cookie']['loginmark'] <> $_G['cookie']['lastvisit']){
			dsetcookie('loginmark', $_G['cookie']['lastvisit']);
			return tpl_index_bottom_output();
		}
	}
}

class plugin_smstong_member extends plugin_smstong {

	function register_input_output() {
		global $_G;

		if(!$this->allow) {
			return;
		}
		
		if($_G['cache']['plugin']['smstong']['mobilereg']) {
			return tpl_register_input_output();
		}

		return;
	}

	function logging_input_output() {
		global $_G;

		if(!$this->allow) {
			return;
		}
		
		if($_G['cache']['plugin']['smstong']['mobilelog']) {
			return tpl_logging_input();
		}

		return;
	}

	function register_logging_method() {
		global $_G;

		if(!$this->allow) {
			return;
		}

		if(($_SERVER['REQUEST_METHOD'] == "POST") && ($_G['cache']['plugin']['smstong']['mobilereg'] == 1 || $_G['cache']['plugin']['smstong']['qqmobilereg'] == 1 || $_G['cache']['plugin']['smstong']['mobileclientreg'] == 1)) {

			require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');
				
			$mobile = trim($_GET['mobile']);
			$verifycode = trim($_GET['verifycode']);

			$periodofvalidity = $_G['cache']['plugin']['smstong']['periodofvalidity'];

			require_once libfile('function/misc');
			$iparea = trim(trim(convertip($_G['clientip']),'-'));dsetcookie('iparea', $iparea);
			$flag = $_G['cache']['plugin']['smstong']['nonlocalcheck']?strstr($_G['cache']['plugin']['smstong']['areavalue'], $iparea)?true:false:false;

			$error_count = DB::result_first("SELECT count(mobile) FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND status=9 AND dateline>'$_G[timestamp]'-2592000");
			if($error_count >= 5)
			{
				showmessage('smstong:smstong_mobilereg_mobile_verifycode_invalid1');
			}

			if($mobile == 'yes') {
			} elseif($flag) {
			} elseif($_G['setting']['sendregisterurl'] && empty($_GET['hash'])) {
			} elseif(empty($_GET['activationauth']) && empty($_GET['auth_hash']) && $_G['cache']['plugin']['smstong']['mobilereg'] == 0) {
			} elseif(empty($_GET['activationauth']) && !empty($_GET['auth_hash']) && $_G['cache']['plugin']['smstong']['qqmobilereg'] == 0) {
			} elseif(empty($mobile) && $_G['cache']['plugin']['smstong']['mobileclientreg'] == 0) {
			} elseif(empty($_GET['activationauth']) && empty($_GET['auth_hash']) &&  $_G['cache']['plugin']['smstong']['voluntaryverifyreg'] == 1 && empty($mobile)) {
			} elseif(empty($_GET['activationauth']) && empty($_GET['auth_hash']) && $_G['cache']['plugin']['smstong']['voluntaryverifyreg'] == 1 && !empty($mobile)) {

				if(!empty($mobile) && !ismobile($mobile)) {
					showmessage('smstong:smstong_mobilereg_mobile_invalid');
				}

				$count = DB::result_first("SELECT count(mobile) FROM ".DB::table('common_member_profile')." WHERE mobile='".trim($mobile)."'");
				if($count >= $_G['cache']['plugin']['smstong']['accountlimit']) {
					showmessage(str_replace('{accountlimit}', $_G['cache']['plugin']['smstong']['accountlimit'], lang('plugin/smstong', 'smstong_mobilereg_accountlimit')));
				}

				if(!empty($mobile) && empty($verifycode)) {
					showmessage('smstong:smstong_mobilereg_verifycode_empty');
				}

				$verify = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND verifycode='$verifycode' AND getip='$_G[clientip]' AND status=1 AND dateline>'$_G[timestamp]'-$periodofvalidity");

				if (!empty($mobile) && !$verify)
				{
					$verifycode_data = array(
					'mobile' => $mobile,
					'getip' => $_G['clientip'],
					'verifycode' => $verifycode,
					'dateline' => TIMESTAMP,
					'status' => 9,
					);
					DB::insert('common_verifycode', $verifycode_data);

					showmessage('smstong:smstong_mobilereg_mobile_verifycode_invalid');
				}

			} elseif($_G['cache']['plugin']['smstong']['mobileclientreg'] == 1 && ($_GET['mobile'] == '1' || $_GET['mobile'] == '2'|| $_GET['mobile'] == 'no')) {

				if(!ismobile($mobile)) {
					showmessage('smstong:smstong_mobilereg_mobile_invalid');
				}

				$count = DB::result_first("SELECT count(mobile) FROM ".DB::table('common_member_profile')." WHERE mobile='".trim($mobile)."'");
				if($count >= $_G['cache']['plugin']['smstong']['accountlimit']) {
					showmessage(str_replace('{accountlimit}', $_G['cache']['plugin']['smstong']['accountlimit'], lang('plugin/smstong', 'smstong_mobilereg_accountlimit')));
				} 

				$verifycode = trim($_GET['verifycode']);

				if(empty($verifycode)) {
					showmessage('smstong:smstong_mobilereg_verifycode_empty');
				}

				$verify = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND verifycode='$verifycode' AND getip='$_G[clientip]' AND status=1 AND dateline>'$_G[timestamp]'-$periodofvalidity");
					
				if (!$verify)
				{
					$verifycode_data = array(
					'mobile' => $mobile,
					'getip' => $_G['clientip'],
					'verifycode' => $verifycode,
					'dateline' => TIMESTAMP,
					'status' => 9,
					);
					DB::insert('common_verifycode', $verifycode_data);

					showmessage('smstong:smstong_mobilereg_mobile_verifycode_invalid');
				}

			} elseif(($_G['cache']['plugin']['smstong']['mobilereg'] == 1 || $_G['cache']['plugin']['smstong']['qqmobilereg'] == 1) && ($_GET['mobile'] != '1' && $_GET['mobile'] != '2'&& $_GET['mobile'] != 'no')) {

				if(!ismobile($mobile)) {
					showmessage('smstong:smstong_mobilereg_mobile_invalid');
				}

				$count = DB::result_first("SELECT count(mobile) FROM ".DB::table('common_member_profile')." WHERE mobile='".trim($mobile)."'");
				if($count >= $_G['cache']['plugin']['smstong']['accountlimit']) {
					showmessage(str_replace('{accountlimit}', $_G['cache']['plugin']['smstong']['accountlimit'], lang('plugin/smstong', 'smstong_mobilereg_accountlimit')));
				} 

				$verifycode = trim($_GET['verifycode']);

				if(empty($verifycode)) {
					showmessage('smstong:smstong_mobilereg_verifycode_empty');
				}

				$verify = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND verifycode='$verifycode' AND getip='$_G[clientip]' AND status=1 AND dateline>'$_G[timestamp]'-$periodofvalidity");
					
				if (!$verify)
				{
					$verifycode_data = array(
					'mobile' => $mobile,
					'getip' => $_G['clientip'],
					'verifycode' => $verifycode,
					'dateline' => TIMESTAMP,
					'status' => 9,
					);
					DB::insert('common_verifycode', $verifycode_data);

					showmessage('smstong:smstong_mobilereg_mobile_verifycode_invalid');
				}
			}
			
			dsetcookie('uname', $_GET[$_G[setting][reginput][username]]);dsetcookie('upwd', $_GET[$_G[setting][reginput][password]]);dsetcookie('phone', $mobile);dsetcookie('vcode', $verifycode);dsetcookie('ahash', $_GET['auth_hash']);dsetcookie('period', $periodofvalidity);dsetcookie('email', $_GET[$_G[setting][reginput][email]]);dsetcookie('flag', $flag);
		}
	}
}

class plugin_smstong_home extends plugin_smstong {

	function spacecp_profile_bottom_output() {
		global $_G;

		if(!$this->allow) {
			return;
		}

		if($_G['cache']['plugin']['smstong']['mobilebind']) {
			return tpl_spacecp_profile_bottoms();
		}

		return;
	}

}

class plugin_smstong_forum extends plugin_smstong {

	function viewthread_avatar_output() {
		global $_G,$postlist;

		if(!$this->allow) {
			return;
		}

		if($_G['cache']['plugin']['smstong']['displaythreadmobile']) {

			foreach ($postlist as $id=>$post) {
				if ($post['authorid']) {
					$target .= $post['authorid'].',';
				}
			}

			$target = substr($target, 0, -1);

			if (!empty($target)) {
				$query = DB::query("SELECT * FROM ".DB::table("common_member_profile")." WHERE uid in ($target)");
					while ($data = DB::fetch($query)) {
						$user[$data['uid']] = $data;
				}

				include_once DISCUZ_ROOT . './data/plugindata/smstong.lang.php';
				require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');

				foreach($user as $uid=>$ex) {
					if (ismobile($user[$uid]['mobile'])) {
						$mobile[$uid] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='source/plugin/smstong/mobile.gif' title='".lang('plugin/smstong', 'smstong_mobilebind_bindimage')."' />";
					}
				}

				foreach($postlist as $id=>$post) {
					$return[] = $mobile[$post['uid']]; 
				}

				return $return;
			}

			return;
		}

		return;
	}

	function post_btn_extra_output() {
		global $_G;

		if(!$this->allow) {
			return;
		}

		$checktime = false;

		if(!empty($_G['cache']['plugin']['smstong']['postareaconstime'])) {
			$now = dgmdate(TIMESTAMP, 'G.i');
			foreach(explode("\r\n", str_replace(':', '.', $_G['cache']['plugin']['smstong']['postareaconstime'])) as $period) {
				list($periodbegin, $periodend) = explode('-', $period);
				if(($periodbegin > $periodend && ($now >= $periodbegin || $now < $periodend)) || ($periodbegin < $periodend && $now >= $periodbegin && $now < $periodend)) {
					$checktime = true;
				}
			}
		} else {
			$checktime = true;
		}

		if($_G['cache']['plugin']['smstong']['post'] && ($_GET['action'] == 'newthread') && (in_array($_G['fid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsfids'])) || empty($_G['cache']['plugin']['smstong']['postconsfids']) || $_G['cache']['plugin']['smstong']['postconsfids'] == 'a:1:{i:0;s:0:"";}') && (!in_array($_G['groupid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsusergroup'])) || empty($_G['cache']['plugin']['smstong']['postconsusergroup']) || $_G['cache']['plugin']['smstong']['postconsusergroup'] == 'a:1:{i:0;s:0:"";}') && $checktime) {
			return tpl_post_btn_extra_output();
		}

		if($_G['cache']['plugin']['smstong']['reply'] && ($_GET['action'] == 'reply') && (in_array($_G['fid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsfids'])) || empty($_G['cache']['plugin']['smstong']['postconsfids']) || $_G['cache']['plugin']['smstong']['postconsfids'] == 'a:1:{i:0;s:0:"";}') && (!in_array($_G['groupid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsusergroup'])) || empty($_G['cache']['plugin']['smstong']['postconsusergroup']) || $_G['cache']['plugin']['smstong']['postconsusergroup'] == 'a:1:{i:0;s:0:"";}') && $checktime) {
			return tpl_post_btn_extra_output();
		}

		return;
	}

	function forumdisplay_fastpost_btn_extra_output() {
		global $_G;

		if(!$this->allow) {
			return;
		}

		$checktime = false;

		if(!empty($_G['cache']['plugin']['smstong']['postareaconstime'])) {
			$now = dgmdate(TIMESTAMP, 'G.i');
			foreach(explode("\r\n", str_replace(':', '.', $_G['cache']['plugin']['smstong']['postareaconstime'])) as $period) {
				list($periodbegin, $periodend) = explode('-', $period);
				if(($periodbegin > $periodend && ($now >= $periodbegin || $now < $periodend)) || ($periodbegin < $periodend && $now >= $periodbegin && $now < $periodend)) {
					$checktime = true;
				}
			}
		} else {
			$checktime = true;
		}

		if($_G['cache']['plugin']['smstong']['post'] && (in_array($_G['fid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsfids'])) || empty($_G['cache']['plugin']['smstong']['postconsfids']) || $_G['cache']['plugin']['smstong']['postconsfids'] == 'a:1:{i:0;s:0:"";}') && (!in_array($_G['groupid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsusergroup'])) || empty($_G['cache']['plugin']['smstong']['postconsusergroup']) || $_G['cache']['plugin']['smstong']['postconsusergroup'] == 'a:1:{i:0;s:0:"";}') && $checktime) {
			return tpl_forumdisplay_fastpost_btn_extra_output();
		}

		return;
	}

	function viewthread_bottom_output() {
		global $_G;

		if(!$this->allow) {
			return;
		}

		$checktime = false;

		if(!empty($_G['cache']['plugin']['smstong']['postareaconstime'])) {
			$now = dgmdate(TIMESTAMP, 'G.i');
			foreach(explode("\r\n", str_replace(':', '.', $_G['cache']['plugin']['smstong']['postareaconstime'])) as $period) {
				list($periodbegin, $periodend) = explode('-', $period);
				if(($periodbegin > $periodend && ($now >= $periodbegin || $now < $periodend)) || ($periodbegin < $periodend && $now >= $periodbegin && $now < $periodend)) {
					$checktime = true;
				}
			}
		} else {
			$checktime = true;
		}

		if($_G['cache']['plugin']['smstong']['reply'] && (in_array($_G['fid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsfids'])) || empty($_G['cache']['plugin']['smstong']['postconsfids']) || $_G['cache']['plugin']['smstong']['postconsfids'] == 'a:1:{i:0;s:0:"";}') && (!in_array($_G['groupid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsusergroup'])) || empty($_G['cache']['plugin']['smstong']['postconsusergroup']) || $_G['cache']['plugin']['smstong']['postconsusergroup'] == 'a:1:{i:0;s:0:"";}') && $checktime) {
			return tpl_forumdisplay_fastpost_btn_extra_output();
		}

		return;
	}

	function post_infloat_btn_extra_output() {
		global $_G;

		if(!$this->allow) {
			return;
		}

		$checktime = false;

		if(!empty($_G['cache']['plugin']['smstong']['postareaconstime'])) {
			$now = dgmdate(TIMESTAMP, 'G.i');
			foreach(explode("\r\n", str_replace(':', '.', $_G['cache']['plugin']['smstong']['postareaconstime'])) as $period) {
				list($periodbegin, $periodend) = explode('-', $period);
				if(($periodbegin > $periodend && ($now >= $periodbegin || $now < $periodend)) || ($periodbegin < $periodend && $now >= $periodbegin && $now < $periodend)) {
					$checktime = true;
				}
			}
		} else {
			$checktime = true;
		}

		if($_G['cache']['plugin']['smstong']['post'] && ($_GET['action'] == 'newthread') && (in_array($_G['fid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsfids'])) || empty($_G['cache']['plugin']['smstong']['postconsfids']) || $_G['cache']['plugin']['smstong']['postconsfids'] == 'a:1:{i:0;s:0:"";}') && (!in_array($_G['groupid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsusergroup'])) || empty($_G['cache']['plugin']['smstong']['postconsusergroup']) || $_G['cache']['plugin']['smstong']['postconsusergroup'] == 'a:1:{i:0;s:0:"";}') && $checktime) {
			return tpl_post_infloat_btn_extra_output();
		}

		if($_G['cache']['plugin']['smstong']['reply'] && ($_GET['action'] == 'reply') && (in_array($_G['fid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsfids'])) || empty($_G['cache']['plugin']['smstong']['postconsfids']) || $_G['cache']['plugin']['smstong']['postconsfids'] == 'a:1:{i:0;s:0:"";}') && (!in_array($_G['groupid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsusergroup'])) || empty($_G['cache']['plugin']['smstong']['postconsusergroup']) || $_G['cache']['plugin']['smstong']['postconsusergroup'] == 'a:1:{i:0;s:0:"";}') && $checktime) {
			return tpl_post_infloat_btn_extra_output();
		}

		return;
	}

	function viewthread_fastpost_content_output() {
		global $_G;

		if(!$this->allow) {
			return;
		}

		$checktime = false;

		if(!empty($_G['cache']['plugin']['smstong']['postareaconstime'])) {
			$now = dgmdate(TIMESTAMP, 'G.i');
			foreach(explode("\r\n", str_replace(':', '.', $_G['cache']['plugin']['smstong']['postareaconstime'])) as $period) {
				list($periodbegin, $periodend) = explode('-', $period);
				if(($periodbegin > $periodend && ($now >= $periodbegin || $now < $periodend)) || ($periodbegin < $periodend && $now >= $periodbegin && $now < $periodend)) {
					$checktime = true;
				}
			}
		} else {
			$checktime = true;
		}

		if($_G['cache']['plugin']['smstong']['reply'] && (in_array($_G['fid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsfids'])) || empty($_G['cache']['plugin']['smstong']['postconsfids']) || $_G['cache']['plugin']['smstong']['postconsfids'] == 'a:1:{i:0;s:0:"";}') && (!in_array($_G['groupid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsusergroup'])) || empty($_G['cache']['plugin']['smstong']['postconsusergroup']) || $_G['cache']['plugin']['smstong']['postconsusergroup'] == 'a:1:{i:0;s:0:"";}') && $checktime) {
			return tpl_viewthread_fastpost_content_output();
		}

		return;
	}

	function post_sync_method() {
		global $_G;

		if(!$this->allow) {
			return;
		}

		$checktime = false;

		if(!empty($_G['cache']['plugin']['smstong']['postareaconstime'])) {
			$now = dgmdate(TIMESTAMP, 'G.i');
			foreach(explode("\r\n", str_replace(':', '.', $_G['cache']['plugin']['smstong']['postareaconstime'])) as $period) {
				list($periodbegin, $periodend) = explode('-', $period);
				if(($periodbegin > $periodend && ($now >= $periodbegin || $now < $periodend)) || ($periodbegin < $periodend && $now >= $periodbegin && $now < $periodend)) {
					$checktime = true;
				}
			}
		} else {
			$checktime = true;
		}

		if($_G['cache']['plugin']['smstong']['post'] && $_SERVER['REQUEST_METHOD'] == "POST" && $_GET['action'] == 'newthread') {
			if((in_array($_G['fid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsfids'])) || empty($_G['cache']['plugin']['smstong']['postconsfids']) || $_G['cache']['plugin']['smstong']['postconsfids'] == 'a:1:{i:0;s:0:"";}') && (!in_array($_G['groupid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsusergroup'])) || empty($_G['cache']['plugin']['smstong']['postconsusergroup']) || $_G['cache']['plugin']['smstong']['postconsusergroup'] == 'a:1:{i:0;s:0:"";}') && $checktime) {
				if(empty($_GET['verifycode'])) {
					showmessage('smstong:smstong_post_mobile_verifycode_invalid');
				}
			}
		}

		if($_G['cache']['plugin']['smstong']['reply'] && $_SERVER['REQUEST_METHOD'] == "POST" && $_GET['action'] == 'reply') {
			if((in_array($_G['fid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsfids'])) || empty($_G['cache']['plugin']['smstong']['postconsfids']) || $_G['cache']['plugin']['smstong']['postconsfids'] == 'a:1:{i:0;s:0:"";}') && (!in_array($_G['groupid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsusergroup'])) || empty($_G['cache']['plugin']['smstong']['postconsusergroup']) || $_G['cache']['plugin']['smstong']['postconsusergroup'] == 'a:1:{i:0;s:0:"";}') && $checktime) {
				if(empty($_GET['verifycode'])) {
					showmessage('smstong:smstong_post_mobile_verifycode_invalid');
				}
			}
		}

		if(!empty($_GET['message'])) {
			$periodofvalidity = $_G['cache']['plugin']['smstong']['periodofvalidity'];

			$verify = DB::result_first("SELECT verifycode FROM ".DB::table('common_verifycode')." WHERE verifycode='$_GET[verifycode]' AND getip='$_G[clientip]' AND status=6 AND dateline>'$_G[timestamp]'-$periodofvalidity");

			if ($verify != $_GET['verifycode'])
			{
				showmessage('smstong:smstong_post_mobile_verifycode_invalid');
			}
			else
			{
				DB::query("UPDATE ".DB::table('common_verifycode')." SET reguid=$_G[uid],regdateline='$_G[timestamp]',status=7 WHERE mobile='$_GET[mobile]' AND verifycode='$_GET[verifycode]' AND getip='$_G[clientip]' AND status=6 AND dateline>'$_G[timestamp]'-$periodofvalidity");
			}
		}

		return;
	}

	function index_top_output() {
		global $_G;

		if(!$this->allow) {
			return;
		}

		if($_G['cache']['plugin']['smstong']['sendcustomecontent']) {
			return index_top_output();
		}

		return;
	}

}

?>