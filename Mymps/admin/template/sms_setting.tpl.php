<?php include mymps_tpl('inc_head');?>
<form method="post" action="sms.php?part=<?=$part?>">
<div id="<?=MPS_SOFTNAME?>">
<table border="0" cellspacing="0" cellpadding="0" class="vbm">
<tr class="firstr"><td colspan="2">配置短信供应商接口</td></tr>
<tr bgcolor="#ffffff">
<td width="25%">
供应商:  &nbsp;&nbsp;
</td>
<td>
<label for="dxton"><input class="radio" name="sms_service" type="radio" id="dxton" value="dxton" onclick='document.getElementById("sms_div").style.display = "";' <?php if($sms_config[sms_service] == 'dxton'){?>checked="checked"<?}?>>通道一</label>
<label for="ihuyi"><input name="sms_service" type="radio" class="radio" id="ihuyi" value="ihuyi" onclick='document.getElementById("sms_div").style.display = "";' <?php if($sms_config[sms_service] == 'ihuyi'){?>checked="checked"<?}?>>通道二</label>
<label for="weimi"><input name="sms_service" type="radio" class="radio" id="weimi" value="weimi" onclick='document.getElementById("sms_div").style.display = "";' <?php if($sms_config[sms_service] == 'weimi'){?>checked="checked"<?}?>>通道三</label>
<label for="chanyoo"><input class="radio" name="sms_service" type="radio" id="chanyoo" value="chanyoo" onclick='document.getElementById("sms_div").style.display = "";' <?php if($sms_config[sms_service] == 'chanyoo'){?>checked="checked"<?}?>><a href='https://market.aliyun.com/products/57126001/cmapi030182.html' target='_blank'>畅友网络</a></label>
<label for="no"><input class="radio" name="sms_service" type="radio" id="no" value="no" onclick='document.getElementById("sms_div").style.display = "none";' <?php if($sms_config[sms_service] == 'no' || empty($sms_config[sms_service])){?>checked="checked"<?}?>>不启用</label>
</td>
</tr>
<tbody id="sms_div" <?php if($sms_config['sms_service'] == 'no' || empty($sms_config['sms_service'])){?>style="display:none"<?php }?>>
<tr bgcolor="#ffffff">
<td>
帐号（AppKey）
</td>
<td><input class="text" type="text" name="sms_user" value="<?=$sms_config[sms_user]?>"></td>
</tr>
<tr bgcolor="#ffffff">
<td>
密码（AppSecret）
</td>
<td><input class="text" type="password" name="sms_pwd" value="<?=$sms_config[sms_pwd]?>"></td>
</tr>
<tr bgcolor="#ffffff">
<td>
新用户注册模板内容/模板ID
</td>
<td><input class="text" style="width:600px" name="sms_regtpl" value="<?=$sms_config[sms_regtpl]?>"></td>
</tr>
<tr bgcolor="#ffffff">
<td>
找回密码模板内容/模板ID
</td>
<td><input class="text" style="width:600px" name="sms_pwdtpl" value="<?=$sms_config[sms_pwdtpl]?>"></td>
</tr>
</tbody>
</table>
</div>
<center><input type="submit" value="提 交" class="mymps large" name="sms_submit"/>  </center>
</form>
<?php mymps_admin_tpl_global_foot();?>