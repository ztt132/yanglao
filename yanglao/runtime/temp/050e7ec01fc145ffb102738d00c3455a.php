<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:84:"/Users/cesc/365web/yanglao/yanglao/public/../application/admin/view/account/add.html";i:1593335708;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新增用户</title>
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
    <legend>新增用户</legend>
</fieldset>

<form class="layui-form" action="">
    <div class="layui-form-item">
        <label class="layui-form-label">用户名</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="user_name" lay-verify="user_name" placeholder="请输入用户名" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">账户</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="account_name" lay-verify="account_name" placeholder="请输入账户" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">密码</label>
        <div class="layui-input-block margin-right">
            <input type="password" name="password" lay-verify="password" placeholder="请输入密码" autocomplete="off" class="layui-input">
        </div>
    </div>
    <?php if($account_name != 'admin'): ?>
    <div class="layui-form-item">
        <label class="layui-form-label">角色</label>
        <div class="layui-input-block margin-right">
            <select name="role_id" lay-filter="role_id" lay-verify="role_id">
                <option value="">请选择角色</option>
                <?php if(is_array($roles) || $roles instanceof \think\Collection || $roles instanceof \think\Paginator): if( count($roles)==0 ) : echo "" ;else: foreach($roles as $key=>$role): ?>
                <option value="<?php echo $role['id']; ?>"><?php echo $role['name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <?php endif; ?>
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
            user_name: function(value){
                if(value.length < 1){
                    return '请输入用户名';
                }
            },
            account_name: function(value){
                if(value.length < 1){
                    return '请输入账户';
                }
            },
            password: function(value){
                if(value.length < 1){
                    return '请输入密码';
                }
            },
            role_id: function(value){
                if(value.length < 1){
                    return '请选择角色';
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(data){
            $.ajax({
                url:'/admin/account/create',
                dataType:'json',
                type:'post',
                data:{
                    user_name:data.field.user_name,
                    account_name:data.field.account_name,
                    password:data.field.password,
                    role_id:data.field.role_id
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