<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"/database/webroot/yanglao/public/../application/admin/view/estate/edit.html";i:1610535599;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑养老地产</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/layui/css/layui.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
    <script src="XADMIN_BASE_DIR/js/org.js"></script>
    <script src="XADMIN_BASE_DIR/js/estate.js"></script>
    <script src="XADMIN_BASE_DIR/js/yanglao.js"></script>
    <script src="XADMIN_BASE_DIR/js/edit.js"></script>
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/wangEditor/css/wangEditor.min.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/wangEditor/js/wangEditor.min.js"></script>
    <script charset="utf-8" src="https://map.qq.com/api/gljs?v=1.exp&key=MGVBZ-MTTKW-LB3RI-O5YDH-5QNVO-CVFNC"></script>
    <script src="XADMIN_BASE_DIR/js/qqmap.js"></script>
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
    .medical_name{width:20%;float:left;}
    .medical_desc{width:60%;float:left;}
    .service_name_desc_margin{margin-left:4px }
    .service_margin_top{margin-top: 10px}
    .add_btn_margin{margin-top: 4px}
    .btn_margin_left{margin-left:4px;margin-top:4px}
    .demo-slider{width: 100%;display: inline-block;vertical-align: middle;margin-top: 15px}
    .require{color: red;font-weight: bold;font-size: 14px}
    .pic_preview{max-width: 200px;margin-left: 110px;}
    .width80{width: 80px}
    .width100{width: 100px}
    .map_div{margin-left: 110px;margin-right: 110px;}
    #container {
        width: 100%;
        height: 100%;
    }
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>编辑养老地产</legend>
</fieldset>


