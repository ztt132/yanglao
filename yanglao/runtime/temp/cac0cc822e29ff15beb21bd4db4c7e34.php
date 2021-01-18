<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"/database/webroot/yanglao/public/../application/admin/view/org/edit.html";i:1599125075;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑机构</title>
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
    .price_tag{width: 10%;float:left;}
    .org_tag_margin_left{margin-left: 2%}
    .editor{margin-right:110px}
    .dean_editor{margin-right:110px}

    .service_name{width:20%;float:left;}
    .service_desc{width:60%;float:left;}
    .service_name_desc_margin{margin-left:4px }
    .service_margin_top{margin-top: 10px}
    .add_btn_margin{margin-top: 4px}
    .btn_margin_left{margin-left:4px;margin-top:4px}
    .demo-slider{width: 100%;display: inline-block;vertical-align: middle;margin-top: 15px}
    .require{color: red;font-weight: bold;font-size: 14px}
    .pic_preview{max-width: 200px;margin-left: 110px;}
    .width80{width: 80px}
    .width100{width: 100px}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>编辑机构</legend>
</fieldset>

<form class="layui-form" action="">
    <input type="hidden" value="<?php echo $org['id']; ?>" name="id" id="id">
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label"><span class="require">*</span>机构名称</label>
        <div class="layui-input-block margin-right">
            <input id="name" type="text" name="name" lay-verify="name" class="layui-input" value="<?php echo $org['name']; ?>">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>城市</label>
        <div class="layui-input-inline margin-right">
            <select id="city_id" name="city_id" lay-filter="city_id" id="city_id" lay-verify="city_id" lay-search="">
                <option value="">请选择城市</option>
                <?php if(is_array($city_district) || $city_district instanceof \think\Collection || $city_district instanceof \think\Paginator): if( count($city_district)==0 ) : echo "" ;else: foreach($city_district as $key=>$item): ?>
                <option value="<?php echo $key; ?>" <?php if($key == $org['city_id']): ?>selected<?php endif; ?>><?php echo $item['city_name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>区域</label>
        <div class="layui-input-block margin-right">
            <select name="district_id" lay-filter="district_id" id="district_id" lay-verify="district_id">
                <option value="">请选择区域</option>
                <?php if(is_array($city_district[$org['city_id']]['districts']) || $city_district[$org['city_id']]['districts'] instanceof \think\Collection || $city_district[$org['city_id']]['districts'] instanceof \think\Paginator): if( count($city_district[$org['city_id']]['districts'])==0 ) : echo "" ;else: foreach($city_district[$org['city_id']]['districts'] as $key=>$item): ?>
                <option value="<?php echo $item['district_id']; ?>" <?php if($item['district_id'] == $org['district_id']): ?>selected<?php endif; ?>><?php echo $item['district_name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">院长姓名</label>
        <div class="layui-input-block margin-right">
            <input type="text" value="<?php echo $org['dean_name']; ?>" name="dean_name" lay-verify="dean_name" placeholder="请输入院长姓名" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">院长介绍</label>
        <div class="layui-input-block margin-right">
            <input type="text" value="<?php echo $org['dean_desc']; ?>" name="dean_desc" lay-verify="dean_desc" placeholder="请输入院长介绍" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">院长头像</label>
        <button type="button" class="layui-btn" id="upload_btn">上传</button>图片限制：200KB
        <div class="layui-upload-list">
            <input type="hidden" id="dean_pic" lay-verify="dean_pic" name="dean_pic" value="<?php echo $org['dean_pic']; ?>">
            <img class="pic_preview" id="dean_pic_preview" src="<?php echo $org['dean_pic']; ?>">
            <p id="pic_text"></p>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">院长内容</label>
        <input type="hidden" name="dean_content" id="dean_content" lay-filter="dean_content" lay-verify="dean_content">
        <div class="layui-input-block" style="z-index:0">
            <div class="dean_editor"><?php echo $org['dean_content']; ?></div>
        </div>
    </div>

    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label"><span class="require">*</span>地址</label>
        <div class="layui-input-block margin-right">
            <input value="<?php echo $org['address']; ?>" type="text" name="address" lay-verify="address" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label"><span class="require">*</span>价格</label>
        <div class="layui-input-block margin-right">
            <input value="<?php echo $org['min_price']; ?>" type="text" lay-filter="min_price" name="min_price" lay-verify="min_price" placeholder="请输入最低价格" class="layui-input price_tag">
            <input value="<?php echo $org['max_price']; ?>" type="text" lay-filter="max_price" name="max_price" lay-verify="max_price" placeholder="请输入最高价格" class="layui-input price_tag org_tag_margin_left">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">民政评级</label>
        <div class="layui-input-block margin-right">
            <select name="grade" lay-filter="grade" lay-verify="grade">
                <?php if(is_array($config['grade']) || $config['grade'] instanceof \think\Collection || $config['grade'] instanceof \think\Paginator): if( count($config['grade'])==0 ) : echo "" ;else: foreach($config['grade'] as $key=>$item): ?>
                <option value="<?php echo $key; ?>" <?php if($org['grade'] == $key): ?>selected<?php endif; ?>><?php echo $item; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label"><span class="require">*</span>机构标签</label>
        <div class="layui-input-block margin-right">
            <input type="text" value="<?php echo $org['tag'][0]; ?>" name="tag[]" lay-verify="tag" class="layui-input org_tag">
            <input type="text" value="<?php echo $org['tag'][1]; ?>" name="tag[]" lay-verify="tag" class="layui-input org_tag org_tag_margin_left">
            <input type="text" value="<?php echo $org['tag'][2]; ?>" name="tag[]" lay-verify="tag" class="layui-input org_tag org_tag_margin_left">
        </div>
    </div>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label"><span class="require">*</span>机构类型</label>
        <div class="layui-input-block">
            <?php if(is_array($config['org_type']) || $config['org_type'] instanceof \think\Collection || $config['org_type'] instanceof \think\Paginator): if( count($config['org_type'])==0 ) : echo "" ;else: foreach($config['org_type'] as $key=>$item): ?>
                <input type="checkbox"  title="<?php echo $item; ?>" lay-filter="org_type" value="<?php echo $key; ?>" <?php if(in_array($key,$org['org_type'])): ?>checked<?php endif; ?>>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label">医保</label>
        <div class="layui-input-block">
            <input type="checkbox" name="health_insurance" title="可用" lay-filter="health_insurance" value="<?php echo $org['health_insurance']; ?>" lay-skin="switch" <?php if($org['health_insurance']): ?>checked<?php endif; ?>>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">机构性质</label>
        <div class="layui-input-block margin-right">
            <select name="nature" lay-filter="nature" lay-verify="nature">
                <?php if(is_array($config['nature']) || $config['nature'] instanceof \think\Collection || $config['nature'] instanceof \think\Paginator): if( count($config['nature'])==0 ) : echo "" ;else: foreach($config['nature'] as $key=>$item): ?>
                <option value="<?php echo $key; ?>" <?php if($key == $org['nature']): ?>selected<?php endif; ?>><?php echo $item; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">主体公司</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="company" lay-verify="company" class="layui-input" value="<?php echo $org['company']; ?>">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">成立时间</label>
        <div class="layui-input-inline">
            <input type="text" name="set_time" id="set_time" lay-verify="set_time"  autocomplete="on" class="layui-input" value="<?php echo $org['set_time']; ?>">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">占地面积</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="cover_area" lay-verify="cover_area" class="layui-input" value="<?php echo $org['cover_area']; ?>">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">建筑面积</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="structure_area" lay-verify="structure_area" class="layui-input" value="<?php echo $org['structure_area']; ?>">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">床位数</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="bed_number" lay-verify="bed_number" class="layui-input" value="<?php echo $org['bed_number']; ?>">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">医护人数</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="employee_number" lay-verify="employee_number" class="layui-input" value="<?php echo $org['employee_number']; ?>">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label"><span class="require">*</span>收住对象</label>
        <div class="layui-input-block margin-right">
            <?php if(is_array($config['target_person']) || $config['target_person'] instanceof \think\Collection || $config['target_person'] instanceof \think\Paginator): if( count($config['target_person'])==0 ) : echo "" ;else: foreach($config['target_person'] as $key=>$item): ?>
            <input type="checkbox" title="<?php echo $item; ?>" value="<?php echo $key; ?>" lay-filter="target_person" <?php if(in_array($key,$org['target_person'])): ?>checked<?php endif; ?>>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">服务范围</label>
        <div class="layui-input-block margin-right">
            <?php if(is_array($config['service_scope']) || $config['service_scope'] instanceof \think\Collection || $config['service_scope'] instanceof \think\Paginator): if( count($config['service_scope'])==0 ) : echo "" ;else: foreach($config['service_scope'] as $key=>$item): ?>
            <input type="checkbox" title="<?php echo $item; ?>" value="<?php echo $key; ?>" lay-filter="service_scope" <?php if(in_array($key,$org['service_scope'])): ?>checked<?php endif; ?>>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
    <!--<div class="layui-form-item" pane="">-->
        <!--<label class="layui-form-label">电话</label>-->
        <!--<div class="layui-input-block margin-right">-->
            <!--<input type="text" value="<?php echo $org['phone1']; ?>" name="phone1" lay-verify="phone1" class="layui-input org_tag">-->
        <!--</div>-->
    <!--</div>-->
    <div class="layui-form-item" pane="">
        <label class="layui-form-label"><span class="require">*</span>400电话</label>
        <div class="layui-input-block margin-right">
            <input type="text" value="<?php echo $org['prefix']; ?>" placeholder="区号,手机为0" id="prefix" name="prefix" lay-verify="prefix" class="layui-input org_tag width100">
            <input type="text" value="<?php echo $org['phone2']; ?>" placeholder="请输入号码" id="phone2" name="phone2" lay-verify="phone2" class="layui-input org_tag org_tag_margin_left">
            <input type="text" value="<?php echo $org['short_tel']; ?>" id="short_tel" readonly name="short_tel"  class="layui-input org_tag width80 org_tag_margin_left">
            <button id="bind_short" type="button" class="layui-btn org_tag_margin_left">绑定短号</button>
            <button id="delete_short" type="button" class="layui-btn org_tag_margin_left">解绑短号</button>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">入住评估标准</label>
        <input type="hidden" name="comment" id="comment" lay-filter="comment" lay-verify="comment">
        <div class="layui-input-block" style="z-index:0">
            <div class="editor"><?php echo $org['comment']; ?></div>
        </div>
    </div>
    <div class="layui-form-item" pane="">
        <label class="layui-form-label">设施设备</label>
        <div class="layui-input-block">
            <?php if(is_array($equipments) || $equipments instanceof \think\Collection || $equipments instanceof \think\Paginator): if( count($equipments)==0 ) : echo "" ;else: foreach($equipments as $key=>$e): ?>
            <input type="checkbox"  title="<?php echo $e['name']; ?>" lay-filter="equipment" value="<?php echo $e['id']; ?>" <?php if(in_array($e['id'],$org['equipment'])): ?>checked<?php endif; ?>>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>

    <div class="layui-form-item" pane="" id="serviceParent">
        <label class="layui-form-label">提供服务</label>
        <div class="layui-input-block">
            <button id="addService" type="button" class="layui-btn layui-btn-primary layui-btn-sm add_btn_margin"><i class="layui-icon"></i></button>
        </div>
        <?php if(is_array($org['service']) || $org['service'] instanceof \think\Collection || $org['service'] instanceof \think\Paginator): if( count($org['service'])==0 ) : echo "" ;else: foreach($org['service'] as $key=>$item): ?>
        <div class="layui-input-block margin-right service_margin_top">
            <input type="text" placeholder="服务" value="<?php echo $item['service_name']; ?>" class="layui-input service_name">
            <input type="text" placeholder="详细介绍" value="<?php echo $item['service_desc']; ?>" class="layui-input service_desc service_name_desc_margin">
            <button onclick="deleteService(this)" type="button" class="layui-btn layui-btn-primary layui-btn-sm btn_margin_left"><i class="layui-icon"></i></button>
        </div>
        <?php endforeach; endif; else: echo "" ;endif; ?>
    </div>

    <div class="layui-form-item">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="submit">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>



</form>

<script>
    // 全局，城市以及区域
    var cityDistrict = JSON.parse('<?php echo json_encode($city_district); ?>');
    var editor;
    var deanEditor;
    layui.use(['form','laydate','slider','jquery','upload'], function(){
        // 富文本
        loadEditor();
        loadDeanEditor();
        var form = layui.form,laydate = layui.laydate,$=layui.jquery,slider = layui.slider,upload=layui.upload;

        // 增加服务按钮事件
        $('#addService').on('click',function() {
            var serviceHtml = getService();
            $('#serviceParent').append(serviceHtml);
        })

        // 绑定短号
        $('#bind_short').on('click',function() {
            bindShort();
        })

        // 删除短号
        $('#delete_short').on('click',function() {
            deleteShort();
        })

        laydate.render({
            // elem: '#set_time'
        });

        // 城市区域联动
        form.on('select(city_id)', function(data){
            // 切换的城市id
            loadDistrictOptions(data.value);
        });

        //自定义验证规则
        form.verify(rule);

        // 上传图片
        upload.render({
            elem: '#upload_btn',
            url: '/admin/file/upload',
            done: function(res){
                parent.layer.msg(res.msg);
                if (res.code == 0) {
                    $('#dean_pic_preview').attr('src', res.data.url);
                    $('#dean_pic').val(res.data.url);
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(formData){
            var data = formData.field;
            if (parseInt(data.min_price) > parseInt(data.max_price)) {
                parent.layer.msg('价格错误');
                return false;
            }
            data.org_type= getCheckBoxValue('org_type');
            if (data.org_type.length < 1) {
                parent.layer.msg('请选择机构类型');
                return false;
            }
            data.target_person= getCheckBoxValue('target_person');
            if (data.target_person.length < 1) {
                parent.layer.msg('请选择收住对象');
                return false;
            }
            // if (!data.phone1 && !data.phone2) {
            //     parent.layer.msg('请至少输入一个电话号码');
            //     return false;
            // }
            data.service_scope= getCheckBoxValue('service_scope');
            data.equipment= getCheckBoxValue('equipment');
            data.health_insurance = formData.field.health_insurance ? 1 : 0;
            // data.is_hot = formData.field.is_hot ? 1 : 0;
            if (editor.txt.text() == '' && editor.txt.html() == '<p><br></p>') {
                data['comment'] = '';
            } else {
                data['comment'] = editor.txt.html();
            }

            if (!validateService()) {
                parent.layer.alert('请完善服务项');
                return false;
            }
            data = executeService(data);
            delete data.file;
            // 机构介绍
            if (deanEditor.txt.text() == '' && deanEditor.txt.html() == '<p><br></p>') {
                data['dean_content'] = '';
            } else {
                data['dean_content'] = deanEditor.txt.html();
            }
            // 拼装服务
            submit('/admin/org/update',data);
            return false;
        });
    });
</script>

</body>
</html>