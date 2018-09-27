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
// | ModelName: 
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\FrontendController;
class ChanyooController extends FrontendController{
	 
	public function _initialize(){
        parent::_initialize();
    }

	public function install(){
		//exit('骑士CMS短信插件安装');

$newsql=<<<EOF

INSERT INTO __PREFIX__sms VALUES ('', '畅友网络', '', 'chanyoo', '', '0', '购买地址：https://market.aliyun.com/products/57126001/cmapi030182.html', '1456373436', '1469004894', '0', '0');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_applyjobs', '申请职位', '{sitename}提醒您:{personalfullname}申请了您发布的职位{jobsname}，请登录{sitedomain}查看', 'chanyoo', '', '【网站名称】{sitename}【申请人姓名】{personalfullname}【职位名称】{jobsname}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_invite', '邀请面试', '{sitename}提醒您：{companyname}对您发起了面试邀请，请登录{sitedomain}查看', 'chanyoo', '', '【网站名称】{sitename}【企业名称】{companyname}【职位名称】{jobsname}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_order', '申请充值', '{sitename}提醒您：订单{oid}已经添加成功，付 款方式为：{paymenttpye}，应付金额{amount}。请登录{sitedomain}查看', 'chanyoo', '', '【网站名称】{sitename}【订单号】{oid}【付 款方式】{paymenttpye}【应付金额】{amount}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_payment', '充值成功', '{sitename}提醒您：充值成功，系统已为您开通服务，请登录{sitedomain}查看', 'chanyoo', '', '【网站名称】{sitename}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_editpwd', '修改密码', '{sitename}提醒您：您的密码修改成功，新密码为：{newpassword}', 'chanyoo', '', '【网站名称】{sitename}【新密码】{newpassword}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_jobsallow', '职位审核通过', '{sitename}提醒您：职位({jobsname})已经通过审核！请登录{sitedomain}查看', 'chanyoo', '', '【网站名称】{sitename}【职位名称】{jobsname}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_jobsnotallow', '职位审核未通过', '{sitename}提醒您：职位({jobsname})未通过审核，请修改后再次提交审核！请登录{sitedomain}查看', 'chanyoo', '', '【网站名称】{sitename}【职位名称】{jobsname}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_licenseallow', '营业执照审核通过', '{sitename}提醒您：您的企业资料已认证通过！请登录{sitedomain}查看', 'chanyoo', '', '【网站名称】{sitename}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_licensenotallow', '营业执照审核未通过', '{sitename}提醒您：你的企业认证未通过，请重新上传营业执照！请登录{sitedomain}查看', 'chanyoo', '', '【网站名称】{sitename}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_resumeallow', '简历审核通过', '{sitename}提醒您：您的简历已通过审核！请登录{sitedomain}查看', 'chanyoo', '', '【网站名称】{sitename}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_resumenotallow', '简历审核未通过', '{sitename}提醒您：您的简历未通过审核，请修改后再次提交审核！请登录{sitedomain}查看', 'chanyoo', '', '【网站名称】{sitename}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_login', '手机登录验证', '您正在登录{sitename}的会员,手机验证码为:{rand},此验证码有效期为10分钟', 'chanyoo', '', '【网站名称】{sitename}【验证码】{rand}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_testing', '测试', '您的手机号：13012345678，验证码：110426，请及时完成验证，如不是本人操作请忽略。', 'chanyoo', '', '', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_retrieve_password', '找回密码', '您正在找回{sitename}的会员密码,手机验证码为:{rand},此验证码有效期为10分钟', 'chanyoo', '', '【网站名称】{sitename}【验证码】{rand}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_register', '注册账号', '您正在注册{sitename}的会员,手机验证码为:{rand},此验证码有效期为10分钟', 'chanyoo', '', '【网站名称】{sitename}【验证码】{rand}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_register_resume', '快速注册简历', '欢迎您注册{sitename}，用户名：{username}，密码：{password}。您也可以直接用手机号登录。', 'chanyoo', '', '【网站名称】{sitename}【用户名】{username}【密码】{password}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_mobile_auth', '手机认证', '感谢您使用{sitename}手机认证,验证码为:{rand}', 'chanyoo', '', '【网站名称】{sitename}【验证码】{rand}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_mobile_verify', '手机验证', '感谢您使用{sitename}手机验证,验证码为:{rand}', 'chanyoo', '', '【网站名称】{sitename}【验证码】{rand}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_resume_photoallow', '简历头像审核通过', '{sitename}提醒您：您的简历头像已审核通过！请登录查看', 'chanyoo', '', '【网站名称】{sitename}', '');
INSERT INTO __PREFIX__sms_templates  VALUES ('', 'set_resume_photonotallow', '简历头像审核未通过', '{sitename}提醒您：您的简历头像审核未通过！请登录查看', 'chanyoo', '', '【网站名称】{sitename}', '');

EOF;
		$Model = new \Think\Model();
		//$res = $Model->execute("update __PREFIX__sms set status=0 where alias='chanyoo'");

		$sqls = explode(";", $newsql);
		foreach ($sqls as $sql) {
			$sql = trim($sql);
			if (empty($sql)) {
				continue;
			}
			if(!$res = $Model->execute($sql)) {
				echo "执行sql语句出错： ".$Model->getDbError();
				exit();
			}
		}

		if($res){
			echo '骑士CMS短信插件安装成功,请删除Application/Home/Controller/ChanyooController.class.php文件！';
		}else{
			echo '骑士CMS短信插件安装失败！';
		}
    }
}
?>