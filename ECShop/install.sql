DROP TABLE IF EXISTS `ecs_verifycode`;

CREATE TABLE `ecs_verifycode` (
`id` mediumint(8) unsigned NOT NULL auto_increment,
`mobile` char(12) NOT NULL,
`getip` char(15) NOT NULL,
`verifycode` char(6) NOT NULL,
`dateline` int(10) unsigned NOT NULL default '0',
`reguid` mediumint(8) unsigned default '0',
`regdateline` int(10) unsigned default '0',
`status` tinyint(1) NOT NULL default '1',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

INSERT INTO `ecs_shop_config` VALUES ('90', '0', 'ecsdxt', 'group', '', '', '', '1');

INSERT INTO `ecs_shop_config` VALUES ('9001', '90', 'ecsdxt_gateway', 'options', '1,2', '', '2', '1');

INSERT INTO `ecs_shop_config` VALUES ('9002', '90', 'ecsdxt_user_name', 'text', '', '', '', '1');
INSERT INTO `ecs_shop_config` VALUES ('9003', '90', 'ecsdxt_pass_word', 'password', '', '', '', '1');
INSERT INTO `ecs_shop_config` VALUES ('9004', '90', 'ecsdxt_shop_mobile', 'text', '', '', '', '1');
INSERT INTO `ecs_shop_config` VALUES ('9005', '90', 'ecsdxt_smsgap', 'text', '', '', '3600', '1');

INSERT INTO `ecs_shop_config` VALUES ('9006', '90', 'ecsdxt_mobile_reg', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9007', '90', 'ecsdxt_mobile_reg_value', 'textarea', '', '', '您的手机号：{$user_mobile}，注册验证码：{$verify_code}，一天内提交有效。感谢您的注册！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9008', '90', 'ecsdxt_mobile_log', 'select', '1,0', '', '0', '1');

INSERT INTO `ecs_shop_config` VALUES ('9009', '90', 'ecsdxt_mobile_pwd', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9010', '90', 'ecsdxt_mobile_pwd_value', 'textarea', '', '', '您的用户名：{$user_name}，新密码：{$new_password}。请及时登陆修改密码！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9011', '90', 'ecsdxt_mobile_changepwd', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9012', '90', 'ecsdxt_mobile_changepwd_value', 'textarea', '', '', '您的用户名：{$user_name}，密码已修改，新密码：{$new_password}。请牢记新密码！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9013', '90', 'ecsdxt_mobile_bind', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9014', '90', 'ecsdxt_mobile_bind_value', 'textarea', '', '', '您的手机号：{$user_mobile}，绑定验证码：{$verify_code}。一天内提交有效！', '1');
INSERT INTO `ecs_shop_config` VALUES ('9015', '90', 'ecsdxt_mobile_cons', 'select', '1,0', '', '0', '1');

INSERT INTO `ecs_shop_config` VALUES ('9016', '90', 'ecsdxt_customer_registed', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9017', '90', 'ecsdxt_customer_registed_value', 'textarea', '', '', '您注册的用户名：{$user_name}，密码：{$user_pwd}。感谢您的注册！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9018', '90', 'ecsdxt_order_placed', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9019', '90', 'ecsdxt_order_placed_value', 'textarea', '', '', '您有新的订单：{$order_sn}，收货人：{$consignee}，电 话：{$tel}，请及时确认订单！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9020', '90', 'ecsdxt_order_canceled', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9021', '90', 'ecsdxt_order_canceled_value', 'textarea', '', '', '订单号 ：{$order_sn} 买家已取消订单！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9022', '90', 'ecsdxt_order_payed', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9023', '90', 'ecsdxt_order_payed_value', 'textarea', '', '', '订单号 ：{$order_sn} 买家付 款了。收货人：{$consignee}，电 话：{$tel}。请及时安排发货！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9024', '90', 'ecsdxt_order_confirm', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9025', '90', 'ecsdxt_order_confirm_value', 'textarea', '', '', '订单号 ：{$order_sn} 买家已确认收货！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9026', '90', 'ecsdxt_customer_placed', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9027', '90', 'ecsdxt_customer_placed_value', 'textarea', '', '', '您的订单：{$order_sn}，收货人：{$consignee} 电 话：{$tel}，已经成功提交。感谢您的购买！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9028', '90', 'ecsdxt_customer_canceled', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9029', '90', 'ecsdxt_customer_canceled_value', 'textarea', '', '', '您的订单：{$order_sn}，已取消！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9030', '90', 'ecsdxt_customer_payed', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9031', '90', 'ecsdxt_customer_payed_value', 'textarea', '', '', '您的订单：{$order_sn}，已于{$time}付 款成功。感谢您的购买！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9032', '90', 'ecsdxt_customer_confirm', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9033', '90', 'ecsdxt_customer_confirm_value', 'textarea', '', '', '您的订单：{$order_sn}，确认收货成功。感谢您购买与支持，欢迎您下次光临！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9034', '90', 'ecsdxt_order_picking', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9035', '90', 'ecsdxt_order_picking_value', 'textarea', '', '', '订单号：{$order_sn} 已于{$time}配货。如有问题请及时联系！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9036', '90', 'ecsdxt_order_shipped', 'select', '1,0', '', '0', '1');
INSERT INTO `ecs_shop_config` VALUES ('9037', '90', 'ecsdxt_order_shipped_value', 'textarea', '', '', '订单号：{$order_sn} 已于{$time}发货，如有问题请及时联系！', '1');

INSERT INTO `ecs_shop_config` VALUES ('9038', '90', 'ecsdxt_aliyun_appkey', 'text', '', '', '', '1');
INSERT INTO `ecs_shop_config` VALUES ('9039', '90', 'ecsdxt_aliyun_appsecret', 'text', '', '', '', '1');