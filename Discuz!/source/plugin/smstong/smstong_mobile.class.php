<?php

/**
 *      [Discuz! X] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: smstong_mobile.class.php 291 2013-01-24 17:25:45Z Ñ½Ñ½¸öÅÞ $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class mobileplugin_smstong {

	var $allow = false;

	function mobileplugin_smstong() {
		global $_G;

		if(!$_G['cache']['plugin']['smstong']) {
			return;
		}

		$this->allow = true;
	}

	function global_header_mobile(){
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

		if($_G['uid'] && CURSCRIPT == 'forum' && $_GET['mod'] == 'post' && $_G['cache']['plugin']['smstong']['post'] && ($_GET['action'] == 'newthread') && (in_array($_G['fid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsfids'])) || empty($_G['cache']['plugin']['smstong']['postconsfids']) || $_G['cache']['plugin']['smstong']['postconsfids'] == 'a:1:{i:0;s:0:"";}') && (!in_array($_G['groupid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsusergroup'])) || empty($_G['cache']['plugin']['smstong']['postconsusergroup']) || $_G['cache']['plugin']['smstong']['postconsusergroup'] == 'a:1:{i:0;s:0:"";}') && $checktime) {
			dexit('&#26412;&#29256;&#22359;&#24050;&#32463;&#24320;&#21551;&#21457;&#24086;&#39564;&#35777;&#21151;&#33021;&#65292;&#30446;&#21069;&#31105;&#27490;&#20174;&#25163;&#26426;&#29256;&#21457;&#24086;&#65292;&#35831;&#29992;&#30005;&#33041;&#30331;&#24405;&#21518;&#25805;&#20316;&#65281;');
		}

		if($_G['uid'] && CURSCRIPT == 'forum' && $_GET['mod'] == 'post' && $_G['cache']['plugin']['smstong']['reply'] && ($_GET['action'] == 'reply') && (in_array($_G['fid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsfids'])) || empty($_G['cache']['plugin']['smstong']['postconsfids']) || $_G['cache']['plugin']['smstong']['postconsfids'] == 'a:1:{i:0;s:0:"";}') && (!in_array($_G['groupid'], (array)unserialize($_G['cache']['plugin']['smstong']['postconsusergroup'])) || empty($_G['cache']['plugin']['smstong']['postconsusergroup']) || $_G['cache']['plugin']['smstong']['postconsusergroup'] == 'a:1:{i:0;s:0:"";}') && $checktime) {
			dexit('&#26412;&#29256;&#22359;&#24050;&#32463;&#24320;&#21551;&#21457;&#24086;&#39564;&#35777;&#21151;&#33021;&#65292;&#30446;&#21069;&#31105;&#27490;&#20174;&#25163;&#26426;&#29256;&#21457;&#24086;&#65292;&#35831;&#29992;&#30005;&#33041;&#30331;&#24405;&#21518;&#25805;&#20316;&#65281;');
		}

		return;
	}

	function global_footer_mobile() {
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

			//dexit('uid:'.$uid.' mobile:'.$phone.' verifycode:'.$verifycode.' periodofvalidity:'.$periodofvalidity.' username:'.$username.' password:'.$password.' auth_hash:'.$auth_hash.' email:'.$email.' flag:'.$flag.'');
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
}

class mobileplugin_smstong_member extends mobileplugin_smstong {

	function register() {
		
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

		return;
	}

}

?>