<?php
namespace app\api\controller;

class User extends Baseuser
{
	public $thismodel;
    public function __construct(){
        parent::__construct();

    }
    public function index()
    {
		return ['msg'=>'ok','data'=>array(),'status'=>1];

    }

}