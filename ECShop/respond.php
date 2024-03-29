<?php

/**
 * ECSHOP 支付响应页面
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: respond.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_payment.php');
require(ROOT_PATH . 'includes/lib_order.php');
/* 支付方式代码 */
$pay_code = !empty($_REQUEST['code']) ? trim($_REQUEST['code']) : '';

//获取首信支付方式
if (empty($pay_code) && !empty($_REQUEST['v_pmode']) && !empty($_REQUEST['v_pstring']))
{
    $pay_code = 'cappay';
}

//获取快钱神州行支付方式
if (empty($pay_code) && ($_REQUEST['ext1'] == 'shenzhou') && ($_REQUEST['ext2'] == 'ecshop'))
{
    $pay_code = 'shenzhou';
}

/* 参数是否为空 */
if (empty($pay_code))
{
    $msg = $_LANG['pay_not_exist'];
}
else
{
    /* 检查code里面有没有问号 */
    if (strpos($pay_code, '?') !== false)
    {
        $arr1 = explode('?', $pay_code);
        $arr2 = explode('=', $arr1[1]);

        $_REQUEST['code']   = $arr1[0];
        $_REQUEST[$arr2[0]] = $arr2[1];
        $_GET['code']       = $arr1[0];
        $_GET[$arr2[0]]     = $arr2[1];
        $pay_code           = $arr1[0];
    }

    /* 判断是否启用 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('payment') . " WHERE pay_code = '$pay_code' AND enabled = 1";
    if ($db->getOne($sql) == 0)
    {
        $msg = $_LANG['pay_disabled'];
    }
    else
    {
        $plugin_file = 'includes/modules/payment/' . $pay_code . '.php';

        /* 检查插件文件是否存在，如果存在则验证支付是否成功，否则则返回失败信息 */
        if (file_exists($plugin_file))
        {
            /* 根据支付方式代码创建支付类的对象并调用其响应操作方法 */
            include_once($plugin_file);

            $payment = new $pay_code();
            $msg     = ($payment->respond()) ? $_LANG['pay_success'] : $_LANG['pay_fail'];

			if ($payment->respond() && empty($_SESSION['sent'])) {
				/* 获取order_id */
				$sql = "SELECT order_id FROM " . $ecs->table('order_info') . " WHERE order_sn='$_REQUEST[subject]' LIMIT 1";
				$row = $db->getRow($sql);//echo $row[order_id];exit();

				$sql = 'SELECT order_id, user_id, order_sn, consignee, address, tel, shipping_id, extension_code, extension_id, goods_amount ' .
                        'FROM ' . $GLOBALS['ecs']->table('order_info') .
                       " WHERE order_id = ' $row[order_id]'";
                $order    = $db->getRow($sql);//echo $order[order_sn];exit();
				
				/* 客户付款时给商家发送短信提醒 */
				if ($_CFG['ecsdxt_order_payed'] == '1' && $_CFG['ecsdxt_shop_mobile'] != '')
				{
					require_once(ROOT_PATH . 'includes/lib_sms.php');

					$smarty->assign('shop_name',	$_CFG['shop_name']);
					$smarty->assign('order_sn', $order['order_sn']);
					$smarty->assign('consignee', $order['consignee']);
					$smarty->assign('tel', $order['tel']);


					$content = $smarty->fetch('str:' . $_CFG['ecsdxt_order_payed_value']);

					$ret = sendsms($_CFG['ecsdxt_shop_mobile'], $content);
				}

				/* 获取用户手机号 */
				$sql = "SELECT user_id, mobile_phone FROM " . $ecs->table('users') . " WHERE user_id='$order[user_id]' LIMIT 1";
				$row = $db->getRow($sql);//echo $row['mobile_phone'];exit();

				/* 客户付款时给客户发送短信提醒 */
				if ($_CFG['ecsdxt_customer_payed'] == '1' && $row['mobile_phone'] != '')
				{
					require_once(ROOT_PATH . 'includes/lib_sms.php');

					$smarty->assign('shop_name',	$_CFG['shop_name']);
					$smarty->assign('order_sn',		$order['order_sn']);
					$smarty->assign('time',			date('Y-m-d H:i:s', time()+28800));

					$content = $smarty->fetch('str:' . $_CFG['ecsdxt_customer_payed_value']);

					$ret = sendsms($row['mobile_phone'], $content);
				}

				$_SESSION['sent'] = '1';
			}
        }
        else
        {
            $msg = $_LANG['pay_not_exist'];
        }
    }
}

assign_template();
$position = assign_ur_here();
$smarty->assign('page_title', $position['title']);   // 页面标题
$smarty->assign('ur_here',    $position['ur_here']); // 当前位置
$smarty->assign('page_title', $position['title']);   // 页面标题
$smarty->assign('ur_here',    $position['ur_here']); // 当前位置
$smarty->assign('helps',      get_shop_help());      // 网店帮助

$smarty->assign('message',    $msg);
$smarty->assign('shop_url',   $ecs->url());

$smarty->display('respond.dwt');

?>