<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:79:"/database/webroot/yanglao/public/../application/admin/view/account/editpwd.html";i:1594017837;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑密码</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/layui/css/layui.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
</head>
<body>
<style>
    .margin-right{margin-right: 110px}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>编辑密码</legend>
</fieldset>

<form class="layui-form" action="">
    <input type="hidden" name="id" value="<?php echo $account['id']; ?>">
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">旧密码</label>
        <div class="layui-input-block margin-right">
            <input type="password" name="old_password" lay-verify="old_password" placeholder="请输入密码" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">新密码</label>
        <div class="layui-input-block margin-right">
            <input type="password" name="password" lay-verify="password" placeholder="请输入密码" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">确认密码</label>
        <div class="layui-input-block margin-right">
            <input type="password" name="password2" lay-verify="password2" placeholder="请输入密码" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="submit">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>

<script>
    layui.use(['form'], function(){
        var form = layui.form;

        //自定义验证规则
        form.verify({
            old_password: function(value){
                if(value.length < 1){
                    return '请输入旧密码';
                }
            },
            password: function(value){
                if(value.length < 1){
                    return '请输入密码';
                }
            },
            password2: function(value){
                if(value.length < 1){
                    return '请再次输入密码';
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(data){
            var pwd = data.field.password;
            var old_pwd = data.field.old_password;
            var pwd2 = data.field.password2;
            if (pwd != pwd2) {
                parent.layer.msg('密码不一致');
                return false;
            }
            $.ajax({
                url:'/admin/account/updatePwd',
                dataType:'json',
                type:'post',
                data:{
                    password:pwd,
                    id:data.field.id,
                    old_password:old_pwd
                },
                success:function(res){
                    if(res.code == 0){
                        parent.layer.msg(res.msg);
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.layui.table.reload('list')
                    }else{
                        $("#submit").attr('disabled',false);
                        parent.layer.alert(res.msg);
                    }
                }
            })
            return false;
        });
    });
</script>

</body>
</html>