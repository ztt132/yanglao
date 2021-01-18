<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/2
 * Time: 3:20 PM
 */
namespace app\api\controller;

use think\Request;
use app\model\Feedback as FeedbackModel;

class Feedback extends Baseuser
{
    public $feedbackModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->feedbackModel = new FeedbackModel();
    }

    /**
     * 新增意见反馈
     */
    public function create() {
        $content = input('post.content');
        if (empty($content)) {
            return ['code' => 0, 'data' => [], 'msg'  => '缺少参数'];
        }

        // 存入数据
        $data = [
            'content' => $content,
            'user_id' => $this->userinfo->id,
            'phone'   => $this->userinfo->phone
        ];
        if (!empty($this->feedbackModel->save($data))) {
            return ['code' => 1, 'data' => [], 'msg'  => 'success'];
        } else {
            return ['code' => 0,'data' => [],'msg'  => '请稍后再试'];
        }
    }

}