<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/23
 * Time: 1:25 PM
 */

namespace app\model;


use think\Model;

class Photo extends Model
{
    CONST IS_COVER = 1;
    CONST NOT_COVER = 0;

    protected $autoWriteTimestamp = true;

    protected static function init() {
        Photo::event('before_insert',function ($photo) {
            if (empty($photo->is_cover)) {
                $photo->is_cover = self::NOT_COVER;
            }
        });
    }

    public function getPhotoListByOrgIds($orgIds = []) {
        return $this->where('org_id','in',$orgIds)->select()->toArray();
    }

    /**
     * 获取机构下所有图片
     * @param int $orgId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPhotoListByOrgId($orgId = 0) {
        return $this->where('org_id',$orgId)
            ->field('id,photo_type,is_cover,pic')->select()->toArray();
    }

    /**
     * 获取机构展示用的图
     * 优先展示封面图
     * 如果不存在封面图则取最新的图
     */
    public function getShowPicByOrgId($orgId = 0) {
        $pic = "";
        $photos = $this->getPhotoListByOrgId($orgId);
        if (empty($photos)) {
            return $pic;
        }
        foreach ($photos as $photo) {
            if (empty($pic)) {
                $pic = $photo['pic'];
            }
            if ($photo['is_cover']) {
                $pic = $photo['pic'];
                break;
            }
        }
        return $pic;
    }

    public function photoPageList($name = '',$page = 1,$limit = 10) {
        $where = [];
        if (!empty($name)) {
            $where['o.name'] = ['like',"%$name%"];
        }

        return $this->field('p.id,p.photo_type,p.is_cover,p.pic,o.name as org_name')
            ->alias('p')
            ->join('org o','o.id = p.org_id','LEFT')
            ->where($where)->order('p.id desc')
            ->paginate($limit,false,['page' => $page])->toArray();
    }

    /**
     * 将机构下目前is_cover值为 $cover的全部改掉
     * @param $orgId
     * @param $cover
     */
    public function changeOtherCover($orgId,$cover) {
        $newCover = $cover ? 0 : 1;
        return $this->save([
            'is_cover' => $newCover
        ],['org_id' => $orgId,'is_cover' => $cover]);
    }

    /**
     * 获取机构封面图
     * @param int $orgId
     * @return string
     */
    public function getCoverPhoto($orgId = 0)
    {
        $photo = $this->where([
            'org_id' => $orgId,
            'is_cover' => 1
        ])->find();
        $pic = !empty($photo) ? $photo->pic : "";

        return $pic;
    }
}