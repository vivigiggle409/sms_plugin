<?php

function mpost($curlPost, $url)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
	$return_str = curl_exec($curl);
	curl_close($curl);
	return $return_str;
}

function msend_sms($sms_user, $sms_pwd, $mobile, $content)
{
	global $charset;

	$gets = "";

	if(strlen($sms_user) >= 8 && strlen($sms_pwd) == 32){
		include_once 'aliyun/Util/Autoloader.php';

		$appKey = $sms_user;
		$appSecret = $sms_pwd;
		$host = "https://chanyoo.market.alicloudapi.com";

		$content = $charset == 'gbk' ? iconv('GBK', 'UTF-8//IGNORE', $content) : $content;

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
			$gets = 'success';
		}else{
			$errmsg = $response->getErrorMessage()==""?json_decode($response->getBody())->errmsg:$response->getErrorMessage();
			$errmsg = $charset == 'gbk' ? iconv('UTF-8', 'GBK//IGNORE', $errmsg) : $errmsg;
			$gets = $response->getHttpStatusCode().' '.$errmsg;
		}
	}else{
		$target = 'http://api.chanyoo.cn/' . ($charset == 'gbk' ? 'gbk' : 'utf8') . '/sendsms.aspx';
		$post_data = 'username=' . $sms_user . '&password=' . $sms_pwd . '&receiver=' . $mobile . '&content=' . rawurlencode($content);
		$gets = mpost($post_data, $target);
	}

	return $gets;
}

function msend_regsms($sms_user, $sms_pwd, $mobile, $yzm, $sms_regtpl = '')
{
	$content = str_replace('{code}', $yzm, $sms_regtpl);
	$content = str_replace('{mobile}', $mobile, $content);
	$content = ($content ? $content : '您的手机号：' . $mobile . '，验证码：' . $yzm . '，请及时完成验证，如不是本人操作请忽略。【Mymps】');
	$message = msend_sms($sms_user, $sms_pwd, $mobile, $content);
	write_sms_sendrecord($mobile, $content, $message, 'chanyoo');
}

function msend_pwdsms($sms_user, $sms_pwd, $mobile, $yzm, $sms_pwdtpl = '')
{
	$content = str_replace('{code}', $yzm, $sms_pwdtpl);
	$content = str_replace('{mobile}', $mobile, $content);
	$content = ($content ? $content : '您的手机号：' . $mobile . '，验证码：' . $yzm . '，请及时完成验证，如不是本人操作请忽略。【Mymps】');
	$message = msend_sms($sms_user, $sms_pwd, $mobile, $content);
	write_sms_sendrecord($mobile, $content, $message, 'chanyoo');
}


?>
