<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"/database/webroot/yanglao/public/../application/admin/view/filter/price.html";i:1593569117;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑价格区间</title>
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
    <legend>编辑价格区间</legend>
</fieldset>

<form class="layui-form" action="">
    <input type="hidden" value="<?php echo $id; ?>" name="id">
    <div class="layui-form-item">
        <label class="layui-form-label">价格</label>
        <div class="layui-input-block margin-right">
            <textarea name="price" style="resize: none;" placeholder="请输入内容" class="layui-textarea"><?php echo $price; ?></textarea>
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
            price: function(value){
                if(value.length < 1){
                    return '请输入价格区间';
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(data){
            $.ajax({
                url:'/admin/filter/update',
                dataType:'json',
                type:'post',
                data: {
                    price:data.field.price,
                    id:data.field.id
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