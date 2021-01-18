<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:73:"/database/webroot/yanglao/public/../application/admin/view/city/edit.html";i:1593570836;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑城市</title>
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
    <legend>编辑城市</legend>
</fieldset>

<form class="layui-form" action="">
    <input type="hidden" name="id" value="<?php echo $city['id']; ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">城市名称</label>
        <div class="layui-input-inline">
            <select name="pinyin" lay-verify="pinyin" lay-search="">
                <option value="">搜索选择</option>
                <?php if(is_array($citys) || $citys instanceof \think\Collection || $citys instanceof \think\Paginator): if( count($citys)==0 ) : echo "" ;else: foreach($citys as $key=>$item): ?>
                <option value="<?php echo $item['city_key']; ?>" <?php if($item['city_key'] == $city['pinyin']): ?>selected<?php endif; ?>><?php echo $item['city_name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
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
                    return '请输入城市名称';
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(data){
            var pinyin = data.field.pinyin;
            $.ajax({
                url:'/admin/city/update',
                dataType:'json',
                type:'post',
                data: {
                    pinyin:pinyin,
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