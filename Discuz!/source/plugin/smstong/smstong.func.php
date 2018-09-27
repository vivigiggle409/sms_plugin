<?php

/*
	[Discuz!] (C)2001-2009 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: sendsms.func.php 20894 2011-06-07 16:34:59Z 呀呀个呸 $
*/

function ismobile($mobile)
{
	return (strlen($mobile) == 11) && (preg_match("/^13\d{9}$/", $mobile) || preg_match("/^14\d{9}$/", $mobile) || preg_match("/^15\d{9}$/", $mobile) || preg_match("/^17\d{9}$/", $mobile) || preg_match("/^18\d{9}$/", $mobile));
}

function sendsms($user, $pass, $mobile, $content, $checkmobile=true, $refno='', $creditchange=true)
{
	if(empty($_SESSION['smstong_mobile']) || $mobile != $_SESSION['smstong_mobile']) {
		$_SESSION['smstong_mobile'] = $mobile;

		global $_G;

		$ret = true;

		$content = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $content);

		$content = str_replace(array('[', ']', 'url=', '/url', 'img', '/img'), array('', '', '', '', '', ''), $content);

		$content = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $content);

		$whiteiplist_array = explode(",", $_G['cache']['plugin']['smstong']['whiteiplist']);

		if(!in_array($_G['clientip'], $whiteiplist_array)) {
			$times = $_G['timestamp']-86400;
			$times = date('Y-m-d H:i:s', $times);
			if($checkmobile) {
				$count1 = DB::result_first("SELECT count(id) FROM ".DB::table('common_verifycode')." WHERE getip='$_G[clientip]' AND dateline>'$_G[timestamp]'-86400");

				if ($count1 >= $_G['cache']['plugin']['smstong']['deniedip'])
				{
					return lang('plugin/smstong','smstong_ipcheck_access_denied');
				}

				$count2 = DB::result_first("SELECT count(id) FROM sms_send WHERE mobile='$mobile' AND addtime>'$times'");

				if ($count2 >= $_G['cache']['plugin']['smstong']['deniedip'])
				{
					return lang('plugin/smstong','smstong_ipcheck_access_denied');
				}
			} else {
				$count2 = DB::result_first("SELECT count(id) FROM sms_send WHERE mobile='$mobile' AND addtime>'$times'");

				if ($count2 >= $_G['cache']['plugin']['smstong']['deniedip'] && !in_array($mobile, explode(",", $_G['cache']['plugin']['smstong']['reportmsgnotifymobile'])))
				{
					return true;//一天超过限制次数跳过发送直接成功
				}
			}
		}

		if ($creditchange) {
			$user_id = DB::result_first("SELECT uid FROM ".DB::table('common_member_profile')." WHERE mobile='$mobile'");
			$user_id = $user_id > 0 ? $user_id : $_G['uid'];
			updatemembercount($user_id, array("extcredits{$_G['cache']['plugin']['smstong']['extcredittype']}" => - $_G['cache']['plugin']['smstong']['extcreditamount']));
		}

		if ($checkmobile)
			$ret = chackmobile1($mobile);

		if ($ret != true && $ret != false)
			$ret = chackmobile2($mobile);

		if($ret === true) {
			$content = str_replace('1989','1 9 8 9',$content);
			$content = str_replace('1259','1 2 5 9',$content);
			$content = str_replace('12590','1 2 5 9 0',$content);
			$content = str_replace('10086','1 0 0 8 6',$content);

			$smsapi = "api.chanyoo.cn";
			$charset = "gbk";

			if ($_G['charset'] != "gbk") {
				$charset = "utf8";
			}

			if (empty($_G['cache']['plugin']['smstong']['appkey']) && empty($_G['cache']['plugin']['smstong']['appsecret'])) {
				if ($_G['cache']['plugin']['smstong']['sendtype'] == "1") {

					$appid = $_G['cache']['plugin']['smstong']['smsusername'];
					$appkey = $_G['cache']['plugin']['smstong']['smspassword'];

					$message = lang('plugin/smstong','smstong_function_sign_left').$_G['cache']['plugin']['smstong']['smstongsign'].lang('plugin/smstong','smstong_function_sign_right').$content;

					if ($_G['charset'] == "gbk")
						$message1 = iconv('GBK', 'UTF-8//IGNORE', $message);
					else
						$message1 = $message;

					$random = rand(100000,999999);$times = time(); $mobile1 = "+86".$mobile;

					$sign = "appkey=".$appkey."&random=".$random."&time=".$times."&tel=".$mobile1;
	//return $sign;
					$sig = hash("sha256", $sign, FALSE);
	//return $sig;
					$postdata = "{\"tel\": \"".$mobile1."\",\"type\": 0,\"msg\": \"".$message1."\",\"sig\": \"".$sig."\", \"time\": ".$times.",\"extend\": \"\",\"ext\": \"\"}";
	//return $postdata;
					$posturl = "https://yun.tim.qq.com/v5/tlssmssvr/sendisms?sdkappid=".$appid."&random=".$random;

					$ret = sendCurlPost($posturl, $postdata);
	//return $ret;

					if ($ret && stristr($ret, 'OK')) {

						$length = mb_strlen($content, $_G['charset']);
						$count = ceil($length/64);
						$addtime = date('Y-m-d H:i:s', TIMESTAMP);

						$mobiles = explode(',', $mobile);

						foreach($mobiles as $k => $v) {
							if(empty($refno)) {
								DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port) values('$v', '$message', '$addtime', '$addtime', $count, 1, 'qcloud')");
							}
							else if(strpos($refno, ',')) {
								DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port,remark) values('$v', '$message', '$addtime', '$addtime', $count, 1, 'qcloud', '$refno')");
							} else {
								DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port,refno) values('$v', '$message', '$addtime', '$addtime', $count, 1, 'qcloud', $refno)");
							}
						}

						return true;
					}

					if ($_G['charset'] == "gbk")
						return iconv("utf-8", "gbk", $ret);
					else
						return $ret;
					
				} elseif ($_G['cache']['plugin']['smstong']['sendtype'] == "2" || $_G['cache']['plugin']['smstong']['sendtype'] == "3" || $_G['cache']['plugin']['smstong']['sendtype'] == "4") {

					if (empty($_G['cache']['plugin']['smstong']['smstongsign'])) {
						$content = $content.lang('plugin/smstong','smstong_function_sign_left').$_G['setting']['bbname'].lang('plugin/smstong','smstong_function_sign_right');
					} else {
						$content = $content.lang('plugin/smstong','smstong_function_sign_left').$_G['cache']['plugin']['smstong']['smstongsign'].lang('plugin/smstong','smstong_function_sign_right');
					}
					
					if ($_G['cache']['plugin']['smstong']['sendtype'] == "2") {
						$smsapi = "api.chanyoo.cn";
					} elseif ($_G['cache']['plugin']['smstong']['sendtype'] == "3") {
						$smsapi = "a1.chanyoo.cn";
					} elseif ($_G['cache']['plugin']['smstong']['sendtype'] == "4") {
						$smsapi = "a2.chanyoo.cn";
					}
					
					$sendurl = "http://".$smsapi."/".$charset."/interface/send_sms.aspx?username=".urlencode($user)."&password=".urlencode($pass)."&receiver=".urlencode($mobile)."&content=".urlencode($content)."";

					$result = httprequest($sendurl);

					if (empty($result)) return lang('plugin/smstong','smstong_notice_failured');

					if (stristr($result, lang('plugin/smstong','smstong_serverip_denied_flag')) || stristr($result, '403 Forbidden')) return lang('plugin/smstong','smstong_serverip_denied_content');

					$xml = simplexml_load_string($result);

					if ($xml->result >= 0)
					{
						$length = mb_strlen($content, $_G['charset']);
						$count = ceil($length/64);
						$addtime = date('Y-m-d H:i:s', TIMESTAMP);

						$mobiles = explode(',', $mobile);

						foreach($mobiles as $k => $v) {
							if(empty($refno)) {
								DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'chanyoo')");
							}
							else if(strpos($refno, ',')) {
								DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port,remark) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'chanyoo', '$refno')");
							} else {
								DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port,refno) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'chanyoo', $refno)");
							}
						}

						return true;
					}
					else
					{
						if ($_G['charset'] == "gbk")
							return iconv("utf-8", "gbk", $xml->message);
						else
							return $xml->message;
					}
				}
			}
			else {
				include DISCUZ_ROOT.'./source/plugin/smstong/aliyun/Autoloader.php';
				//include_once 'aliyun/Util/Autoloader.php';

				$content = $content.lang('plugin/smstong','smstong_function_sign_left').$_G['cache']['plugin']['smstong']['smstongsign'].lang('plugin/smstong','smstong_function_sign_right');
				if ($_G['charset'] == "gbk")
					$content = iconv('GBK', 'UTF-8//IGNORE', $content);

				$appKey = $_G['cache']['plugin']['smstong']['appkey'];
				$appSecret = $_G['cache']['plugin']['smstong']['appsecret'];
				$host = "https://aliyun.chanyoo.net";

				//域名后、query前的部分
				$path = "/sendsms";
				$request = new HttpRequest($host, $path, HttpMethod::GET, $appKey, $appSecret);

				//设定Content-Type，根据服务器端接受的值来设置
				$request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_TEXT);
				
				//设定Accept，根据服务器端接受的值来设置
				$request->setHeader(HttpHeader::HTTP_HEADER_ACCEPT, ContentType::CONTENT_TYPE_TEXT);

				//注意：业务query部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
				$request->setQuery("mobile", $mobile);
				$request->setQuery("content", $content);

				$response = HttpClient::execute($request);

				if($response->getSuccess()){
					return true;
				}else{
					$result = 'API Error '. $response->getHttpStatusCode() . ' '. ($response->getErrorMessage()==""?json_decode($response->getBody())->errmsg:$response->getErrorMessage());
					if ($_G['charset'] == "gbk")
						$result = iconv('UTF-8', 'GBK//IGNORE', $result);
					return $result;
				}
			}
		}
		 else {
			return $ret;
		}
	} else {
		return lang('plugin/smstong','smstong_ipcheck_denied_mobile');
	}
}

