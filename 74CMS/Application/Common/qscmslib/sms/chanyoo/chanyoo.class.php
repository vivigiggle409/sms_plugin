<?php
// +----------------------------------------------------------------------
// | 74CMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.74cms.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------
// | ModelName: 74cms短信类
// +----------------------------------------------------------------------

include_once 'aliyun/Util/Autoloader.php';

class chanyoo_sms{
	protected $_error = 0;
	protected $setting = array();
	protected $_base_url = 'http://api.chanyoo.cn/utf8/sendsms.aspx?';//基础类短信请求地址
	protected $_notice_url = 'http://api.chanyoo.cn/utf8/sendsms.aspx?';//通知类短信请求地地址
	protected $_captcha_url = 'http://api.chanyoo.cn/utf8/sendsms.aspx?';//验证码类短信请求地址
	protected $_other_url = 'http://api.chanyoo.cn/utf8/sendsms.aspx?';//其它类短信请求地址

	public function __construct($setting) {
		$this->setting = $setting;
	}
	/**
	 * 发送模板短信
	 * @param    string     $type 短信通道 手机号码集合,用英文逗号分开
	 * @param    array      $option['mobile':手机号码集合,用英文逗号分开,'content':短信内容]
	 * @return   boolean
	 */
	public function sendTemplateSMS($type='captcha',$option){
		if(strlen($this->setting['appkey']) >= 8 && strlen($this->setting['secretKey']) == 32){
			$this->alimarket_sendsms($type='alimarket',$option);
		}else{
			//var_dump(strlen($this->setting['secretKey']));
			$this->QS_sendTemplateSMS($type='captcha',$option);
		}
	}

	protected function alimarket_sendsms($type='alimarket', $option)
	{
		//var_dump($option);
		//解析模板内容
		if($option['data']){
            foreach ($option['data'] as $key => $val) {
                $data['{'.$key.'}'] = $val;
            }
            $data['content'] = strtr($option['tpl'],$data);
        }else{
            $data['content'] = $option['tpl'];
        }

		$data['content'] = str_replace('13012345678', $option['mobile'] ,$data['content']);
		$data['content'] = str_replace('110426', rand(100000,999999) ,$data['content']);

		$sms['content'] = $data['content'].'【'.$this->setting['signature'].'】';

		//$sms['content'] = urlencode($sms['content']);
		//var_dump($sms);

		//遍历发送
        $mobile = explode(',',$option['mobile']);
        foreach ($mobile as $key => $val) {
        	if(false === $this->_alimarket_https_request($sms['content'], $val)) return false;
        }
	}

	protected function _alimarket_https_request($content, $mobile)
	{
		if(function_exists('curl_init')){

			$appKey = $this->setting['appkey'];
			$appSecret = $this->setting['secretKey'];
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
				if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){
					header('Content-Type:application/json; charset=utf-8');
					exit(json_encode(array('status'=>0,'msg'=>$result,'data'=>'','dialog'=>'')));
				}
				else
					exit($result);
				$this->_error = $result;
				return false;
			}
			
		}else{
			$this->_error = '短信发送失败，请开启curl服务！';
			return false;
		}
	}

	protected function QS_sendTemplateSMS($type='captcha',$option){
		$data['username'] = $this->setting['appkey'];
		$data['password'] = $this->setting['secretKey'];
		//var_dump($option);
		//解析模板内容
		if($option['data']){
            foreach ($option['data'] as $key => $val) {
                $data['{'.$key.'}'] = $val;
            }
            $data['content'] = strtr($option['tpl'],$data);
        }else{
            $data['content'] = $option['tpl'];
        }
		$data['content'] = $data['content'].'【'.$this->setting['signature'].'】';
        //转换编码
        //foreach ($data as $key => $val) {
		//	$data[$key] = iconv('UTF-8','GB2312//IGNORE',$val);
		//}
		$name = '_'.$type.'_url';
		$url = $this->$name.http_build_query($data);
		//var_dump($url);
		//遍历发送
        $mobile = explode(',',$option['mobile']);
        foreach ($mobile as $key => $val) {
        	if(false === $this->_https_request($url.'&receiver='.$val)) return false;
        }
		return true;
	}

	protected function _https_request($url,$data = null){
		if(function_exists('curl_init')){
			$curl = curl_init();
		    curl_setopt($curl, CURLOPT_URL, $url);
		    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
			curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
		    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		    curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
		    if (!empty($data)){
		        curl_setopt($curl, CURLOPT_POST, 1);
		        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		    }
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		    $output = curl_exec($curl);
		    curl_close($curl);
		    return $output;
		}else{
			$this->_error = '短信发送失败，请开启curl服务！';
			return false;
		}
	}
	public function getError(){
		return $this->_error;
	}
}
?>