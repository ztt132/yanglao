<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/30
 * Time: 8:23 AM
 */

namespace app\admin\controller;


use app\model\Filter as FilterModel;
use think\Config;
use think\Request;

class Filter extends AdminBase
{
    public $filterModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->filterModel = new FilterModel();
    }

    public function index() {
        return $this->fetch();
    }

    public function price() {
        $filter = $this->filterModel->select()->toArray();
        $data = [
            'id'    => empty($filter) ? 0 : $filter[0]['id'],
            'price' => empty($filter) ? "" : $filter[0]['price']
        ];
        $this->assign($data);

        return $this->fetch();
    }

    public function listFilter() {
        // 加载列表筛选项配置
        $config = Config::get('Org.filter_page');
        $filters = $this->filterModel->select()->toArray();
        // 处理配置数据
        if (!empty($filters)) {
            $listFilterValue = $filters[0]['list_filter'];
        }
        if (!empty($listFilterValue)) {
            $listFilterValue = array_column($listFilterValue,NULL,'key');
            foreach ($config as &$cv) {
                if (!empty($listFilterValue[$cv['value']])) {
                    $cv['checked'] = $listFilterValue[$cv['value']]['checked'];
                    $cv['sort'] = $listFilterValue[$cv['value']]['sort'];
                } else {
                    $cv['checked'] = 0;
                    $cv['sort'] = 0;
                }
            }
        } else {
            foreach ($config as $ck => &$cv) {
                $cv['checked'] = 0;
                $cv['sort'] = $ck + 1;
            }
        }

        $this->assign('config',$config);
        return $this->fetch();
    }

    public function quickFilter() {
        $config = get_filter_config();
        $filters = $this->filterModel->select()->toArray();
        $quickFilter = [];

        if (!empty($filters)) {
            $quickFilter = $filters[0]['quick_filter'];
            if (!empty($quickFilter)) {
                // 追加子分类选项，方便界面渲染
                foreach ($quickFilter as &$q) {
                    foreach ($config as $c) {
                        if ($q['key'] == $c['value']) {
                            $q['sub'] = $c['sub'];
                            $q['alias'] = !empty($q['alias']) ? $q['alias'] : '';
                        }
                    }
                }
            }
        }

        $data = [
            'config' => $config,
            'quick_filter' => $quickFilter,
            'sub_values' => json_encode(array_column($config,'sub','value')),// 方便前端切换分类时候，加载子分类
            'jsonValueConfig' => json_encode(array_column($config,'option','value')),
            'jsonSubValueConfig' => json_encode(array_column($config[0]['sub'],'option','value'))
        ];
        $this->assign($data);
        return $this->fetch();
    }

    public function update() {
        $this->isAjaxRequest();
        $data = $this->request->param();
        $type = input('type');
        if (!empty($data['id'])) {
            // 更新
            $id = $data['id'];
            unset($data['id']);
            $this->filterModel->update($data,['id' => $id]);
        } else {
            // 防止同一页面重新请求
            $filter = $this->filterModel->getFilter();
            if (empty($filter)) {
                // 新增                unset($data['type']);
                unset($data['type']);
                $this->filterModel->create($data);
            } else {
                $id = $filter['id'];
                $data = empty($data[$type]) ? [$type => []] : $data;
                if (array_key_exists('type',$data)) {
                    unset($data['type']);
                }
                $this->filterModel->update($data,['id' => $id]);
            }
        }
        return $this->jsonData('update_success',0);
    }
}