<?php
namespace app\api\controller;

use app\model\Userinfo;
use think\Controller;
use think\Request;
class Baseuser extends Controller
{
	public $userinfo;
	public $openid;
	public $userinfoModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->userinfoModel = new Userinfo();
		$this->openid = Input("param.openid/s");
		$re = $this->checkUser();
		if($re!==true or !$this->userinfo){
            $result=['msg'=>'未获取用户信息','data'=>'','code'=>0];
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit;
		}
		if (!$this->userinfo->phone) {
            $result=['msg'=>'用户未授权','data'=>'','code'=>0];
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function checkUser(){
        $openid = $this->openid;
        if(!$openid){
            return false;
        }
       	$r = $this->userinfoModel->where('openid',$openid)->find();
		if(!$r){
		   return false;
		}
        if($r){
			$this->userinfo=$r;
			return true;
        }

        return false;
    }


}