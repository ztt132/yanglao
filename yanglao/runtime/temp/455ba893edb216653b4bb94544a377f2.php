<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:77:"/database/webroot/yanglao/public/../application/admin/view/community/add.html";i:1605075601;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新增社区</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/layui/css/layui.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
    <script src="XADMIN_BASE_DIR/js/org.js"></script>
</head>
<body>
<style>
    .margin-right{margin-right: 110px}
    .require{color: red;font-weight: bold;font-size: 14px}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>添加社区</legend>
</fieldset>

<form class="layui-form" action="">
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>城市</label>
        <div class="layui-input-block margin-right">
            <select name="city_id" lay-filter="city_id" id="city_id" lay-verify="city_id">
                <option value="" selected>请选择城市</option>
                <?php if(is_array($city_district) || $city_district instanceof \think\Collection || $city_district instanceof \think\Paginator): if( count($city_district)==0 ) : echo "" ;else: foreach($city_district as $key=>$item): ?>
                <option value="<?php echo $key; ?>"><?php echo $item['city_name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>区域</label>
        <div class="layui-input-block margin-right">
            <select name="district_id" lay-filter="district_id" id="district_id" lay-verify="district_id">
                <option value="" selected>请选择区域</option>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>街道</label>
        <div class="layui-input-block margin-right">
            <select name="street_id" lay-filter="street_id" id="street_id" lay-verify="street_id">
                <option value="" selected>请选择街道</option>
            </select>
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label"><span class="require">*</span>社区名称</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="name" lay-verify="name" class="layui-input">
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
    // 加载所有城市以及区域
    var cityDistrict = JSON.parse('<?php echo json_encode($city_district); ?>');
    console.log(cityDistrict);
    layui.use(['form'], function(){
        var form = layui.form;

        //自定义验证规则
        form.verify({
            city_id: function(value){
                if(!value){
                    return '请选择城市';
                }
            },
            district_id: function(value){
                if(!value){
                    return '请选择区域';
                }
            },
            street_id: function(value){
                if(!value){
                    return '请选择街道';
                }
            },
            name: function(value){
                if(value.length < 1){
                    return '请输入社区';
                }
            }
        });

        // 初始化时，加载区域
        var nowCityId = $('#city_id').val();
        if (nowCityId) {
            loadDistrictOptions(nowCityId);
        }

        // 城市区域联动
        form.on('select(city_id)', function(data){
            // 切换的城市id
            loadDistrictOptions(data.value);
        });

        // 区域联动
        form.on('select(district_id)', function(data){
            console.log('change street:'+data.value);
            // 切换的区域
            loadStreetOptions(data.value);
        });

        //监听提交
        form.on('submit(submit)', function(data){
            var name = data.field.name.trim();
            var city_id = data.field.city_id;
            var district_id = data.field.district_id;
            var street_id = data.field.street_id;
            $.ajax({
                url:'/admin/community/create',
                dataType:'json',
                type:'post',
                data:{
                    name:name,
                    city_id:city_id,
                    district_id:district_id,
                    street_id:street_id
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