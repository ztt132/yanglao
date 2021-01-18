function loadEditor() {
    editor = new wangEditor('.editor');
    editor.customConfig.uploadImgMaxSize =  200 * 1024

    editor.customConfig.customAlert = function (info) {
        // info 是需要提示的内容
        alert('图片最大200KB')
    }

    editor.customConfig.colors = [
        '#000000',
        '#eeece0',
        '#1c487f',
        '#4d80bf',
        '#ff0000',
        '#8baa4a',
        '#7b5ba1',
        '#46acc8',
        '#f9963b',
        '#ffffff'
    ];
    editor.customConfig.uploadImgServer = '/admin/file/wangUpload'; // 上传图片到服务器
    editor.customConfig.pasteFilterStyle = true;
    editor.customConfig.menus = [
        'head',  // 标题
        'bold',  // 粗体
        'fontSize',  // 字号
        'fontName',  // 字体
        'italic',  // 斜体
        'underline',  // 下划线
        'strikeThrough',  // 删除线
        'foreColor',  // 文字颜色
        'backColor',  // 背景颜色
        'link',  // 插入链接
        // 'list',  // 列表
        'justify',  // 对齐方式
        // 'quote',  // 引用
        // 'emoticon',  // 表情
        'image'  // 插入图片
        // 'table',  // 表格
        // 'video',  // 插入视频
        // 'code',  // 插入代码
        // 'undo',  // 撤销
        // 'redo'  // 重复
    ];
    editor.customConfig.showLinkImg = false;

    editor.create();
}
// 临时复制，后期封装
function loadDeanEditor() {
    deanEditor = new wangEditor('.dean_editor');
    deanEditor.customConfig.colors = [
        '#000000',
        '#eeece0',
        '#1c487f',
        '#4d80bf',
        '#ff0000',
        '#8baa4a',
        '#7b5ba1',
        '#46acc8',
        '#f9963b',
        '#ffffff'
    ];
    deanEditor.customConfig.uploadImgServer = '/admin/file/wangUpload'; // 上传图片到服务器
    deanEditor.customConfig.pasteFilterStyle = true;
    deanEditor.customConfig.menus = [
        'head',  // 标题
        'bold',  // 粗体
        'fontSize',  // 字号
        'fontName',  // 字体
        'italic',  // 斜体
        'underline',  // 下划线
        'strikeThrough',  // 删除线
        'foreColor',  // 文字颜色
        'backColor',  // 背景颜色
        'justify',  // 对齐方式
        'image'  // 插入图片
    ];
    deanEditor.customConfig.showLinkImg = false;
    deanEditor.create();
}