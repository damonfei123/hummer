// JavaScript Document
function phoneCheck(phoneCheckUrl){
	var phone=$("#phone").val();
	if(phone==""){
		$('#phone').popover('destroy');
		$("#phone").popover({
			"container": "body",
			"trigger": "click",
			"content": "请输入您的手机号"
		});
		$("#phone").focus();
		$("#phone").popover('show');
		return false; 
	}
	if(!(/^1[3|4|5|8][0-9]\d{4,8}$/.test(phone))){ 
		$('#phone').popover('destroy');
		$("#phone").popover({
			"container": "body",
			"trigger": "click",
			"content": "您输入的手机号格式不正确"
		});
		$("#phone").focus();
		$("#phone").popover('show');
		return false; 
	}
	var flag = true;
	$.ajax({
		url:phoneCheckUrl,
		data:"phone="+phone,
		type:"post",
		async:false,  // 设置同步方式
		cache:false,
		dataType:"text",
		success: function(data){
			if(data!=""){
				$('#phone').popover('destroy');
				$("#phone").popover({
					"container": "body",
					"trigger": "click",
					"content": "您输入的手机号已经被注册"
				});
				$("#phone").focus();
				$("#phone").popover('show');
				flag = false;
			}
		},
	});
	if(!flag){return false;}
	$('#phone').popover('destroy');
	return true;
}


function msgCheck(msgCheckUrl){
	var msg=$("#msg").val();	
	if(msg==''){
		$('#getmsg').popover('destroy');
		$("#getmsg").popover({
			"container": "body",
			"trigger": "click",
			"content": "请输入您收到的短信验证码"
		});
		$("#msg").focus();
		$("#getmsg").popover('show');
		return false; 
	}
	var flag = true;
	$.ajax({
		url:msgCheckUrl,
		data:"msg="+msg,
		type:"post",
		async:false,  // 设置同步方式
		cache:false,
		dataType:"text",
		success: function(data){
			if(data!=1){
				$('#getmsg').popover('destroy');
				$("#getmsg").popover({
					"container": "body",
					"trigger": "click",
					"content": "您输入的短信验证码不正确"
				});
				$("#msg").focus();
				$("#getmsg").popover('show');
				flag = false;
			}
		},
	});
	if(!flag){return false;}
	$('#getmsg').popover('destroy');
	return true;	
}
function pwCheck(){
	var pw=$("#password").val();
	if(!(/^[a-zA-Z0-9]\w{5,15}$/.test(pw))){
		$('#password').popover('destroy');
		$("#password").popover({
			"container": "body",
			"trigger": "click",
			"content": "您输入的密码长度须在6~16位之间"
		});
		$("#password").focus();
		$("#password").popover('show');
		return false; 
	}
	$('#password').popover('destroy');
	return true;
}
function repwCheck(){
	var pw=$("#password").val();
	var repw=$("#repassword").val();
	if(repw!=pw){
		$('#repassword').popover('destroy');
		$("#repassword").popover({
			"container": "body",
			"trigger": "click",
			"content": "您两次输入的密码不一致"
		});
		$("#repassword").focus();
		$("#repassword").popover('show');
		return false; 
	}
	$('#repassword').popover('destroy');
	return true;
}
function vyCodeCheck(vyCodeCheckUrl){
	var vycode=$("#vycode").val();	
	if(vycode==''){
		$('#codeimg').popover('destroy');
		$("#codeimg").popover({
			"container": "body",
			"trigger": "click",
			"content": "请您输入图形验证码"
		});
		$("#vycode").focus();
		$("#codeimg").popover('show');
		return false; 
	}
	var flag = true;
	$.ajax({
		url:vyCodeCheckUrl,
		data:"vycode="+vycode,
		type:"post",
		async:false,  // 设置同步方式
		cache:false,
		dataType:"text",
		success: function(data){
			if(data!=1){
				$('#codeimg').popover('destroy');
				$("#codeimg").popover({
					"container": "body",
					"trigger": "click",
					"content": "您输入的图形验证码不正确"
				});
				$("#vycode").focus();
				$("#codeimg").popover('show');
				flag = false;
			}
		},
	});
	if(!flag){return false;}
	$('#codeimg').popover('destroy');
	return true;
}
function amountCheck(){
	var amount=$("input#amount").val();
	var size=$.trim(amount).length;
	if(size==0){	
		$('#amount').popover('destroy');
		$("#amount").popover({
			"container": "body",
			"trigger": "click",
			"content": "请输入您的借款金额"
		});
		$("#amount").focus();
		$("#amount").popover('show');
		return false;
	};
	if(amount%1!=0){	
		$('#amount').popover('destroy');
		$("#amount").popover({
			"container": "body",
			"trigger": "click",
			"content": "您输入的借款金额须为整数"
		});
		$("#amount").focus();
		$("#amount").popover('show');
		return false;
	};
	if(amount<1||amount>200){	
		$('#amount').popover('destroy');
		$("#amount").popover({
			"container": "body",
			"trigger": "click",
			"content": "借款金额为1万~200万元之间"
		});
		$("#amount").focus();
		$("#amount").popover('show');
		return false;
	};
	$('#amount').popover('destroy');
	return true;
}
function rateCheck(){
	var rate=$("input#rate").val();
	if(rate%1!=0){	
		$('#rate').popover('destroy');
		$("#rate").popover({
			"container": "body",
			"trigger": "click",
			"content": "借款年利率须为整数"
		});
		$("#rate").focus();
		$("#rate").popover('show');
		return false;
	};
	if(rate<15||rate>30){	
		$('#rate').popover('destroy');
		$("#rate").popover({
			"container": "body",
			"trigger": "click",
			"content": "借款年利率须在15%-30%之间"
		});
		$("#rate").focus();
		$("#rate").popover('show');
		return false;
	}
	$('#rate').popover('destroy');
	return true;
}
function psCheck(){
	var size=$.trim($("textarea#ps").val()).length;
	if(size>50){	
		$('#ps').popover('destroy');
		$("#ps").popover({
			"container": "body",
			"trigger": "click",
			"content": "备注说明不得超过50个字符"
		});
		$("#ps").focus();
		$("#ps").popover('show');
		return false;
	}
	$('#ps').popover('destroy');
	return true;
}
function agreementCheck(){
	if(document.getElementById("agreement").checked==false){
		$('#agreement').popover('destroy');
		$("#agreement").popover({
			"container": "body",
			"trigger": "click",
			"content": "请阅读并同意签署相关协议"
		});
		$("#agreement").focus();
		$("#agreement").popover('show');
		return false;
	}
	$('#agreement').popover('destroy');
	return true;
}