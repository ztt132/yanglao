<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"/database/webroot/yanglao/public/../application/admin/view/filter/quickfilter.html";i:1598857567;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑列表页快捷筛选</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/layui/css/layui.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
</head>
<body>
<style>
    .div_style{float: left;width: 22%;}
    .div_operation_style{float: left;width: 40px}
    .margin-left{margin-left: 1%}
    .i_w{width:40px;height:35px;line-height:1.3;border-width:1px;
        border-style:solid;border-radius:2px;background-color:#fff;border-color:#D2D2D2!important;text-align: center}
    .btn_margin_left{margin-left:4px;margin-top:4px}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>编辑列表页快捷筛选</legend>
</fieldset>

<form class="layui-form" action="">
    <div class="layui-form-item" pane="">
        <label class="layui-form-label">快捷筛选项</label>
        <div class="layui-input-block">
            <button id="addFilter" type="button" class="layui-btn layui-btn-primary layui-btn-sm add_btn_margin"><i class="layui-icon"></i></button>
        </div>
    </div>
    <div id="filter_div">
        <?php if(is_array($quick_filter) || $quick_filter instanceof \think\Collection || $quick_filter instanceof \think\Paginator): if( count($quick_filter)==0 ) : echo "" ;else: foreach($quick_filter as $key=>$item): ?>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="div_style">
                <select name="value" id="value" lay-filter="value">
                    <?php if(is_array($config) || $config instanceof \think\Collection || $config instanceof \think\Paginator): if( count($config)==0 ) : echo "" ;else: foreach($config as $key=>$c): ?>
                    <option value="<?php echo $c['value']; ?>" <?php if($c['value']==$item['key']): ?>selected<?php endif; ?>><?php echo $c['option']; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
            <div class="div_style margin-left">
                <select name="sub_value" id="sub_value" lay-filter="sub_value">
                    <?php if(is_array($item['sub']) || $item['sub'] instanceof \think\Collection || $item['sub'] instanceof \think\Paginator): if( count($item['sub'])==0 ) : echo "" ;else: foreach($item['sub'] as $key=>$s): ?>
                    <option value="<?php echo $s['value']; ?>" <?php if($item['value'] == $s['value']): ?>selected<?php endif; ?>><?php echo $s['option']; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
            <div class="div_operation_style margin-left">
                <input type="text" id="sort" value="<?php echo $item['sort']; ?>" class="i_w">
            </div>
            <div class="div_operation_style margin-left">
                <input type="text" id="alias" value="<?php echo $item['alias']; ?>" class="i_w">
            </div>
            <div class="div_operation_style margin-left">
                <button onclick="deleteFilter(this)" type="button" class="layui-btn layui-btn-primary layui-btn-sm btn_margin_left"><i class="layui-icon"></i></button>
            </div>
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
    // value=>subvalue
    var subValuesArr = JSON.parse('<?php echo $sub_values; ?>');
    // 默认分类
    var defaultSelect = JSON.parse('<?php echo $jsonValueConfig; ?>');
    var defaultSubSelect = JSON.parse('<?php echo $jsonSubValueConfig; ?>');
    function deleteFilter(obj) {
        $(obj).parent().parent().remove();
    }

    layui.use(['form','jquery'], function(){
        var form = layui.form;

        // 增加服务按钮事件
        $('#addFilter').on('click',function() {
            // 最多5个
            var l = $('#filter_div').find('.layui-form-item').length;
            if (l > 4) {
                parent.layer.alert('最多5个');
                return false;
            }

            var valueOptions = "";
            var subValueOptions = "";
            $.each(defaultSelect,function(index,item) {
                valueOptions += "<option value='"+index+"'>" + item + "</option>";
            })
            $.each(defaultSubSelect,function(index,item) {
                subValueOptions += "<option value='"+index+"'>" + item + "</option>";
            })

            var div = "<div class='layui-form-item'>" +
                "    <label class='layui-form-label'></label>" +
                "    <div class='div_style'>" +
                "        <select name='value' id='value' lay-filter='value' t='1'>" + valueOptions +
                "        </select>" +
                "    </div>" +
                "    <div class='div_style margin-left'>" +
                "        <select name='sub_value' id='sub_value' lay-filter='sub_value'>" + subValueOptions +
                "        </select>" +
                "    </div>" +
                "    <div class='div_operation_style margin-left'>" +
                "        <input type='text' id='sort' value='1' class='i_w'>" +
                "    </div>" +
                "    <div class='div_operation_style margin-left'>" +
                "        <input type='text' id='alias' class='i_w'>" +
                "    </div>" +
                "    <div class='div_operation_style margin-left'>" +
                "        <button onclick='deleteFilter(this)' type='button' class='layui-btn layui-btn-primary layui-btn-sm btn_margin_left'><i class='layui-icon'></i></button>" +
                "    </div>" +
            "</div>";
            $('#filter_div').append(div);
            // 重新reload
            form.render();
        })

        // 一级分类切换时，对2级分类的处理
        form.on('select(value)', function(data){
            // 一级分类切换时，2级重新加载
            var value = data.value;
            var subSelect = $(data.elem).parent().parent().find('#sub_value');
            subSelect.empty();
            // 根据value获取对应sub_value
            var subValues = subValuesArr[value];
            $.each(subValues,function(index,item) {
                subSelect.append(new Option(item.option, item.value));
            })
            layui.form.render("select");
        });

        //监听提交
        form.on('submit(submit)', function(formData){
            // 处理提交数据
            var data = [];
            var preg = /^\d+$/;
            var ret = true;
            $('#filter_div').find('.layui-form-item').each(function () {
                var item = {};
                // 获取
                item.key = $(this).find('#value').val();
                item.value = $(this).find('#sub_value').val();
                var sort = $(this).find('#sort').val();
                if (!preg.test(sort)) {
                    ret = false;
                }
                item.sort = sort;
                item.alias = $(this).find('#alias').val();
                data.push(item);
            })
            if (!ret) {
                parent.layer.alert('排序请输入正整数');
                return false;
            }
            $.ajax({
                url:'/admin/filter/update',
                dataType:'json',
                type:'post',
                data:{
                    quick_filter:data,
                    type:'quick_filter'
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