function voicecode($user, $pass, $mobile, $verifycode, $content, $checkmobile=true, $refno='', $creditchange=true)
{
	if(empty($_SESSION['smstong_mobile']) || $mobile != $_SESSION['smstong_mobile']) {
		$_SESSION['smstong_mobile'] = $mobile;

		global $_G;

		$ret = true;

		$whiteiplist_array = explode(",", $_G['cache']['plugin']['smstong']['whiteiplist']);

		if(!in_array($_G['clientip'], $whiteiplist_array)) {
			$times = $_G['timestamp']-86400;
			$times = date('Y-m-d H:i:s', $times);
			if($checkmobile) {
				$count1 = DB::result_first("SELECT count(id) FROM ".DB::table('common_verifycode')." WHERE getip='$_G[clientip]' AND dateline>'$_G[timestamp]'-86400");

				if ($count1 >= $_G['cache']['plugin']['smstong']['deniedip'])
				{
					return lang('plugin/smstong','smstong_ipcheck_access_denied');
				}

				$count2 = DB::result_first("SELECT count(id) FROM sms_send WHERE mobile='$mobile' AND addtime>'$times'");

				if ($count2 >= $_G['cache']['plugin']['smstong']['deniedip'])
				{
					return lang('plugin/smstong','smstong_ipcheck_access_denied');
				}
			} else {
				$count2 = DB::result_first("SELECT count(id) FROM sms_send WHERE mobile='$mobile' AND addtime>'$times'");

				if ($count2 >= $_G['cache']['plugin']['smstong']['deniedip'] && !in_array($mobile, explode(",", $_G['cache']['plugin']['smstong']['reportmsgnotifymobile'])))
				{
					return true;//一天超过限制次数跳过发送直接成功
				}
			}
		}

		if ($creditchange) {
			$user_id = DB::result_first("SELECT uid FROM ".DB::table('common_member_profile')." WHERE mobile='$mobile'");
			$user_id = $user_id > 0 ? $user_id : $_G['uid'];
			updatemembercount($user_id, array("extcredits{$_G['cache']['plugin']['smstong']['extcredittype']}" => - $_G['cache']['plugin']['smstong']['extcreditamount']));
		}

		if ($checkmobile)
			$ret = chackmobile1($mobile);

		if ($ret != true && $ret != false)
			$ret = chackmobile2($mobile);

		if($ret === true) {
			
			if ($_G['cache']['plugin']['smstong']['sendtype'] == "1") {

				$appid = $_G['cache']['plugin']['smstong']['smsusername'];
				$appkey = $_G['cache']['plugin']['smstong']['smspassword'];

				$random = rand(100000,999999);$times = time();

				$sign = "appkey=".$appkey."&random=".$random."&time=".$times."&mobile=".$mobile;
//return $sign;
				$sig = hash("sha256", $sign, FALSE);
//return $sig;
				$postdata = "{\"tel\": {\"nationcode\": \"86\",\"mobile\": \"".$mobile."\"},\"msg\": \"".$verifycode."\",\"playtimes\": 3,\"sig\": \"".$sig."\", \"time\": ".$times.",\"ext\": \"\"}";
//return $postdata;
				$posturl = "https://yun.tim.qq.com/v5/tlsvoicesvr/sendvoice?sdkappid=".$appid."&random=".$random;

				$ret = sendCurlPost($posturl, $postdata);
//return $ret;

				if ($ret && stristr($ret, 'OK')) {

					$length = mb_strlen($content, $_G['charset']);
					$count = ceil($length/64);
					$addtime = date('Y-m-d H:i:s', TIMESTAMP);

					$mobiles = explode(',', $mobile);

					foreach($mobiles as $k => $v) {
						if(empty($refno)) {
							DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'voicecode')");
						}
						else if(strpos($refno, ',')) {
							DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port,remark) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'voicecode', '$refno')");
						} else {
							DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port,refno) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'voicecode', $refno)");
						}
					}

					return true;
				}
				else
				{
					if ($_G['charset'] == "gbk")
						return iconv("utf-8", "gbk", $ret);
					else
						return $ret;
				}
				
			} elseif ($_G['cache']['plugin']['smstong']['sendtype'] == "2" || $_G['cache']['plugin']['smstong']['sendtype'] == "3" || $_G['cache']['plugin']['smstong']['sendtype'] == "4") {

				$smsapi = "api.chanyoo.cn";
				$charset = "gbk";

				if ($_G['charset'] != "gbk") {
					$charset = "utf8";
				}

				if ($_G['cache']['plugin']['smstong']['sendtype'] == "2") {
					$smsapi = "api.chanyoo.cn";
				} elseif ($_G['cache']['plugin']['smstong']['sendtype'] == "3") {
					$smsapi = "a1.chanyoo.cn";
				} elseif ($_G['cache']['plugin']['smstong']['sendtype'] == "4") {
					$smsapi = "a2.chanyoo.cn";
				}
				
				$sendurl = "http://".$smsapi."/".$charset."/interface/send_voice.aspx?username=".urlencode($user)."&password=".urlencode($pass)."&receiver=".urlencode($mobile)."&content=".urlencode($verifycode)."";

				$result = httprequest($sendurl);

				if (empty($result)) return lang('plugin/smstong','smstong_notice_failured');

				if (stristr($ret, lang('plugin/smstong','smstong_serverip_denied_flag')) || stristr($ret, '403 Forbidden')) return lang('plugin/smstong','smstong_serverip_denied_content');

				$xml = simplexml_load_string($result);

				if ($xml->result >= 0)
				{
					$length = mb_strlen($content, $_G['charset']);
					$count = ceil($length/64);
					$addtime = date('Y-m-d H:i:s', TIMESTAMP);

					$mobiles = explode(',', $mobile);

					foreach($mobiles as $k => $v) {
						if(empty($refno)) {
							DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'voicecode')");
						}
						else if(strpos($refno, ',')) {
							DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port,remark) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'voicecode', '$refno')");
						} else {
							DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port,refno) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'voicecode', $refno)");
						}
					}

					return true;
				}
				else
				{
					if ($_G['charset'] == "gbk")
						return iconv("utf-8", "gbk", $xml->message);
					else
						return $xml->message;
				}
			}
		}
		 else {
			return $ret;
		}
	} else {
		return lang('plugin/smstong','smstong_ipcheck_denied_mobile');
	}
}

