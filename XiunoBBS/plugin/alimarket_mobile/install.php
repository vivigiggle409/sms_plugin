<?php

!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;
$sql = "ALTER TABLE ADD INDEX {$tablepre}user mobile(mobile)";

$r = db_exec($sql);

$kv = kv_get('mobile_setting');
if(!$kv) {
	
	$kv = array();
	$kv['login_type'] = 1;
	$kv['find_pw_on'] = 0;
	$kv['create_user_on'] = 0;
	$kv['bind_on'] = 0;
	$kv['force_bind_on'] = 0;
	$kv['send_plat'] = 0;
	/*
	$kv['tencent_appid'] = '';
	$kv['tencent_appkey'] = '';
	$kv['aliyun_appid'] = '';
	$kv['aliyun_appkey'] = '';

	$kv['yunsms_appid'] = '';
	$kv['yunsms_appkey'] = '';
	*/

	$kv['alimarket_api_url'] = 'https://aliyun.chanyoo.net/sendsms';
	$kv['alimarket_api_method'] = 'GET';
	$kv['alimarket_api_query'] = 'mobile={$mobile}&content={$content}';
	$kv['alimarket_api_body'] = '';
	$kv['alimarket_api_appcode'] = '';
	$kv['alimarket_api_template'] = '您的手机号：{$mobile}，验证码：{$code}，请及时完成验证，如不是本人操作请忽略。【阿里云市场】';
	
	kv_set('mobile_setting', $kv);
}

?>