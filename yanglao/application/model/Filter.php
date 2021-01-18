<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/30
 * Time: 10:49 AM
 */

namespace app\model;

use think\Session;
use think\Model;

class Filter extends Model
{
    protected $type = [
        'list_filter'  => 'json',
        'quick_filter' => 'json',
    ];

    protected static function init()
    {
        // 追加用户id
        Filter::event('before_insert', function ($filter) {
            if (empty($filter->account_id)) {
                $account = Session::get('account_info');
                $filter->account_id = $account['id'];
            }
        });
        // 追加用户id
        Filter::event('before_update', function ($filter) {
            if (empty($filter->account_id)) {
                $account = Session::get('account_info');
                $filter->account_id = $account['id'];
            }
        });
    }

    public function getFilter() {
        $filters = $this->select()->toArray();
        if (empty($filters)) {
            return;
        }

        return $filters[0];
    }
}