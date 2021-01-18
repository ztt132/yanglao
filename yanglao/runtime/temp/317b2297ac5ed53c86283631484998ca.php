<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:87:"/Users/cesc/365web/yanglao/yanglao/public/../application/admin/view/equipment/edit.html";i:1593415930;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑设备设施</title>
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
    .pic_preview{max-width: 200px;margin-left: 110px;}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>编辑设备设施</legend>
</fieldset>

<form class="layui-form" action="">
    <input type="hidden" name="id" value="<?php echo $equipment['id']; ?>">
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">名称</label>
        <div class="layui-input-block margin-right">
            <input value="<?php echo $equipment['name']; ?>" type="text" name="name" lay-verify="name" placeholder="请输入名称" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">排序</label>
        <div class="layui-input-block margin-right">
            <input value="<?php echo $equipment['sort']; ?>" type="text" name="sort" lay-verify="sort" placeholder="请输入排序" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">图片</label>
        <button type="button" class="layui-btn" id="upload_btn">上传</button>
        <div class="layui-upload-list">
            <input type="hidden" id="pic" lay-verify="pic" name="pic" value="<?php echo $equipment['pic']; ?>">
            <img class="pic_preview" id="pic_preview" src="<?php echo $equipment['pic']; ?>">
            <p id="pic_text"></p>
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
    layui.use(['form','upload'], function(){

        var form = layui.form;
        var upload = layui.upload;
        var $ = layui.jquery;
        //自定义验证规则
        form.verify({
            name: function(value){
                if(!value){
                    return '请输入名称';
                }
            },
            sort: function(value){
                if(!value){
                    return '请输入排序';
                }
                var preg = /^\d+$/;
                if (!preg.test(value)) {
                    return '请输入数字';
                }
            },
            pic: function(value){
                if(!value){
                    return '请上传图片';
                }
            }
        });

        // 上传图片
        upload.render({
            elem: '#upload_btn',
            url: '/admin/file/upload', //改成您自己的上传接口
            done: function(res){
                parent.layer.msg(res.msg);
                if (res.code == 0) {
                    $('#pic_preview').attr('src', res.data.url);
                    $('#pic').val(res.data.url);
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(data){
            $.ajax({
                url:'/admin/equipment/update',
                dataType:'json',
                type:'post',
                data:{
                    name:data.field.name,
                    sort:data.field.sort,
                    pic:data.field.pic,
                    id:data.field.id
                },
                success:function(res){
                    parent.layer.msg(res.msg);
                    if(res.code == 0){
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        parent.layui.table.reload('list')
                    }else{
                        $("#submit").attr('disabled',false);
                    }
                }
            })
            return false;
        });
    });
</script>

</body>
</html>