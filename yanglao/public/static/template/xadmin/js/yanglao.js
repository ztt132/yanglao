// 获取checkbox值
function getCheckBoxValue(layFilter) {
    var value = [];
    $("input[lay-filter="+layFilter+"]:checked").each(function() {
        value.push($(this).val());
    });

    return value;
}