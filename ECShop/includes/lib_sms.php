<?php

include_once 'aliyun/Util/Autoloader.php';

function sendsms($mobile, $content)
{
	if (empty($GLOBALS['_CFG']['ecsdxt_aliyun_appkey']) && empty($GLOBALS['_CFG']['ecsdxt_aliyun_appsecret']) )
		return sendsms_imtong($mobile, $content);
	else
		return sendsms_alimarket($mobile, $content);
}

function sendsms_alimarket($mobile, $content)
{
	$content = $content."【".$GLOBALS['_CFG']['shop_name']."】";

	$appKey = $GLOBALS['_CFG']['ecsdxt_aliyun_appkey'];
	$appSecret = $GLOBALS['_CFG']['ecsdxt_aliyun_appsecret'];
	$host = "https://chanyoo.market.alicloudapi.com";

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
		return $result;
	}
}

function sendsms_imtong($mobile, $content)
{
	//过滤黑字典
	$content = str_replace('1989','1 9 8 9',$content);
	$content = str_replace('1259','1 2 5 9',$content);
	$content = str_replace('12590','1 2 5 9 0',$content);
	$content = str_replace('10086','1 0 0 8 6',$content);

	//配置信息
	$smsapi = "api.chanyoo.cn";					//短信网关
	$charset = "utf8";							//文件编码

	$content = $content."【".$GLOBALS['_CFG']['shop_name']."】";

	$sendurl = "http://".$smsapi."/".$charset."/interface/send_sms.aspx?username=".$GLOBALS['_CFG']['ecsdxt_user_name']."&password=".$GLOBALS['_CFG']['ecsdxt_pass_word']."&receiver=".$mobile."&content=".urlencode($content)."";

	$result = file_get_contents($sendurl);

	$xml = simplexml_load_string($result);

	if ($xml->result >= 0)
	{
		return true;
	}
	else
	{
		return $xml->message; 
	}
}

function ismobile($mobile)
{
	return (strlen($mobile) == 11) && (preg_match("/^13\d{9}$/", $mobile) || preg_match("/^14\d{9}$/", $mobile) || preg_match("/^15\d{9}$/", $mobile) || preg_match("/^16\d{9}$/", $mobile) || preg_match("/^17\d{9}$/", $mobile) || preg_match("/^18\d{9}$/", $mobile) || preg_match("/^19\d{9}$/", $mobile));
}

function getverifycode()
{
	$verifycode = rand(100000,999999);

	$verifycode = str_replace('1989','9819',$verifycode);
	$verifycode = str_replace('1259','9521',$verifycode);
	$verifycode = str_replace('12590','09521',$verifycode);
	$verifycode = str_replace('10086','68001',$verifycode);

	return $verifycode;
}

function httprequest($url, $data=array(), $abort=false) {
	if ( !function_exists('curl_init') ) { return empty($data) ? doget($url) : dopost($url, $data); }
	$timeout = $abort ? 1 : 2;
	$ch = curl_init();
	if (is_array($data) && $data) {
		$formdata = http_build_query($data);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $formdata);
	}
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
	$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
	$host_ip = @gethostbyname($url2["host"]);
	$fsock_timeout = 2;  //2 second
	if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}
	$request =  $url2["path"] .($url2["query"] ? "?".$url2["query"] : "");
	$in  = "GET " . $request . " HTTP/1.0\r\n";
	$in .= "Accept: */*\r\n";
	$in .= "User-Agent: Payb-Agent\r\n";
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
	$in .= "Accept: */*\r\n";
	$in .= "Host: " . $url2["host"] . "\r\n";
	$in .= "User-Agent: Lowell-Agent\r\n";
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

?>