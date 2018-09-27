<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: acountinfo.inc.php 18582 2010-07-17 10:38:36Z я╫я╫╦Жеч $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('plugin');

$Plang = $scriptlang['smstong'];

if (!$_G['cache']['plugin']['smstong']) {
	cpmsg($Plang['smstong_plugin_closed'], "action=plugins", 'error');
}

$smstong_regurl = 'http://s1.chanyoo.cn/registers.aspx?agree=true';
$smstong_regurl .= '&siteurl='.urlencode($_G['siteurl']);

if ($_G['charset'] == "gbk")
	$smstong_regurl .= '&sitename='.urlencode(iconv('GBK', 'UTF-8//IGNORE', $_G['setting']['sitename']));
else
	$smstong_regurl .= '&sitename='.urlencode($_G['setting']['sitename']);

$smstong_regurl .= '&adminemail='.urlencode($_G['setting']['adminemail']);
$smstong_regurl .= '&site_qq='.urlencode($_G['setting']['site_qq']);

if (($_G['cache']['plugin']['smstong']['smsusername'] == 'demo') && ($_G['cache']['plugin']['smstong']['smspassword'] == 'demo')) {
	cpmsg('&#x6CA1;&#x6709;&#x8BBE;&#x7F6E;&#x5E73;&#x53F0;&#x5E10;&#x53F7;&#x548C;&#x5BC6;&#x7801;&#xFF0C;&#x70B9;&#x51FB;&#x4E0B;&#x9762;&#x94FE;&#x63A5;&#x8DF3;&#x8F6C;&#x5230;&#x6CE8;&#x518C;&#x9875;&#x9762;&#xFF01;', $smstong_regurl, 'error');
}

if (empty($_G['cache']['plugin']['smstong']['smsusername']) || empty($_G['cache']['plugin']['smstong']['smspassword'])) {
	cpmsg($Plang['smstong_username_password_empty'], "action=plugins&operation=config&do=$_GET[do]", 'error');
}

echo '<iframe id="frame_content" src="source/plugin/smstong/accountinfo.php?username='.urlencode($_G['cache']['plugin']['smstong']['smsusername']).'&password='.urlencode($_G['cache']['plugin']['smstong']['smspassword']).'&sendtype='.$_G['cache']['plugin']['smstong']['sendtype'].'" scrolling="no" frameborder="0" onload="this.height=this.contentWindow.document.documentElement.scrollHeight" style="position:absolute; left:0px; top:50px; width:100%; border:0px;"></iframe>';

?>