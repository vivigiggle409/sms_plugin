# Xiuno BBS 介绍
Xiuno BBS 4.0 是一款轻论坛产品，前端基于 BootStrap 4.0、JQuery 3，后端基于 PHP/7 MySQL XCache/Yac/Redis/Memcached...，自适应手机、平板、PC，有着非常方便的插件机制，不仅仅是一个轻论坛，还是一个良好的二次开发平台。 

# 短信插件使用流程
## 一、购买阿里云市场短信接口：
https://market.aliyun.com/products/57126001/cmapi030182.html

![购买阿里云市场短信接口](https://github.com/320266360/sms_plugin/blob/master/XiunoBBS/images/01.png "购买阿里云市场短信接口")


## 二、登录阿里云控制台-云市场-已购买的服务获取AppKey和AppSecret点复制：
https://market.console.aliyun.com/#/bizlist

![登录阿里云控制台-云市场-已购买的服务获取AppKey和AppSecret点复制](https://github.com/320266360/sms_plugin/blob/master/XiunoBBS/images/02.png "登录阿里云控制台-云市场-已购买的服务获取AppKey和AppSecret点复制")


## 三、直接帖子附件下载插件解压后上传插件到网站plugin目录：

![直接帖子附件下载插件解压后上传插件到网站plugin目录](https://github.com/320266360/sms_plugin/blob/master/XiunoBBS/images/03.png "直接帖子附件下载插件解压后上传插件到网站plugin目录")


## 四、登陆网站后台插件-本地插件安装插件配置接口参数：

![登陆网站后台插件-本地插件安装插件配置接口参数](https://github.com/320266360/sms_plugin/blob/master/XiunoBBS/images/04.png "登陆网站后台插件-本地插件安装插件配置接口参数")

插件设置：

是否开启手机号注册：是

是否开启手机重置密码：是

登陆方式：手机号/Email/用户名

开启绑定手机号：是

强制绑定手机才能发帖：是

强制绑定手机帖子才能显示：否

阿里云AppKey：阿里云控制台-云市场-已购买的服务列表AppKey 复制粘贴到这里

阿里云AppSecret：阿里云控制台-云市场-已购买的服务列表AppSecret 复制粘贴到这里

短信模板内容：您的手机号：{$mobile}，验证码：{$code}，请及时完成验证，如不是本人操作请忽略。【Xiuno开源】

![登陆网站后台插件-本地插件安装插件配置接口参数](https://github.com/320266360/sms_plugin/blob/master/XiunoBBS/images/05.png "登陆网站后台插件-本地插件安装插件配置接口参数")


## 五、点确定提交插件参数设置：

![点确定提交插件参数设置](https://github.com/320266360/sms_plugin/blob/master/XiunoBBS/images/06.png "点确定提交插件参数设置")


## 六、如果修改了默认签名【Xiuno开源】请先到自助模板报备地址报备模板：
自助模板报备地址：https://api.chanyoo.net/aliyun/template.html 

![如果修改了默认签名【Xiuno开源】请先到自助模板报备地址报备模板](https://github.com/320266360/sms_plugin/blob/master/XiunoBBS/images/07.png "如果修改了默认签名【Xiuno开源】请先到自助模板报备地址报备模板")


## 七、网站前台注册页面输入手机号点发送短信：

![网站前台注册页面输入手机号点发送短信](https://github.com/320266360/sms_plugin/blob/master/XiunoBBS/images/08.png "网站前台注册页面输入手机号点发送短信")


## 八、收到注册短信验证码后输入点下一步完成注册：

![收到注册短信验证码后输入点下一步完成注册](https://github.com/320266360/sms_plugin/blob/master/XiunoBBS/images/09.png "收到注册短信验证码后输入点下一步完成注册")


## 九、安装插件后，继续在Xiuno后台插件里面安装xn_vcode验证码或者【Gingerbbs】验证码插件，安装验证码插件的目的是为了保证正式运营的网站不受恶意刷短信的骚扰，必须输入正确的图形验证码后才能继续获取验证码短信，可以有效的阻止刷验证码短信的恶意请求，由于恶意刷短信导致网站购买的短信套餐包被消耗完需自己承担责任。

![继续在Xiuno后台插件里面安装xn_vcode验证码或者【Gingerbbs】验证码插件](https://github.com/320266360/sms_plugin/blob/master/XiunoBBS/images/10.png "继续在Xiuno后台插件里面安装xn_vcode验证码或者【Gingerbbs】验证码插件")

![继续在Xiuno后台插件里面安装xn_vcode验证码或者【Gingerbbs】验证码插件](https://github.com/320266360/sms_plugin/blob/master/XiunoBBS/images/11.png "继续在Xiuno后台插件里面安装xn_vcode验证码或者【Gingerbbs】验证码插件")


## 十、至此整个流程完毕，如果需要可以自行下载安装测试。



## [欢迎各类应用接入，接口简单，对接方便，审核快速，价格便宜，服务周到，联系 QQ：320266360 （微信同号）平台已经入驻阿里云市场，量大优惠。](https://market.aliyun.com/products/57126001/cmapi030182.html)
