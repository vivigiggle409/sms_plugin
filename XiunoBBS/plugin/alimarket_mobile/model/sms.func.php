<?php

include_once 'aliyun/Util/Autoloader.php';


// 发送验证码接口
function sms_send_code($tomobile, $code) {
	// 根据类型调用不同的短信发送 SDK
	$kv = kv_get('mobile_setting');
	$r = FALSE;

	$content = str_replace('{$mobile}', $tomobile, $kv['alimarket_api_template']);
	$content = str_replace('{$code}', $code, $content);

	$appKey = $kv['alimarket_api_appkey'];
	$appSecret = $kv['alimarket_api_appsecret'];
	$host = "https://chanyoo.market.alicloudapi.com";

	//域名后、query前的部分
	$path = "/sendsms";
	$request = new HttpRequest($host, $path, HttpMethod::GET, $appKey, $appSecret);

	//设定Content-Type，根据服务器端接受的值来设置
	$request->setHeader(HttpHeader::HTTP_HEADER_CONTENT_TYPE, ContentType::CONTENT_TYPE_TEXT);
	
	//设定Accept，根据服务器端接受的值来设置
	$request->setHeader(HttpHeader::HTTP_HEADER_ACCEPT, ContentType::CONTENT_TYPE_TEXT);

	//注意：业务query部分，如果没有则无此行；请不要、不要、不要做UrlEncode处理
	$request->setQuery("mobile", $tomobile);
	$request->setQuery("content", $content);

	$response = HttpClient::execute($request);

	if($response->getSuccess()){
		message(0, '短信发送成功！');
	}else{
		$result = 'API Error '. $response->getHttpStatusCode() . ' '. ($response->getErrorMessage()==""?json_decode($response->getBody())->errmsg:$response->getErrorMessage());
		message(-1, '短信发送失败：'.$result);
	}

	return $r;
}

/*
function sms_sms_send_code($tomobile, $code, $d, $s)
{
	return false;
}
*/
// sms_send('15600900902', "您的初始密码为：123456");

/*

Array
(
    [result] => 0
    [errmsg] => OK
    [ext] => 
    [sid] => 8:xxxxxxxxxxxxxxxxxxxxxxx
    [fee] => 1
)

*/

?>