<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: statistics.inc.php 18582 2011-10-04 11:36:40Z 呀呀个呸 $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('plugin');

$Plang = $scriptlang['smstong'];

echo '<script src="https://img.hcharts.cn/jquery/jquery-1.8.3.min.js" charset="utf-8"></script>';
echo '<script src="https://img.hcharts.cn/highcharts/highcharts.js" charset="utf-8"></script>';
echo '<script src="https://img.hcharts.cn/highcharts/modules/exporting.js" charset="utf-8"></script>';
echo '<script src="https://img.hcharts.cn/highcharts-plugins/highcharts-zh_CN.js" charset="utf-8"></script>';

echo '<div id="container1" style="min-width:400px;height:400px"></div>';

echo '<div id="container2" style="min-width:400px;height:400px"></div>';

require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');

$url = "http://api.chanyoo.cn/utf8/interface/user_stat.aspx?username=".urlencode($_G['cache']['plugin']['smstong']['smsusername'])."&password=".urlencode($_G['cache']['plugin']['smstong']['smspassword'])."";

$ret = httprequest($url);

$xml = simplexml_load_string($ret);

$uid = intval($xml->result);

$daytitle = "[]";
$daytotal = "[]";
$monthtitle = "[]";
$monthtotal = "[]";

if ($uid > 0)
{
	$result = $xml->result;
	$daytitle = $xml->daytitle;
	$daytotal = $xml->daytotal;
	$monthtitle = $xml->monthtitle;
	$monthtotal = $xml->monthtotal;
}

echo "<script>
	$(function () {
    $('#container1').highcharts({
        chart: {
            type: 'line'
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: ".$daytitle."
        },
        yAxis: {
            title: {
                text: '".$Plang['smstong_stat_unit']."'
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: true
            }
        },
        series: [{
            name: '".$Plang['smstong_stat_day']."',
            data: ".$daytotal."
        }]
    });
});
</script>";

echo "<script>
	$(function () {
    $('#container2').highcharts({
        chart: {
            type: 'line'
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: ".$monthtitle."
        },
        yAxis: {
            title: {
                text: '".$Plang['smstong_stat_unit']."'
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: true
            }
        },
        series: [{
            name: '".$Plang['smstong_stat_month']."',
            data: ".$monthtotal."
        }]
    });
});
</script>";

?>