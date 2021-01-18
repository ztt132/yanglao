<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/23
 * Time: 10:14 AM
 */

namespace app\admin\controller;

class File extends AdminBase
{
    CONST BASE_UPLOAD_DIR = '/static/upload/image';

    CONST MAX_SIZE = 204800;//图片最大 200KB

    /**
     * 上传图片
     */
    public function upload() {
        if (empty($_FILES['file'])) {
            return returnDataFormat('upload_error');
        }
        $file = $_FILES['file'];
        if ($file['size'] > SELF::MAX_SIZE) {
            return json(returnDataFormat('image_size_error'));
        }
        // 后缀
        $extension = get_file_extension($file['name']);
        // 目录
        $dir = make_file_dir(SELF::BASE_UPLOAD_DIR);
        // 生成文件名
        $fileName = make_file_name($extension);
        $newFile = $dir . '/' . $fileName;
        $result = move_uploaded_file($_FILES["file"]["tmp_name"],$newFile);
        // 返回url
        if ($result) {
            return json(returnDataFormat('upload_success',0,[
                'url' => get_protocol().'://'.get_server_name().SELF::BASE_UPLOAD_DIR . '/' . date('Ymd',time()) . '/' . $fileName
            ]));
        } else {
            return json(returnDataFormat('upload_error'));
        }
    }

    /**
     * wangEditor上传图片
     * @return \think\response\Json
     */
    public function wangUpload() {
        $pics = [];
        foreach ($_FILES as $k => $file) {
            // 后缀
            $extension = get_file_extension($file['name']);
            // 目录
            $dir = make_file_dir(SELF::BASE_UPLOAD_DIR);
            // 生成文件名
            $fileName = make_file_name($extension);
            $newFile = $dir . '/' . $fileName;
            move_uploaded_file($file["tmp_name"],$newFile);
            // wangEditor返回绝对路径
            $pics[] = get_protocol().'://'.get_server_name().SELF::BASE_UPLOAD_DIR . '/' . date('Ymd',time()) . '/' . $fileName;
        }
        // 返回url
        return json([
            'data' => $pics,
            'errno' => 0
        ]);
    }
}