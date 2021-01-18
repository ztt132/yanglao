<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:78:"/database/webroot/yanglao/public/../application/admin/view/community/edit.html";i:1605075601;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑社区</title>
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
    .require{color: red;font-weight: bold;font-size: 14px}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>编辑社区</legend>
</fieldset>

<form class="layui-form" action="">
    <input type="hidden" name="id" value="<?php echo $community['id']; ?>">
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">城市</label>
        <div class="layui-input-block margin-right">
            <input readonly type="text" name="city_name" lay-verify="city_name" class="layui-input" value="<?php echo $community['city_name']; ?>">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">区域</label>
        <div class="layui-input-block margin-right">
            <input readonly type="text" name="district_name" lay-verify="district_name" class="layui-input" value="<?php echo $community['district_name']; ?>">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">街道名称</label>
        <div class="layui-input-block margin-right">
            <input readonly type="text" name="street_name" lay-verify="street_name" class="layui-input" value="<?php echo $community['street_name']; ?>">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label"><span class="require">*</span>社区名称</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="name" lay-verify="name" class="layui-input" value="<?php echo $community['name']; ?>">
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
            name: function(value){
                if(value.length < 1){
                    return '请输入社区名称';
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(data){
            var id = data.field.id;
            var name = data.field.name;
            $.ajax({
                url:'/admin/community/update',
                dataType:'json',
                type:'post',
                data: {
                    name: name,
                    id:id
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