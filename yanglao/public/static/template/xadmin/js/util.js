// 验证是否为纯数字
function validateNumber(value)
{
    var preg = /^\d+$/;
    if (!preg.test(value)) {
        return false;
    }

    return true;
}