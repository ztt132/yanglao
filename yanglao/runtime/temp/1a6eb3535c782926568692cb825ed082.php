<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"/database/webroot/yanglao/public/../application/admin/view/filter/listfilter.html";i:1595397908;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑列表筛选项</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/lib/layui/css/layui.css"  media="all">
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
</head>
<body>
<style>
    .margin-right{margin-right: 10px;float:left;margint-left:10px;min-height:36px}
    .i_w{width:40px;height:38px;line-height:1.3;border-width:1px;
        border-style:solid;border-radius:2px;background-color:#fff;border-color:#D2D2D2!important;text-align: center}
</style>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>编辑列表筛选项</legend>
</fieldset>

<form class="layui-form" action="">
    <?php if(is_array($config) || $config instanceof \think\Collection || $config instanceof \think\Paginator): if( count($config)==0 ) : echo "" ;else: foreach($config as $key=>$item): ?>
        <div class="layui-form-item filter_div" pane="">
            <label class="layui-form-label"><?php echo $item['option']; ?></label>
            <div class="margin-right value_div">
                <input id="<?php echo $item['value']; ?>" type="checkbox" name="<?php echo $item['value']; ?>" lay-skin="switch" <?php if($item['checked']): ?>checked<?php endif; ?>>
            </div>
            <div class="margin-right sort_div">
                <input name="sort" id="sort" type="text" class="i_w" value="<?php echo $item['sort']; ?>">
            </div>
        </div>
    <?php endforeach; endif; else: echo "" ;endif; ?>
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

        //监听提交
        form.on('submit(submit)', function(formData){
            // 处理提交数据
            var data = [];
            var preg = /^\d+$/;
            var ret = true;
            $('.layui-form').find('.filter_div').each(function () {
                var item = {};
                // 获取
                item.key = $(this).find('.value_div').find('input').attr('id');
                item.checked = $(this).find('.value_div').find('input').is(':checked') == true ? 1 :0;
                var sort = $(this).find('.sort_div').find('input').val();
                if (!preg.test(sort)) {
                    // parent.layer.alert('请输入正整数');
                    // return false;
                    ret = false;
                }
                item.sort = sort;
                data.push(item);
            })
            if (!ret) {
                parent.layer.alert('请输入正整数');
                return false;
            }
            $.ajax({
                url:'/admin/filter/update',
                dataType:'json',
                type:'post',
                data: {
                    list_filter:data,
                    type:'list_filter'
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