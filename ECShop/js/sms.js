function trim(str) {
	return str.replace(/^\s*(.*?)[\s\n]*$/g, '$1');
}

function getverifycode1(field_id, field_name, picyzm) {
	var mobile = trim($(field_id).value);
	var captcha = "";
	if(mobile == '') {
		alert("- "+field_name+" 不能为空！");
		$(field_id).focus();
		return;
	}
	if($(picyzm))
		captcha = trim($(picyzm).value);

	if($(picyzm) && captcha == '') {
		alert("- 图片验证码 不能为空！");
		$(picyzm).focus();
		return;
	}
	Ajax.call('sms.php?step=getverifycode1&r=' + Math.random(), 'mobile=' + mobile +'&captcha=' + captcha, getverifycode1Response, 'POST', 'JSON');
}

function getverifycode2() {
	var mobile = trim($("mobile").value);
	if(mobile == '') {
		alert("手机号不能为空！");
		$("mobile").focus();
		return;
	}
	Ajax.call('sms.php?step=getverifycode2&r=' + Math.random(), 'mobile=' + mobile, getverifycode2Response, 'POST', 'JSON');
}

function getverifycode1Response(result) {
	alert(result.message);
}

function getverifycode2Response(result) {
	alert(result.message);
}