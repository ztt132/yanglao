<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"/database/webroot/yanglao/public/../application/admin/view/district/edit.html";i:1603173319;}*/ ?>
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
    <legend>编辑区域</legend>
</fieldset>

<form class="layui-form" action="">
    <input type="hidden" name="id" value="<?php echo $district['id']; ?>">
    <div class="layui-form-item">
        <label class="layui-form-label">城市</label>
        <div class="layui-input-block margin-right">
            <select name="city" lay-filter="city">
                <?php if(is_array($citys) || $citys instanceof \think\Collection || $citys instanceof \think\Paginator): if( count($citys)==0 ) : echo "" ;else: foreach($citys as $key=>$city): ?>
                <option value="<?php echo $city['id']; ?>" <?php if($city['id'] == $district['city_id']): ?>selected="selected"<?php endif; ?>><?php echo $city['city_name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">区域</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="name" lay-verify="name" autocomplete="off" placeholder="请输入区域" class="layui-input" value="<?php echo $district['name']; ?>">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">经度</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="lng" lay-verify="lng" placeholder="请输入经度" class="layui-input" value="<?php echo $district['lng']; ?>">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">纬度</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="lat" lay-verify="lat" placeholder="请输入纬度" class="layui-input" value="<?php echo $district['lat']; ?>">
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
            var city_id = data.field.city;
            var id = data.field.id;
            var name = data.field.name;
            var lat = data.field.lat;
            var lng = data.field.lng;
            $.ajax({
                url:'/admin/district/update',
                dataType:'json',
                type:'post',
                data: {
                    name: name,
                    id:id,
                    city_id:city_id,
                    lat:lat,
                    lng:lng
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