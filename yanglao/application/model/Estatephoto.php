<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2021/1/11
 * Time: 5:55 PM
 */

namespace app\model;


use think\Model;

class Estatephoto extends Model
{
    protected $dateFormat = 'Y-m-d';

    protected $autoWriteTimestamp = true;

    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    public function photoPageList($name = '',$page = 1,$limit = 10) {
        $where = [];
        if (!empty($name)) {
            $where['e.name'] = ['like',"%$name%"];
        }

        return $this->field('p.id,p.photo_type,p.is_cover,p.pic,e.name as estate_name')
            ->alias('p')
            ->join('estate e','e.id = p.estate_id','LEFT')
            ->where($where)->order('p.id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }

    /**
     * 将地产下目前is_cover值为 $cover的全部改掉
     * @param $estateId
     * @param $cover
     */
    public function changeOtherCover($estateId,$cover) {
        $newCover = $cover ? 0 : 1;
        return $this->save([
            'is_cover' => $newCover
        ],['estate_id' => $estateId,'is_cover' => $cover]);
    }

    public function getPhotoListByEstateIds($estateIds = []) {
        return $this->where('estate_id','in',$estateIds)->select()->toArray();
    }

    public function getPhotoListByEstateId($id = 0) {
        return $this->where('estate_id',$id)
            ->field('id,photo_type,is_cover,pic')->select()->toArray();
    }
}