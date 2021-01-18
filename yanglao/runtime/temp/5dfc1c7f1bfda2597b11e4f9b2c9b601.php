<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:75:"/database/webroot/yanglao/public/../application/admin/view/index/index.html";i:1596618343;}*/ ?>
<!doctype html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>养老小程序</title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/font.css">
    <link rel="stylesheet" href="XADMIN_BASE_DIR/css/xadmin.css">
    <!-- <link rel="stylesheet" href="./css/theme5.css"> -->
    <script src="XADMIN_BASE_DIR/lib/layui/layui.js" charset="utf-8"></script>
    <script src="XADMIN_BASE_DIR/js/xadmin.js"></script>
    <script src="XADMIN_BASE_DIR/js/jquery.min.js"></script>
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
        // 是否开启刷新记忆tab功能
        // var is_remember = false;
    </script>
</head>
<body class="index">
<!-- 顶部开始 -->
<div class="container">
    <div class="logo">
        <a href="./index.html">养老小程序</a></div>
    <div class="left_open">
        <a><i title="展开左侧栏" class="iconfont">&#xe699;</i></a>
    </div>
    <ul class="layui-nav right" lay-filter="">
        <li class="layui-nav-item">
            <a href="javascript:;"><?php echo $account['user_name']; ?></a>
            <dl class="layui-nav-child">
                <!-- 二级菜单 -->
                <!--<dd><a onclick="xadmin.open('个人信息','http://www.baidu.com')">个人信息</a></dd>-->
                <dd><a href="/admin/login/loginOut">退出</a></dd>
            </dl>
        </li>
        <li class="layui-nav-item to-index">
            <a href="/">首页</a></li>
    </ul>
</div>
<!-- 顶部结束 -->
<!-- 中部开始 -->
<!-- 左侧菜单开始 -->
<div class="left-nav">
    <div id="side-nav">
        <ul id="nav">
            <?php if(is_array($menus) || $menus instanceof \think\Collection || $menus instanceof \think\Paginator): if( count($menus)==0 ) : echo "" ;else: foreach($menus as $key=>$menu): ?>
                <li>
                    <a href="javascript:;">
                        <i class="iconfont left-nav-li" lay-tips="<?php echo $menu['name']; ?>">&#xe6b8;</i>
                        <cite><?php echo $menu['name']; ?></cite>
                        <i class="iconfont nav_right">&#xe697;</i>
                    </a>
                    <ul class="sub-menu">
                        <?php if(is_array($menu['sub_menu']) || $menu['sub_menu'] instanceof \think\Collection || $menu['sub_menu'] instanceof \think\Paginator): if( count($menu['sub_menu'])==0 ) : echo "" ;else: foreach($menu['sub_menu'] as $key=>$sub): if($sub['is_display'] == 1): ?>
                                <li>
                                    <a onclick="xadmin.add_tab('<?php echo $sub['name']; ?>','<?php echo $sub['action']; ?>')">
                                        <i class="iconfont">&#xe6a7;</i>
                                        <cite><?php echo $sub['name']; ?></cite></a>
                                </li>
                            <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
</div>
<!-- <div class="x-slide_left"></div> -->
<!-- 左侧菜单结束 -->
<!-- 右侧主体开始 -->
<div class="page-content">
    <div class="layui-tab tab" lay-filter="xbs_tab" lay-allowclose="true">
        <ul class="layui-tab-title">
            <li class="home" style="display: none;">
                <i class="layui-icon">&#xe68e;</i>城市管理
            </li>
        </ul>
        <div class="layui-unselect layui-form-select layui-form-selected" id="tab_right">
            <dl>
                <dd data-type="this">关闭当前</dd>
                <dd data-type="other">关闭其它</dd>
                <dd data-type="all">关闭全部</dd></dl>
        </div>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <iframe src='/admin/city' frameborder="0" scrolling="yes" class="x-iframe"></iframe>
            </div>
        </div>
        <div id="tab_show"></div>
    </div>
</div>
<div class="page-content-bg"></div>
<style id="theme_style"></style>
<!-- 右侧主体结束 -->
<!-- 中部结束 -->
</body>
</html>