function chackmobile1($mobile) {

	global $_G;

	$mobile_array = explode(",", $_G['cache']['plugin']['smstong']['blackmobile']);

	if(in_array($_G['clientip'], $mobile_array))
		return lang('plugin/smstong','smstong_blackip_existed');

	if(in_array($mobile, $mobile_array))
		return lang('plugin/smstong','smstong_blackmobile_existed');

	foreach($mobile_array as $k => $v) {
		if(strpos($mobile, $v)===0)
		return lang('plugin/smstong','smstong_blackmobile_existed');
	}

	if ($_G['cache']['plugin']['smstong']['areacons'] == "0") return true;

	if (empty($_G['cache']['plugin']['smstong']['areavalue'])) return true;

	$checkmobile = false;

	if(!empty($_G['cache']['plugin']['smstong']['areaconstime'])) {
		$now = dgmdate(TIMESTAMP, 'G.i');
		foreach(explode("\r\n", str_replace(':', '.', $_G['cache']['plugin']['smstong']['areaconstime'])) as $period) {
			list($periodbegin, $periodend) = explode('-', $period);
			if(($periodbegin > $periodend && ($now >= $periodbegin || $now < $periodend)) || ($periodbegin < $periodend && $now >= $periodbegin && $now < $periodend)) {
				$checkmobile = true;
			}
		}
	} else {
		$checkmobile = true;
	}

	if($_G['cache']['plugin']['smstong']['areacons'] == "1" && $checkmobile) {

		$checkurl = "https://cx.shouji.360.cn/phonearea.php?number=".$mobile;

		$result = httprequest($checkurl);

		$checkresult = "";
		$errormsg = lang('plugin/smstong','smstong_checkmobile_error');

		$result = strip_tags($result);
		$result = preg_replace('/\s/', '', $result);
//return iconv("utf-8", "gbk", $result);
		if(empty($result) || $result == lang('plugin/smstong','smstong_checkmobile_fast')) return $errormsg;
//return $result;
		$phonearea = json_decode($result, true);
//return $phonearea["data"]["province"].$phonearea["data"]["city"]."1";
		switch ($_G['cache']['plugin']['smstong']['areatype'])
		{
			case 1 :
			{
				if ($_G['charset'] == "utf-8") {
					$checkresult = $phonearea["data"]["province"].$phonearea["data"]["city"];
				}
				else {
					preg_match(lang('plugin/smstong','smstong_mobilearea_xpcha'), iconv("utf-8", "gbk", $result), $area);
					$checkresult =  iconv("utf-8", "gbk", $phonearea["data"]["province"]).iconv("utf-8", "gbk", $phonearea["data"]["city"]);
				}

				$errormsg = lang('plugin/smstong','smstong_checkmobile_default').$_G['cache']['plugin']['smstong']['areavalue'];
			}
			break;
			case 2 :
			{
				if ($_G['charset'] == "utf-8") {
					$checkresult = $phonearea["data"]["province"].$phonearea["data"]["city"];
				}
				else {
					preg_match(lang('plugin/smstong','smstong_mobilearea_xpcha'), iconv("utf-8", "gbk", $result), $area);
					$checkresult =  iconv("utf-8", "gbk", $phonearea["data"]["province"]).iconv("utf-8", "gbk", $phonearea["data"]["city"]);
				}

				$errormsg = lang('plugin/smstong','smstong_checkmobile_default').$_G['cache']['plugin']['smstong']['areavalue'];
			}
			break;
			default :
			{
				if ($_G['charset'] == "utf-8") {
					$checkresult = $phonearea["data"]["province"].$phonearea["data"]["city"];
				}
				else {
					$checkresult =  $phonearea["data"]["province"].$phonearea["data"]["city"];
				}

				$errormsg = lang('plugin/smstong','smstong_checkmobile_default').$_G['cache']['plugin']['smstong']['areavalue'];
			}
			break;
			
		}
//return $checkresult."1";
		$area_array = explode("|", $_G['cache']['plugin']['smstong']['areavalue']);

		$flag1 = false;
		$flag2 = false;

		if(in_array($checkresult, $area_array))
			$flag1 = true;

		foreach($area_array as $k => $v) {
			if(strstr($checkresult, $v))
			$flag1 = true;
		}

		if($_G['cache']['plugin']['smstong']['ipareacons'] == "1") {
			require_once libfile('function/misc');
			$iparea = trim(trim(convertip($_G['clientip']),'-'));
			
			foreach($area_array as $k => $v) {
				if(strstr($iparea, $v)) {
					$flag2 = true;
					break;
				}
			}
		} else {
			$flag2 = true;
		}

		if($flag1 && $flag2) return true;
	} else {
		return true;
	}

	return $errormsg;
}

