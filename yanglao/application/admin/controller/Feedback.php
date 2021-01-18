<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/7
 * Time: 2:54 PM
 */

namespace app\admin\controller;

use app\model\Feedback as FeedbackModel;
use think\Request;

class Feedback extends AdminBase
{
    public $feedbackModel;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->feedbackModel = new FeedbackModel();
    }

    public function index() {
        $phone = $this->request->get('phone');
        $this->assign('phone',$phone);

        return $this->fetch();
    }

    public function feedbackList() {
        $this->isAjaxRequest();

        $phone = input('phone');
        list($page,$limit) = $this->getPaginateInfo();

        $data = $this->feedbackModel->feedbackPageList($phone,$page,$limit);
        return $this->jsonData('success',0,$data);
    }
}