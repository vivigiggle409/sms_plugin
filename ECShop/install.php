<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$newsql = sreadfile("install.sql");

if($prefix != 'ecs_') 
	$newsql = str_replace('ecs_', $prefix, $newsql);//替换表名前缀

$sqls = explode(";", $newsql);
foreach ($sqls as $sql) {
	$sql = trim($sql);
	if (empty($sql)) {
		continue;
	}
	if(!$query = $db->query($sql)) {
		echo "执行sql语句出错： ".mysql_error();
		exit();
	}
}

echo "<h4>ECShop短信通插件安装成功，请删除此文件！</h4>";

function sreadfile($filename) {
	$content = '';
	if(function_exists('file_get_contents')) {
		@$content = file_get_contents($filename);
	} else {
		if(@$fp = fopen($filename, 'r')) {
			@$content = fread($fp, filesize($filename));
			@fclose($fp);
		}
	}
	return $content;
}

?>