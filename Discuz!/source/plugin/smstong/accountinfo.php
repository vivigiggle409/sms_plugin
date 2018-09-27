<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: acountinfo.php 18582 2010-07-17 11:12:11Z 呀呀个呸 $
 */

define('IN_DISCUZ', TRUE);
header("Content-type: text/html; charset=utf-8");

$smsapi = "api.chanyoo.cn";
$charset = "utf8";
$username = $_GET['username'];
$password = $_GET['password'];
$sendtype = $_GET['sendtype'];

if ($sendtype == "2") {
	$smsapi = "api.chanyoo.cn";
} elseif ($sendtype == "3") {
	$smsapi = "a1.chanyoo.cn";
} elseif ($sendtype == "4") {
	$smsapi = "a2.chanyoo.cn";
}

$url = "http://".$smsapi."/".$charset."/interface/user_info.aspx?username=".urlencode($username)."&password=".urlencode($password)."";

require_once('smstong.func.php');

$ret = httprequest($url);

$xml = simplexml_load_string($ret);

$uid = intval($xml->result);

if ($uid > 0)
{
	$result = $xml->result;
	$user_balance = $xml->user_balance;
	$user_amount = $xml->user_amount;
	$sms_left = $xml->sms_left;
	$sms_send = $xml->sms_send;
	$sms_receive = $xml->sms_receive;
	$expired_date = $xml->expired_date;
	$user_point = $xml->user_point;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta name="Copyright" content="" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<title>帐号信息</title>
<style>
html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, font, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td {margin: 0;padding: 0;border: 0;outline: 0;font-size: 100%;background: transparent;}
ol, ul {list-style: none;}
body{ background:#FFF; margin:0 20px;}
body,table,td{ font-size:12px; color:#666;}
table{ border-collapse:collapse; border-spacing: 0;}
td{ border-top:1px dotted #DEEFFB; padding:5px; font-size:14px}
.title{color:#0099CC; font-weight:700; height:25px; text-align:left; padding:5px; background:#e5f1fb}
a{ color:#f8505c; text-decoration:none;}
.btn{ padding:5px; display:block; width:200px; background:#e5f1fb; text-align:center; height:20px; line-height:20px; text-decoration:none; color:#666; border: 1px solid #c7e1f6; margin-left:80px; font-size:14px; cursor:pointer;}
</style>
</head>
<body>

<?php
if($uid > 0){
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th colspan="2" class="title">帐号信息</th>
  </tr>
  <tr>
    <td width="13%" align="right"><strong>当前帐号：</strong></td>
    <td width="87%"><?php echo $_GET['username'] ?></td>
  </tr>
  <tr>
    <td align="right"><strong>剩余条数：</strong></td>
    <td><?php echo $sms_left ?> 条</td>
  </tr>
  <tr>
    <td width="13%" align="right"><strong>已发条数：</strong></td>
    <td width="87%"><?php echo $sms_send ?> 条</td>
  </tr>
  <tr>
    <td width="13%" align="right"><strong>帐号积分：</strong></td>
    <td width="87%"><?php echo $user_point ?> <span class="result"><a href="http://bbs.chanyoo.cn/plugin.php?id=auction" target="_blank" title="开源软件增值服务平台在线充值消费1元对应1积分，例如某帐号在线充值10元对应积10分，在线充值12.30元对应积12分，在线充值2.80元对应积12分，不足1元的不计积分，累积在线充值积分可以到积分商城兑换奖品，银行转账，淘宝交易，直接支付宝付款是没有积分的，同时不能跟其它优惠活动叠加，欢迎大家踊跃在线充值，积分兑换奖品！">点击此处积分兑换奖品</a></span></td>
  </tr>
  <tr>
    <td align="right"><strong>联系方式：</strong></td>
    <td>如有任何问题请联系QQ：320266361，320266362，320266363，320266364，320266365 工作时间：周一至周五9:00-11:30 13:30-17:30，周六日节假日休息。
	</td>
  </tr>

   <tr>
    <td align="right"><strong>奖励短信：</strong></td>
    <td>安装插件注册平台帐号，应用中心本应用介绍页面右上角分享到QQ，腾讯微博，QQ空间，朋友网，新浪微博各得10条短信，评五星赠送10条一共50条短信，安装收藏插件开启评五星并分享在应用中心评论中回复分享后的四个地址和论坛域名然后联系我们核实发放短信。
	</td>
  </tr>

  <tr>
    <td align="right"><strong>短信模板：</strong></td>
    <td>新注册用户请登录开源软件增值服务平台到帐号管理-短信模板中添加对应内容的短信模板然后才能正常的下发短信，插件默认签名是带网站名称的，如果你注册的帐号信息里面的网站名称跟您论坛提交的短信内容结尾的签名是一致的那么不用添加新的模板直接使用公共模板即可，如果提交的短信内容结尾签名跟您帐号注册信息里面的网站名称不一致请到插件设置短信内容签名里面设置跟注册信息里面的网站名称一致的签名内容，否则网站提交的短信内容无法下发。
	</td>
  </tr>

  <tr>
    <td align="right"><strong>免责声明：</strong></td>
    <td>凡使用本插件发送的各种诈骗，辱骂，恐吓，骚扰，等违法短信均与本插件开发运营方无关，插件最终使用者须对发送的短信内容承担全部法律责任。如果发现违反以上规定的，冻结开源软件增值服务平台帐号清零处理，情节严重的短信记录存档并报送相关执法部门依法处理。
	</td>
  </tr>

  <tr>
    <td align="right"><strong><br />购买短信：</strong></td>
    <td><span class="result"><br /><a href="http://www.chanyoo.cn/mod_static-view-sc_id-1111115.html" target="_blank" title="通过在线充值的方式购买短信条数：在新打开的页面中选择您要充值的短信条数，根据提示完成在线支付操作，之后刷新本帐号信息页面剩余条数就会加上您充值购买的短信条数，目前支持支付宝，财付通，以及各个常用的网银在线充值。">点击此处在线购买短信</a></span></td>
  </tr>
  
</table>

<?php } else { ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th colspan="2" class="title">帐号信息</th>
  </tr>
  <tr>
    <td width="13%" align="right"><strong>当前帐号：</strong></td>
    <td width="87%"><?php echo $_GET['username'] ?></td>
  </tr>
  <tr>
    <td width="13%" align="right"><strong>返回信息：</strong></td>
    <td width="87%"><?php echo $xml->message ?></td>
  </tr>
</table>

<?php } ?>

</body>
</html>
