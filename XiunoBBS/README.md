# Xiuno BBS 介绍
Xiuno BBS 4.0 是一款轻论坛产品，前端基于 BootStrap 4.0、JQuery 3，后端基于 PHP/7 MySQL XCache/Yac/Redis/Memcached...，自适应手机、平板、PC，有着非常方便的插件机制，不仅仅是一个轻论坛，还是一个良好的二次开发平台。 

# 短信插件使用流程
## 一、购买阿里云市场短信接口：
https://market.aliyun.com/products/57126001/cmapi030182.html

![购买阿里云市场短信接口](http://bbs.xiuno.com/upload/attach/201809/20361_J7MEDEQKMZHU2HU.png "购买阿里云市场短信接口")


## 二、登录阿里云控制台-云市场-已购买的服务获取AppKey和AppSecret点复制：
https://market.console.aliyun.com/#/bizlist

![登录阿里云控制台-云市场-已购买的服务获取AppKey和AppSecret点复制](http://bbs.xiuno.com/upload/attach/201809/20361_4F7ZE8BJN9XF3MT.png "登录阿里云控制台-云市场-已购买的服务获取AppKey和AppSecret点复制")


## 三、直接帖子附件下载插件解压后上传插件到网站plugin目录：

![直接帖子附件下载插件解压后上传插件到网站plugin目录](http://bbs.xiuno.com/upload/attach/201809/20361_JTYEDPUGMRA8HZK.png "直接帖子附件下载插件解压后上传插件到网站plugin目录")


## 四、登陆网站后台插件-本地插件安装插件配置接口参数：

![登陆网站后台插件-本地插件安装插件配置接口参数](http://bbs.xiuno.com/upload/attach/201809/20361_VF8DQMGUAUU895Y.png "登陆网站后台插件-本地插件安装插件配置接口参数")

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

![登陆网站后台插件-本地插件安装插件配置接口参数](http://bbs.xiuno.com/upload/tmp/20361_BY6QYMYTHSW62BC.png "登陆网站后台插件-本地插件安装插件配置接口参数")


## 五、点确定提交插件参数设置：

![点确定提交插件参数设置](http://bbs.xiuno.com/upload/tmp/20361_6M3A269UBBY5MYA.png "点确定提交插件参数设置")


## 六、如果修改了默认签名【Xiuno开源】请先到自助模板报备地址报备模板：
自助模板报备地址：https://api.chanyoo.net/aliyun/template.html 

![如果修改了默认签名【Xiuno开源】请先到自助模板报备地址报备模板](http://bbs.xiuno.com/upload/attach/201809/20361_29497DQVHPC5QE7.png "如果修改了默认签名【Xiuno开源】请先到自助模板报备地址报备模板")


## 七、网站前台注册页面输入手机号点发送短信：

![网站前台注册页面输入手机号点发送短信](http://bbs.xiuno.com/upload/attach/201809/20361_SCTGNXYAAXWGMQ3.png "网站前台注册页面输入手机号点发送短信")


## 八、收到注册短信验证码后输入点下一步完成注册：

![收到注册短信验证码后输入点下一步完成注册](http://bbs.xiuno.com/upload/attach/201809/20361_WSUM4X5VHRHKMRB.png "收到注册短信验证码后输入点下一步完成注册")


## 九、安装插件后，继续在Xiuno后台插件里面安装xn_vcode验证码或者【Gingerbbs】验证码插件，安装验证码插件的目的是为了保证正式运营的网站不受恶意刷短信的骚扰，必须输入正确的图形验证码后才能继续获取验证码短信，可以有效的阻止刷验证码短信的恶意请求，由于恶意刷短信导致网站购买的短信套餐包被消耗完需自己承担责任。

![继续在Xiuno后台插件里面安装xn_vcode验证码或者【Gingerbbs】验证码插件](http://bbs.xiuno.com/upload/attach/201809/20361_WW57GNRXZE3QN5Z.png "继续在Xiuno后台插件里面安装xn_vcode验证码或者【Gingerbbs】验证码插件")

![继续在Xiuno后台插件里面安装xn_vcode验证码或者【Gingerbbs】验证码插件](http://bbs.xiuno.com/upload/attach/201809/20361_2SDK3EEG6NNFVJB.png "继续在Xiuno后台插件里面安装xn_vcode验证码或者【Gingerbbs】验证码插件")


## 十、至此整个流程完毕，如果需要可以自行下载安装测试。



## [欢迎各类应用接入，接口简单，对接方便，审核快速，价格便宜，服务周到，联系 QQ：320266360 （微信同号）平台已经入驻阿里云市场，量大优惠。](https://market.aliyun.com/products/57126001/cmapi030182.html)