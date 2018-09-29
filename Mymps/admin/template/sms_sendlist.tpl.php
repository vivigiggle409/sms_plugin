<?php include mymps_tpl('inc_head');?>
<form action="?part=sendlist" method="post"/>
<div id="<?=MPS_SOFTNAME?>">
    <table border="0" cellspacing="0" cellpadding="0" class="vbm">
    <tr class="firstr">
      <td><input class="checkbox" name="checkall" type="checkbox" id="checkall" onClick="CheckAll(this.form)"/> 删?</td>
      <td>手机号码</td>
      <td>发送内容</td>
      <td>发送状态</td>
      <td>发送时间</td>
      <td>发送接口</td>
    </tr>
	<?php foreach($list as $list){?>
        <tr bgcolor="white">
          <td><input class="checkbox" type='checkbox' name='delids[]' value='<?=$list[id]?>' id="<?=$list[id]?>"></td>
          <td><?=$list[mobile]?></td>
          <td width="40%"><font color=gray><?=$list[sms_service]=='weimi'?'已隐藏':$list[sms_content]?></font></td>
          <td width="100"><?php echo strstr($list['status'],'成功') ? '<font color=green>成功</font>' : '<font color=red>'.$list[status].'</font>' ;?></td>
          
          <td><em><?=GetTime($list[sendtime])?></em></td>
          <td><?php if($list[sms_service]=='dxton') echo '短信通'; elseif($list['sms_service']=='ihuyi') echo '互亿无线'; elseif($list['sms_service']=='weimi') echo '微米'; elseif($list['sms_service']=='chanyoo') echo '畅友网络';?></td>
        </tr>
	<?php }?>
    </table>
</div>
<center><input type="submit" value="提 交" class="mymps large" name="mail_submit"/></center>
</form>
<div class="pagination"><?php echo page2();?></div>
<?php mymps_admin_tpl_global_foot();?>