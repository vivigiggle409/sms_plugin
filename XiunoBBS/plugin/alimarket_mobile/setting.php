<?php
!defined('DEBUG') AND exit('Access Denied.');

if($method == 'GET') {
	
	$kv = kv_get('mobile_setting');
	
	$input = array();
	$input['user_resetpw_on'] = form_radio_yes_no('user_resetpw_on', $kv['user_resetpw_on']);
	$input['user_create_on'] = form_radio_yes_no('user_create_on', $kv['user_create_on']);
	$input['bind_on'] = form_radio_yes_no('bind_on', $kv['bind_on']);
	$input['force_post_bind_on'] = form_radio_yes_no('force_post_bind_on', $kv['force_post_bind_on']);
	$input['force_view_bind_on'] = form_radio_yes_no('force_view_bind_on', $kv['force_view_bind_on']);
	//$input['send_plat'] = form_select('send_plat', array('yunsms'=>'云短信平台'), $kv['send_plat']);
	/*				
	$input['tencent_appid'] = form_text('tencent_appid', $kv['tencent_appid']);
	$input['tencent_appkey'] = form_text('tencent_appkey', $kv['tencent_appkey']);
	$input['tencent_sign'] = form_text('tencent_sign', $kv['tencent_sign']);

	$input['yunsms_appid'] = form_text('yunsms_appid', $kv['yunsms_appid']);
	$input['yunsms_appkey'] = form_text('yunsms_appkey', $kv['yunsms_appkey']);
	$input['yunsms_sign'] = form_text('yunsms_sign', $kv['yunsms_sign'], FALSE, '您的验证码：{**}，该验证码5分钟内有效。【平台签名】');
	$input['yunsms_templateid'] = form_text('yunsms_templateid', $kv['yunsms_templateid'], FALSE, 'SMS_1234567');
	*/

	$input['alimarket_api_appkey'] = form_text('alimarket_api_appkey', $kv['alimarket_api_appkey'], FALSE, '');
	$input['alimarket_api_appsecret'] = form_text('alimarket_api_appsecret', $kv['alimarket_api_appsecret'], FALSE, '');
	$input['alimarket_api_template'] = form_text('alimarket_api_template', $kv['alimarket_api_template'], FALSE, '您的手机号：{$mobile}，验证码：{$code}，请及时完成验证，如不是本人操作请忽略。【阿里云市场】');
	
	include _include(APP_PATH.'plugin/alimarket_mobile/setting.htm');
	
}

else {
	$login_type = param('login_type', 0);
	$user_resetpw_on = param('user_resetpw_on', 0);
	$user_create_on = param('user_create_on', 0);
	$bind_on = param('bind_on', 0);
	$force_post_bind_on = param('force_post_bind_on', 0);
	$force_view_bind_on = param('force_view_bind_on', 0);
	$send_plat = param('send_plat');
	/*
	$tencent_appid = param('tencent_appid');
	$tencent_appkey = param('tencent_appkey');
	$tencent_sign = param('tencent_sign');
	
	$yunsms_appid = param('yunsms_appid');
	$yunsms_appkey = param('yunsms_appkey');
	$yunsms_sign = param('yunsms_sign');
	$yunsms_templateid = param('yunsms_templateid');
	*/

	$alimarket_api_appkey = param('alimarket_api_appkey');
	$alimarket_api_appsecret = param('alimarket_api_appsecret');
	$alimarket_api_template = param('alimarket_api_template');
	
	$kv = array();
	$kv['login_type'] = $login_type;
	$kv['user_resetpw_on'] = $user_resetpw_on;
	$kv['user_create_on'] = $user_create_on;
	$kv['bind_on'] = $bind_on;
	$kv['force_post_bind_on'] = $force_post_bind_on;
	$kv['force_view_bind_on'] = $force_view_bind_on;
	$kv['send_plat'] = $send_plat;
	/*
	$kv['tencent_appid'] = $tencent_appid;
	$kv['tencent_appkey'] = $tencent_appkey;
	$kv['tencent_sign'] = $tencent_sign;
	
	$kv['yunsms_appid'] = $yunsms_appid;
	$kv['yunsms_appkey'] = $yunsms_appkey;
	$kv['yunsms_sign'] = $yunsms_sign;
	$kv['yunsms_templateid'] = $yunsms_templateid;
	*/

	$kv['alimarket_api_appkey'] = $alimarket_api_appkey;
	$kv['alimarket_api_appsecret'] = $alimarket_api_appsecret;
	$kv['alimarket_api_template'] = $alimarket_api_template;
	
	kv_set('mobile_setting', $kv);
	
	message(0, '修改成功');
}
	
?>