function chackmobile2($mobile) {

	global $_G;

	$mobile_array = explode(",", $_G['cache']['plugin']['smstong']['blackmobile']);

	if(in_array($_G['clientip'], $mobile_array))
		return lang('plugin/smstong','smstong_blackip_existed');

	if(in_array($mobile, $mobile_array))
		return lang('plugin/smstong','smstong_blackmobile_existed');

	foreach($mobile_array as $k => $v) {
		if(strpos($mobile, $v)===0)
		return lang('plugin/smstong','smstong_blackmobile_existed');
	}

	if ($_G['cache']['plugin']['smstong']['areacons'] == "0") return true;

	if (empty($_G['cache']['plugin']['smstong']['areavalue'])) return true;

	$checkmobile = false;

	if(!empty($_G['cache']['plugin']['smstong']['areaconstime'])) {
		$now = dgmdate(TIMESTAMP, 'G.i');
		foreach(explode("\r\n", str_replace(':', '.', $_G['cache']['plugin']['smstong']['areaconstime'])) as $period) {
			list($periodbegin, $periodend) = explode('-', $period);
			if(($periodbegin > $periodend && ($now >= $periodbegin || $now < $periodend)) || ($periodbegin < $periodend && $now >= $periodbegin && $now < $periodend)) {
				$checkmobile = true;
			}
		}
	} else {
		$checkmobile = true;
	}

	if($_G['cache']['plugin']['smstong']['areacons'] == "1" && $checkmobile) {

		$checkurl = "http://cx.chanyoo.cn/?username=".$_G['cache']['plugin']['smstong']['smsusername']."&password=".$_G['cache']['plugin']['smstong']['smspassword']."&mobile=".$mobile;

		$result = httprequest($checkurl);

		$checkresult = "";
		$errormsg = lang('plugin/smstong','smstong_checkmobile_error');

		$result = strip_tags($result);
		$result = preg_replace('/\s/', '', $result);
//return iconv("utf-8", "gbk", $result);
		if(empty($result) || $result == lang('plugin/smstong','smstong_checkmobile_fast')) return $errormsg;
//return $result;
		$phonearea = json_decode($result, true);
//return $phonearea["gsd"]."2";
		switch ($_G['cache']['plugin']['smstong']['areatype'])
		{
			case 1 :
			{
				if ($_G['charset'] == "utf-8") {
					$checkresult = $phonearea["gsd"];
				}
				else {
					$checkresult =  iconv("utf-8", "gbk", $phonearea["gsd"]);
				}

				$errormsg = lang('plugin/smstong','smstong_checkmobile_default').$_G['cache']['plugin']['smstong']['areavalue'];
			}
			break;
			case 2 :
			{
				if ($_G['charset'] == "utf-8") {
					$checkresult = $phonearea["gsd"];
				}
				else {
					$checkresult =  iconv("utf-8", "gbk", $phonearea["gsd"]);
				}

				$errormsg = lang('plugin/smstong','smstong_checkmobile_default').$_G['cache']['plugin']['smstong']['areavalue'];
			}
			break;
			default :
			{
				if ($_G['charset'] == "utf-8") {
					$checkresult = $phonearea["gsd"];
				}
				else {
					$checkresult =  $phonearea["gsd"];
				}

				$errormsg = lang('plugin/smstong','smstong_checkmobile_default').$_G['cache']['plugin']['smstong']['areavalue'];
			}
			break;
			
		}
//return $checkresult."2";
		$area_array = explode("|", $_G['cache']['plugin']['smstong']['areavalue']);

		$flag1 = false;
		$flag2 = false;

		if(in_array($checkresult, $area_array))
			$flag1 = true;

		foreach($area_array as $k => $v) {
			if(strstr($checkresult, $v))
			$flag1 = true;
		}

		if($_G['cache']['plugin']['smstong']['ipareacons'] == "1") {
			require_once libfile('function/misc');
			$iparea = trim(trim(convertip($_G['clientip']),'-'));
			
			foreach($area_array as $k => $v) {
				if(strstr($iparea, $v)) {
					$flag2 = true;
					break;
				}
			}
		} else {
			$flag2 = true;
		}

		if($flag1 && $flag2) return true;
	} else {
		return true;
	}

	return $errormsg;
}

