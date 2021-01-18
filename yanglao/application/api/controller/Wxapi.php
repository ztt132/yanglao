<?php
namespace app\api\controller;
use app\model\Weouth;
use think\Db;
class Wxapi
{
	public $Weouth;
	public $code;

	public function __construct()
	{
		$this->Weouth=new Weouth();
	}
	public function getopenid(){
		$this->code=input("param.code");

		if(!$this->code){
			return ['msg'=>"CODE不存在",'data'=>'','code'=>0];
			exit;
		}
		//城市列表 获取openid 同时返回城市列表 方便注册
		$result = $this->Weouth->get_sessionKey($this->code);

		if($result){
			$r = Db::name("userinfo")->where('openid',$result['openid'])->find();
			$return['openid']=$result['openid'];
			$return['phone']='';
			if($r){
				$return['phone']=$r['phone'];
				$row['code']=$this->code;
				$row['getkeytime']=time();
				$row['session_key']=$result['session_key'];
				$add_result=Db::name('userinfo')->where(array('openid'=>$result['openid']))->update($row);
			}else{
				$row['code']=$this->code;
				$row['getkeytime']=time();
				$row['openid']=$result['openid'];
				$row['session_key']=$result['session_key'];
				$row['dateline']=time();
				$add_result=Db::name('userinfo')->insert($row);
			}
			return ['msg'=>"ok",'data'=>$return,'code'=>1];
		}else{
			return ['msg'=>$this->Weouth->errMsg,'data'=>array(),'code'=>0];
			exit;
		}
	}
	public function getphone(){
        $openid = input("param.openid/s");
        $encryptedData=input("param.encryptedData/s");
        $iv=input("param.iv/s");

        $re = Db::name("userinfo")->where('openid',$openid)->find();
        $session_key=$re['session_key'];
        $result=$this->Weouth->check_data($session_key,$encryptedData,$iv);
        if($result){
            $passport_phone=$result['phoneNumber'];
            Db::name('userinfo')->where(array('openid'=>$openid))->update(array('phone'=>$passport_phone));
            return ['msg'=>"ok",'data'=>$passport_phone,'code'=>1];
        }else{
            return ['msg'=>$this->Weouth->errMsg,'data'=>array(),'code'=>0];
            exit;
        }
	}

}