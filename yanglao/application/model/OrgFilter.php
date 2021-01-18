<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/30
 * Time: 3:40 PM
 */
namespace app\model;
use think\Model;

class OrgFilter extends Model
{
    public function getOrgFilterByOrgId($orgId) {
        return $this->where('org_id',$orgId)->select()->toArray();
    }

    public function updateFilterValue($filterData = [],$orgId = 0) {
        // 更新时，删除此orgid所有的数据
        $this->where('org_id',$orgId)->delete();
        // 组织数据
        $data = [];
        foreach ($filterData as $type => $v) {
            if (empty($v)) {
                continue;
            }
            foreach ($v as $value) {
                $data[] = [
                    'type'   => $type,
                    'org_id' => $orgId,
                    'value'  => $value
                ];
            }
        }
        if (empty($data)) {
            return true;
        }
        $this->saveAll($data);
    }

    /**
     * 根据orgid查询所有筛选值
     * @param int $orgId
     */
    public function findAllByOrgId($orgId = 0) {
        return $this->where('org_id',$orgId)->select()->toArray();
    }

    /**
     * 根据orgid查询所有筛选值
     * @param int $orgId
     */
    public function findAllByOrgIds($orgId = []) {
        return $this->where('org_id','in',$orgId)->select()->toArray();
    }
}