function sendCurlPost($url, $dataObj) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $dataObj);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	$ret = curl_exec($curl);
	if (false == $ret) {
		// curl_exec failed
		$result = "{ \"result\":" . -2 . ",\"errmsg\":\"" . curl_error($curl) . "\"}";
	} else {
		$rsp = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if (200 != $rsp) {
			$result = "{ \"result\":" . -1 . ",\"errmsg\":\"". $rsp . " " . curl_error($curl) ."\"}";
		} else {
			$result = $ret;
		}
	}
	curl_close($curl);
	return $result;
}

function httprequest($url, $data=array(), $abort=false) {
	$resp = file_get_contents($url);
	if (!empty($resp) && empty($data)) return $resp;
	if ( !function_exists('curl_init') ) { return empty($data) ? doget($url) : dopost($url, $data); }
	$timeout = $abort ? 1 : 2;
	$ch = curl_init();
	if (is_array($data) && $data) {
		$formdata = http_build_query($data);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $formdata);
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	$result = curl_exec($ch);
	return (false===$result && false==$abort)? ( empty($data) ?  doget($url) : dopost($url, $data) ) : $result;
}

function doget($url){
	$url2 = parse_url($url);
	$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
	if(array_key_exists("port", $url2))
		$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
	else
		$url2["port"] = 80;
	$host_ip = @gethostbyname($url2["host"]);
	$fsock_timeout = 2;  //2 second
	if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}
	if(array_key_exists("query", $url2))
		$request =  $url2["path"] .($url2["query"] ? "?".$url2["query"] : "");
	else
		$request =  $url2["path"];
	$in  = "GET " . $request . " HTTP/1.0\r\n";
	$in .= "Accept: image/jpeg, image/gif, image/pjpeg, application/x-ms-application, application/xaml+xml, application/x-ms-xbap, application/vnd.ms-excel, application/vnd.ms-powerpoint, application/msword, */*\r\n";
	$in .= "User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET4.0C; .NET4.0E; .NET CLR 3.5.30729; .NET CLR 3.0.30729)\r\n";
	$in .= "Host: " . $url2["host"] . "\r\n";
	$in .= "Connection: Close\r\n\r\n";
	if(!@fwrite($fsock, $in, strlen($in))){
		fclose($fsock);
		return false;
	}
	return gethttpcontent($fsock);
}