<form class="layui-form" action="">
    <input type="hidden" value="<?php echo $estate['id']; ?>" name="id" id="id">
    <div class="layui-tab" lay-filter="tab">
        <ul class="layui-tab-title">
            <li class="layui-this" lay-id="basic">基本信息</li>
            <li lay-id="operation">运营信息</li>
            <li lay-id="assort">配套信息</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="require">*</span>城市</label>
                    <div class="layui-input-block margin-right">
                        <select name="city_id" lay-filter="city_id" id="city_id" lay-verify="city_id">
                            <option value="" selected>请选择城市</option>
                            <?php if(is_array($city_district) || $city_district instanceof \think\Collection || $city_district instanceof \think\Paginator): if( count($city_district)==0 ) : echo "" ;else: foreach($city_district as $key=>$item): ?>
                            <option value="<?php echo $key; ?>" <?php if($key == $estate['city_id']): ?>selected<?php endif; ?>><?php echo $item['city_name']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="require">*</span>区域</label>
                    <div class="layui-input-block margin-right">
                        <select name="district_id" lay-filter="district_id" id="district_id" lay-verify="district_id">
                            <option value="">请选择区域</option>
                            <?php if(is_array($city_district[$estate['city_id']]['districts']) || $city_district[$estate['city_id']]['districts'] instanceof \think\Collection || $city_district[$estate['city_id']]['districts'] instanceof \think\Paginator): if( count($city_district[$estate['city_id']]['districts'])==0 ) : echo "" ;else: foreach($city_district[$estate['city_id']]['districts'] as $key=>$item): ?>
                            <option value="<?php echo $item['district_id']; ?>" <?php if($item['district_id'] == $estate['district_id']): ?>selected<?php endif; ?>><?php echo $item['district_name']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                </div>

                <!--遍历添加表单元素-->
                <?php if(is_array($form['basic']) || $form['basic'] instanceof \think\Collection || $form['basic'] instanceof \think\Paginator): if( count($form['basic'])==0 ) : echo "" ;else: foreach($form['basic'] as $key=>$item): if($item['type'] == 'text'): ?>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label"><?php if($item['require'] == 1): ?><span class="require">*</span><?php endif; ?><?php echo $item['name']; ?></label>
                    <div class="layui-input-block margin-right">
                        <input type="text" id="<?php echo $item['key']; ?>" name="<?php echo $item['key']; ?>" lay-verify="<?php echo $item['key']; ?>" class="layui-input" value="<?php echo $estate[$item['key']]; ?>">
                    </div>
                </div>
                <?php else: ?>
                <div class="layui-form-item">
                    <label class="layui-form-label"><?php echo $item['name']; ?></label>
                    <div class="layui-input-block margin-right">
                        <select name="<?php echo $item['key']; ?>" lay-filter="<?php echo $item['key']; ?>" lay-verify="<?php echo $item['key']; ?>">
                            <?php if(is_array($enum[$item['key']]) || $enum[$item['key']] instanceof \think\Collection || $enum[$item['key']] instanceof \think\Paginator): if( count($enum[$item['key']])==0 ) : echo "" ;else: foreach($enum[$item['key']] as $key=>$i): ?>
                            <option value="<?php echo $key; ?>" <?php if($key == $estate[$item['key']]): ?>selected<?php endif; ?>><?php echo $i; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                </div>
                <?php endif; endforeach; endif; else: echo "" ;endif; ?>

                <div class="layui-form-item">
                    <label class="layui-form-label">位置</label>
                    <input readonly type="text" placeholder="经度" id="lng" name="lng" lay-verify="lng" class="layui-input org_tag width100" value="<?php echo $estate['lng']; ?>">
                    <input readonly type="text" placeholder="纬度" id="lat" name="lat" lay-verify="lat" class="layui-input org_tag width100 org_tag_margin_left" value="<?php echo $estate['lat']; ?>">
                </div>
                <div class="layui-form-item">
                    <div class="map_div">
                        <div id="container"></div>
                    </div>
                </div>
            </div>
            <div class="layui-tab-item">
                <!-- 运营 -->
                <div class="layui-form-item" pane="">
                    <label class="layui-form-label">400电话</label>
                    <div class="layui-input-block margin-right">
                        <input type="text" placeholder="区号,手机为0" id="prefix" name="prefix" lay-verify="prefix" class="layui-input org_tag width100" value="<?php echo $estate['prefix']; ?>">
                        <input type="text" placeholder="请输入号码" id="phone" name="phone" class="layui-input org_tag org_tag_margin_left" value="<?php echo $estate['phone']; ?>">
                        <input type="text" id="short_tel" readonly name="short_tel"  class="layui-input org_tag width80 org_tag_margin_left" value="<?php echo $estate['short_tel']; ?>">
                        <button id="bind_short" type="button" class="layui-btn org_tag_margin_left">绑定短号</button>
                        <button id="delete_short" type="button" class="layui-btn org_tag_margin_left">解绑短号</button>
                    </div>
                </div>
                <?php if(is_array($form['operation']) || $form['operation'] instanceof \think\Collection || $form['operation'] instanceof \think\Paginator): if( count($form['operation'])==0 ) : echo "" ;else: foreach($form['operation'] as $key=>$item): ?>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label"><?php if($item['require'] == 1): ?><span class="require">*</span><?php endif; ?><?php echo $item['name']; ?></label>
                    <div class="layui-input-block margin-right">
                        <input type="text" id="<?php echo $item['key']; ?>" name="<?php echo $item['key']; ?>" lay-verify="<?php echo $item['key']; ?>" class="layui-input" value="<?php echo $estate[$item['key']]; ?>">
                    </div>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>

                <div class="layui-form-item">
                    <label class="layui-form-label">费用类型</label>
                    <div class="layui-input-block margin-right">
                        <textarea placeholder="请输入内容" class="layui-textarea" name="cost_type" id="cost_type" lay-filter="cost_type" lay-verify="cost_type"><?php echo $estate['cost_type']; ?></textarea>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">项目介绍</label>
                    <input type="hidden" name="desc" id="desc" lay-filter="desc" lay-verify="desc">
                    <div class="layui-input-block" style="z-index:0">
                        <div class="editor"><?php echo $estate['desc']; ?></div>
                    </div>
                </div>
                <!-- 运营 end -->
            </div>
            <div class="layui-tab-item">
                <!--配套信息 -->
                <?php if(is_array($form['assort']) || $form['assort'] instanceof \think\Collection || $form['assort'] instanceof \think\Paginator): if( count($form['assort'])==0 ) : echo "" ;else: foreach($form['assort'] as $key=>$item): ?>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label"><?php if($item['require'] == 1): ?><span class="require">*</span><?php endif; ?><?php echo $item['name']; ?></label>
                    <div class="layui-input-block margin-right">
                        <input type="text" id="<?php echo $item['key']; ?>" name="<?php echo $item['key']; ?>" lay-verify="<?php echo $item['key']; ?>" class="layui-input" value="<?php echo $estate[$item['key']]; ?>">
                    </div>
                </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>

                <div class="layui-form-item" pane="">
                    <label class="layui-form-label">设施设备</label>
                    <div class="layui-input-block">
                        <?php if(is_array($equipments) || $equipments instanceof \think\Collection || $equipments instanceof \think\Paginator): if( count($equipments)==0 ) : echo "" ;else: foreach($equipments as $key=>$e): ?>
                        <input type="checkbox"  title="<?php echo $e['name']; ?>" lay-filter="equipment" value="<?php echo $e['id']; ?>" <?php if(in_array($e['id'],$estate['equipment'])): ?>checked<?php endif; ?>>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </div>
                </div>
                <div class="layui-form-item" pane="" id="serviceParent">
                    <label class="layui-form-label">提供服务</label>
                    <div class="layui-input-block">
                        <button id="addService" type="button" class="layui-btn layui-btn-primary layui-btn-sm add_btn_margin"><i class="layui-icon"></i></button>
                    </div>
                    <?php if(is_array($estate['service']) || $estate['service'] instanceof \think\Collection || $estate['service'] instanceof \think\Paginator): if( count($estate['service'])==0 ) : echo "" ;else: foreach($estate['service'] as $key=>$item): ?>
                    <div class="layui-input-block margin-right service_margin_top">
                        <input type="text" placeholder="服务" value="<?php echo $item['service_name']; ?>" class="layui-input service_name">
                        <input type="text" placeholder="详细介绍" value="<?php echo $item['service_desc']; ?>" class="layui-input service_desc service_name_desc_margin">
                        <button onclick="deleteService(this)" type="button" class="layui-btn layui-btn-primary layui-btn-sm btn_margin_left"><i class="layui-icon"></i></button>
                    </div>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
                <div class="layui-form-item" pane="" id="medicalParent">
                    <label class="layui-form-label">医疗</label>
                    <div class="layui-input-block">
                        <button id="addMedical" type="button" class="layui-btn layui-btn-primary layui-btn-sm add_btn_margin"><i class="layui-icon"></i></button>
                    </div>
                    <?php if(is_array($estate['medical']) || $estate['medical'] instanceof \think\Collection || $estate['medical'] instanceof \think\Paginator): if( count($estate['medical'])==0 ) : echo "" ;else: foreach($estate['medical'] as $key=>$item): ?>
                    <div class="layui-input-block margin-right service_margin_top">
                        <input type="text" placeholder="医疗" value="<?php echo $item['medical_name']; ?>" class="layui-input medical_name">
                        <input type="text" placeholder="详细介绍" value="<?php echo $item['medical_desc']; ?>" class="layui-input medical_desc service_name_desc_margin">
                        <button onclick="deleteService(this)" type="button" class="layui-btn layui-btn-primary layui-btn-sm btn_margin_left"><i class="layui-icon"></i></button>
                    </div>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
                <!--配套信息end -->
            </div>
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
    var changeTab;
    var editor;
    // 加载所有城市以及区域
    var cityDistrict = JSON.parse('<?php echo json_encode($city_district); ?>');
    layui.use(['form','laydate','jquery','slider','upload','element'], function(){
        var form = layui.form,laydate = layui.laydate,$=layui.jquery,
            slider = layui.slider,upload=layui.upload,element=layui.element;
        loadEditor();
        // 切换tab
        changeTab = function (v) {
            element.tabChange('tab', v);
        }


        // 城市区域联动
        form.on('select(city_id)', function(data){
            // 切换的城市id
            loadDistrictOptions(data.value);
        });

        // 基本校验
        form.verify(estate_rule);

        //监听提交
        form.on('submit(submit)', function(formData){
            var data = formData.field;
            // 设备
            data.equipment= getCheckBoxValue('equipment');
            // 处理服务以及医疗
            data = executeEstateService(data);
            data = executeMedical(data);
            // 介绍
            if (editor.txt.text() == '' && editor.txt.html() == '<p><br></p>') {
                data['desc'] = '';
            } else {
                data['desc'] = editor.txt.html();
            }
            submit('/admin/estate/update',data);
            return false;
        })

        // 绑定短号
        $('#bind_short').on('click',function() {
            bindEstateShort();
        })

        // 删除短号
        $('#delete_short').on('click',function() {
            deleteEstateShort();
        })

        // 增加服务按钮以及医疗按钮事件
        $('#addService').on('click',function() {
            var serviceHtml = getServiceHtml();
            $('#serviceParent').append(serviceHtml);
        })

        $('#addMedical').on('click',function() {
            var medicalHtml = getMedicalHtml();
            $('#medicalParent').append(medicalHtml);
        })
    })
</script>

<script type="text/javascript">
    var lng = $('#lng').val() ? $('#lng').val() : 118.784047;
    var lat = $('#lat').val() ? $('#lat').val() : 32.041819;
    initMap(lng,lat);
    if ($('#lng').val() && $('#lat').val()) {
        createMarker(lng,lat);
    }
</script>

</body>
</html>