<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2021/1/11
 * Time: 4:39 PM
 */

namespace app\model;


use think\Model;

class Estatenews extends Model
{
    protected $dateFormat = 'Y-m-d';

    protected $autoWriteTimestamp = true;

    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    public function newsPageList($name = '',$page = 1,$limit = 10)
    {
        $where = ['en.status' => 1];
        if (!empty($name)) {
            $where['e.name'] = ['like',"%$name%"];
        }

        return $this->field('en.id,c.city_name,e.name,en.publish_time,en.content')
            ->alias('en')
            ->join('estate e','e.id = en.estate_id','LEFT')
            ->join('city c','c.id = e.city_id','LEFT')
            ->where($where)->order('en.id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }

    public function getNewsByEstateId($estateId = 0)
    {
        return $this->where('estate_id',$estateId)->where('status',1)
            ->field('publish_time,content')->order('publish_time desc')->select()->toArray();
    }
}