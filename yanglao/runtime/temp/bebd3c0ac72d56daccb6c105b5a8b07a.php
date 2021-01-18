<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:80:"/Users/cesc/365web/yanglao/yanglao/public/../application/admin/view/org/add.html";i:1593423887;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新增机构</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/layui/css/layui.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
    <script src="XADMIN_BASE_DIR/js/org.js"></script>
    <script src="XADMIN_BASE_DIR/js/yanglao.js"></script>
    <script src="XADMIN_BASE_DIR/js/edit.js"></script>
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/wangEditor/css/wangEditor.min.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/wangEditor/js/wangEditor.min.js"></script>
</head>
<body>
<style>
    .margin-right{margin-right: 110px}
    .org_tag{width: 32%;float:left;}
    .org_tag_margin_left{margin-left: 2%}
    .editor{margin-right:110px}

    .service_name{width:20%;float:left;}
    .service_desc{width:60%;float:left;}
    .service_name_desc_margin{margin-left:4px }
    .service_margin_top{margin-top: 10px}
    .add_btn_margin{margin-top: 4px}
    .btn_margin_left{margin-left:4px;margin-top:4px}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>添加机构</legend>
</fieldset>

<form class="layui-form" action="">
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">机构名称</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="name" lay-verify="name" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">城市</label>
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
        <label class="layui-form-label">区域</label>
        <div class="layui-input-block margin-right">
            <select name="district_id" lay-filter="district_id" id="district_id" lay-verify="district_id">
                <option value="" selected>请选择区域</option>
            </select>
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">价格</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="price" lay-verify="price" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">民政评级</label>
        <div class="layui-input-block margin-right">
            <select name="grade" lay-filter="grade" lay-verify="grade">
                <option value="" selected="">请选择评级</option>
                <?php if(is_array($config['grade']) || $config['grade'] instanceof \think\Collection || $config['grade'] instanceof \think\Paginator): if( count($config['grade'])==0 ) : echo "" ;else: foreach($config['grade'] as $key=>$item): ?>
                    <option value="<?php echo $key; ?>"><?php echo $item; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label">机构标签</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="tag[]" lay-verify="tag" class="layui-input org_tag">
            <input type="text" name="tag[]" lay-verify="tag" class="layui-input org_tag org_tag_margin_left">
            <input type="text" name="tag[]" lay-verify="tag" class="layui-input org_tag org_tag_margin_left">
        </div>
    </div>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label">机构类型</label>
        <div class="layui-input-block">
            <?php if(is_array($config['org_type']) || $config['org_type'] instanceof \think\Collection || $config['org_type'] instanceof \think\Paginator): if( count($config['org_type'])==0 ) : echo "" ;else: foreach($config['org_type'] as $key=>$item): ?>
            <input type="checkbox"  title="<?php echo $item; ?>" lay-filter="org_type" value="<?php echo $key; ?>">
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label">医保</label>
        <div class="layui-input-block">
            <input type="checkbox" name="health_insurance" title="可用" lay-filter="health_insurance" value="1" lay-skin="switch">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">机构性质</label>
        <div class="layui-input-block margin-right">
            <select name="nature" lay-filter="nature" lay-verify="nature">
                <option value="" selected="">请选择机构性质</option>
                <?php if(is_array($config['nature']) || $config['nature'] instanceof \think\Collection || $config['nature'] instanceof \think\Paginator): if( count($config['nature'])==0 ) : echo "" ;else: foreach($config['nature'] as $key=>$item): ?>
                    <option value="<?php echo $key; ?>"><?php echo $item; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">主体公司</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="company" lay-verify="company" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">成立时间</label>
        <div class="layui-input-inline">
            <input type="text" name="set_time" id="set_time" lay-verify="set_time" autocomplete="on" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">占地面积</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="cover_area" lay-verify="cover_area" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">建筑面积</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="structure_area" lay-verify="structure_area" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">床位数</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="bed_number" lay-verify="bed_number" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">员工数</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="employee_number" lay-verify="employee_number" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">收住对象</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="target_person" lay-verify="target_person" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">服务范围</label>
        <div class="layui-input-block margin-right">
            <?php if(is_array($config['service_scope']) || $config['service_scope'] instanceof \think\Collection || $config['service_scope'] instanceof \think\Paginator): if( count($config['service_scope'])==0 ) : echo "" ;else: foreach($config['service_scope'] as $key=>$item): ?>
            <input type="checkbox" title="<?php echo $item; ?>" value="<?php echo $key; ?>" lay-filter="service_scope">
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label">热门</label>
        <div class="layui-input-block">
            <input type="checkbox" name="is_hot" title="热门" lay-filter="is_hot" value="1" lay-skin="switch">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">入住评估标准内容</label>
        <input type="hidden" name="comment" id="comment" lay-filter="comment" lay-verify="comment">
        <div class="layui-input-block" style="z-index:0">
            <div class="editor"></div>
        </div>
    </div>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label">设施设备</label>
        <div class="layui-input-block">
            <?php if(is_array($equipments) || $equipments instanceof \think\Collection || $equipments instanceof \think\Paginator): if( count($equipments)==0 ) : echo "" ;else: foreach($equipments as $key=>$e): ?>
            <input type="checkbox" title="<?php echo $e['name']; ?>" lay-filter="equipment" value="<?php echo $key; ?>">
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>

    <div class="layui-form-item" pane="" id="serviceParent">
        <label class="layui-form-label">提供服务</label>
        <div class="layui-input-block">
            <button id="addService" type="button" class="layui-btn layui-btn-primary layui-btn-sm add_btn_margin"><i class="layui-icon"></i></button>
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
    var editor;
    layui.use(['form','laydate','jquery'], function(){
        // 富文本
        loadEditor();

        var form = layui.form,laydate = layui.laydate,$=layui.jquery;

        // 增加服务按钮事件
        $('#addService').on('click',function() {
            var serviceHtml = getService();
            $('#serviceParent').append(serviceHtml);
        })

        laydate.render({
            elem: '#set_time'
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

        //自定义验证规则
        form.verify(rule);

        //监听提交
        form.on('submit(submit)', function(data){
            var data = data.field;
            data.org_type= getCheckBoxValue('org_type');
            data.service_scope= getCheckBoxValue('service_scope');
            data.equipment= getCheckBoxValue('equipment');
            data['comment'] = editor.txt.html();
            // 处理服务数据
            data = executeService(data);
            if (!validateService()) {
                parent.layer.alert('请完善服务项');
                return false;
            }

            $.ajax({
                url:'/admin/org/create',
                dataType:'json',
                type:'post',
                data:data,
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