function dopost($url,$post_data=array()){
	$url2 = parse_url($url);
	$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
	$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
	$host_ip = @gethostbyname($url2["host"]);
	$fsock_timeout = 2; //2 second
	if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}
	$request =  $url2["path"].($url2["query"] ? "?" . $url2["query"] : "");
	$post_data2 = http_build_query($post_data);
	$in  = "POST " . $request . " HTTP/1.0\r\n";
	$in .= "Accept: image/jpeg, image/gif, image/pjpeg, application/x-ms-application, application/xaml+xml, application/x-ms-xbap, application/vnd.ms-excel, application/vnd.ms-powerpoint, application/msword, */*\r\n";
	$in .= "Host: " . $url2["host"] . "\r\n";
	$in .= "User-Agent: Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET4.0C; .NET4.0E; .NET CLR 3.5.30729; .NET CLR 3.0.30729)\r\n";
	$in .= "Content-type: application/x-www-form-urlencoded\r\n";
	$in .= "Content-Length: " . strlen($post_data2) . "\r\n";
	$in .= "Connection: Close\r\n\r\n";
	$in .= $post_data2 . "\r\n\r\n";
	unset($post_data2);
	if(!@fwrite($fsock, $in, strlen($in))){
		fclose($fsock);
		return false;
	}
	return gethttpcontent($fsock);
}

function gethttpcontent($fsock=null) {
	$out = null;
	while($buff = @fgets($fsock, 2048)){
		$out .= $buff;
	}
	fclose($fsock);
	$pos = strpos($out, "\r\n\r\n");
	$head = substr($out, 0, $pos);    //http head
	$status = substr($head, 0, strpos($head, "\r\n"));    //http status line
	$body = substr($out, $pos + 4, strlen($out) - ($pos + 4));//page body
	if(preg_match("/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches)){
		if(intval($matches[1]) / 100 == 2){
			return $body;  
		}else{
			return false;
		}
	}else{
		return false;
	}
}