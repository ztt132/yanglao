<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"/database/webroot/yanglao/public/../application/admin/view/login/index.html";i:1595384119;}*/ ?>
<!doctype html>
<html  class="x-admin-sm">
<head>
	<meta charset="UTF-8">
	<title>养老小程序</title>
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/font.css">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/login.css">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/xadmin.css">
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <!--[if lt IE 9]>
      <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
      <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="login-bg">
    
    <div class="login layui-anim layui-anim-up">
        <div class="message">养老小程序-管理登录</div>
        <div id="darkbannerwrap"></div>
        
        <form method="post" class="layui-form" >
            <input name="account_name" placeholder="用户名"  type="text" lay-verify="required" lay-reqText="用户名" class="layui-input" >
            <hr class="hr15">
            <input name="password" lay-verify="required" placeholder="密码"  type="password" class="layui-input">
            <hr class="hr15">
            <input value="登录" lay-submit lay-filter="login" style="width:100%;" type="submit">
            <hr class="hr20" >
        </form>
    </div>

<script>
    $(function () {
        layui.use('form', function(){
            var form = layui.form;
            //监听提交,执行登录操作
            form.on('submit(login)', function(data){
                var account_name = data.field.account_name.trim();
                var password = data.field.password.trim();
                $.ajax({
                    url:'/admin/login/doLogin',
                    dataType:'json',
                    type:'post',
                    data:{
                        account_name:account_name,
                        password:password
                    },
                    success:function(ret){
                        layer.msg(ret.msg);
                        if (ret.code == 0) {
                            location.href = '/admin/index';
                        }
                    }
                })
                return false;
            });
        });
    })
</script>
</body>
</html>