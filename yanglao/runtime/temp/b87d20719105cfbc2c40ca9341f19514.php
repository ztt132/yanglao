<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"/database/webroot/yanglao/public/../application/admin/view/food/add.html";i:1605081560;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>新增助餐</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/layui/css/layui.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
    <script src="XADMIN_BASE_DIR/js/yanglao.js"></script>
    <script src="XADMIN_BASE_DIR/js/edit.js"></script>
    <script src="XADMIN_BASE_DIR/js/org.js"></script>
    <script charset="utf-8" src="https://map.qq.com/api/gljs?v=1.exp&key=MGVBZ-MTTKW-LB3RI-O5YDH-5QNVO-CVFNC"></script>
    <script src="XADMIN_BASE_DIR/js/qqmap.js"></script>
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/wangEditor/css/wangEditor.min.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/wangEditor/js/wangEditor.min.js"></script>
</head>
<body>
<style>
    .margin-right{margin-right: 110px}
    .org_tag{width: 32%;float:left;}
    .org_tag_margin_left{margin-left: 2%}

    .require{color: red;font-weight: bold;font-size: 14px}
    .pic_preview{max-width: 200px;margin-left: 110px;}
    .width100{width: 100px}
    .width80{width: 80px}
    #container {
        width: 100%;
        height: 100%;
    }
    .map_div{margin-left: 110px;margin-right: 110px;}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>添加助餐</legend>
</fieldset>

<form class="layui-form" action="">
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label"><span class="require">*</span>名称</label>
        <div class="layui-input-block margin-right">
            <input type="text" id="name" name="name" lay-verify="name" class="layui-input" placeholder="请输入名称">
        </div>
    </div>
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
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>社区</label>
        <div class="layui-input-block margin-right">
            <select name="community_id" lay-filter="community_id" id="community_id" lay-verify="community_id">
                <option value="" selected>请选择社区</option>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>地址</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="address" lay-verify="address" placeholder="请输入地址" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>面积</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="area" lay-verify="area" placeholder="请输入面积" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>营业时间</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="opening_hours" lay-verify="opening_hours" placeholder="请输入营业时间" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>提供餐饮</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="provide_food" lay-verify="provide_food" placeholder="请输入餐饮" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label"><span class="require">*</span>联系人</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="contacts" lay-verify="contacts" placeholder="请输入联系人" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">早餐</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="breakfast_time" lay-verify="breakfast_time" placeholder="营业时间" class="layui-input org_tag width100">
            <input type="text" name="breakfast_price" lay-verify="breakfast_time" placeholder="价格" class="layui-input org_tag width100 org_tag_margin_left">
            <input type="text" name="breakfast_sub" lay-verify="breakfast_sub" placeholder="补贴" class="layui-input org_tag width100 org_tag_margin_left">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">午餐</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="lunch_time" lay-verify="lunch_time" placeholder="营业时间" class="layui-input org_tag width100">
            <input type="text" name="lunch_price" lay-verify="lunch_price" placeholder="价格" class="layui-input org_tag width100 org_tag_margin_left">
            <input type="text" name="lunch_sub" lay-verify="lunch_sub" placeholder="补贴" class="layui-input org_tag width100 org_tag_margin_left">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">晚餐</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="dinner_time" lay-verify="dinner_time" placeholder="营业时间" class="layui-input org_tag width100">
            <input type="text" name="dinner_price" lay-verify="dinner_price" placeholder="价格" class="layui-input org_tag width100 org_tag_margin_left">
            <input type="text" name="dinner_sub" lay-verify="dinner_sub" placeholder="补贴" class="layui-input org_tag width100 org_tag_margin_left">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">资质</label>
        <div class="layui-input-block margin-right">
            <input type="text" name="natural" lay-verify="natural" placeholder="请输入资质" class="layui-input">
        </div>
    </div>

    <div class="layui-form-item" pane="">
        <label class="layui-form-label"><span class="require">*</span>400电话</label>
        <div class="layui-input-block margin-right">
            <input type="text" placeholder="区号,手机为0" id="prefix" name="prefix" lay-verify="prefix" class="layui-input org_tag width100">
            <input type="text" placeholder="请输入号码" id="phone2" name="phone2" lay-verify="phone2" class="layui-input org_tag org_tag_margin_left">
            <input type="text" id="short_tel" readonly name="short_tel"  class="layui-input org_tag width80 org_tag_margin_left">
            <button id="bind_short" type="button" class="layui-btn org_tag_margin_left">绑定短号</button>
            <button id="delete_short" type="button" class="layui-btn org_tag_margin_left">解绑短号</button>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">图片</label>
        <button type="button" class="layui-btn" id="upload_btn">上传</button>图片限制：200KB
        <div class="layui-upload-list">
            <input type="hidden" id="pic" lay-verify="pic" name="pic">
            <img class="pic_preview" id="pic_preview">
            <p id="pic_text"></p>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">位置</label>
        <input readonly type="text" placeholder="经度" id="lng" name="lng" lay-verify="lng" class="layui-input org_tag width100">
        <input readonly type="text" placeholder="纬度" id="lat" name="lat" lay-verify="lat" class="layui-input org_tag width100 org_tag_margin_left">
    </div>
    <div class="layui-form-item">
        <div class="map_div">
            <div id="container"></div>
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
    layui.use(['form','laydate','jquery','slider','upload'], function(){
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

        // 区域街道联动
        form.on('select(district_id)', function(data){
            console.log('change street:'+data.value);
            // 切换的区域
            loadStreetOptions(data.value);
        });

        // 街道社区联动
        form.on('select(street_id)', function(data){
            console.log('change street_id:'+data.value);
            // 切换社区
            loadCommunityOptions(data.value);
        });

        //自定义验证规则
        form.verify(food_rule);

        // 上传图片
        upload.render({
            elem: '#upload_btn',
            url: '/admin/file/upload',
            done: function(res){
                parent.layer.msg(res.msg);
                if (res.code == 0) {
                    $('#pic_preview').attr('src', res.data.url);
                    $('#pic').val(res.data.url);
                }
            }
        });

        //监听提交
        form.on('submit(submit)', function(formData){
            var data = formData.field;
            delete data.file;
            submit('/admin/food/create',data);
            return false;
        });
    });
</script>

<script type="text/javascript">
    initMap( 118.784047,32.041819);
</script>

</body>
</html>