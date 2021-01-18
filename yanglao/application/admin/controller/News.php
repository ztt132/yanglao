<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/6/28
 * Time: 10:03 AM
 */
namespace app\admin\controller;
use app\model\City;
use app\model\News as NewsModel;
use think\Config;
use think\Request;

class News extends AdminBase
{
    public $cityModel;
    public $newsModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->cityModel = new City();
        $this->newsModel = new NewsModel();
    }

    public function index() {
        $title = $this->request->get('title');
        $this->assign('title',$title);

        return $this->fetch();
    }

    public function add() {
        $config = Config::get('Enum.news');
        // 获取所有城市
        $citys = $this->cityModel->allCitys();
        $data = [
            'config' => $config,
            'citys'  => array_merge($config['more_city'],$citys)
        ];

        $this->assign($data);
        return $this->fetch();
    }

    public function edit() {
        $id = input('id');
        $news = $this->newsModel->find($id);
        $config = Config::get('Enum.news');
        // 获取所有城市
        $citys = $this->cityModel->allCitys();
        $data = [
            'config' => $config,
            'citys'  => array_merge($config['more_city'],$citys),
            'news'   => $news
        ];

        $this->assign($data);
        return $this->fetch();
    }

    public function create() {
        $this->isAjaxRequest();

        $data = $this->request->param();
        $this->dataValidate($data,'news.create');

        $ret = $this->newsModel->save($data);
        if(empty($ret)) {
            return $this->jsonData('create_error');
        }
        return $this->jsonData('create_success',0);
    }

    public function update() {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        // 数据验证
        $data = $this->request->param();
        $this->dataValidate($data,'news.update');

        $id = $data['id'];
        unset($data['id']);
        $ret = $this->newsModel->update($data,['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }

    public function newsList() {
        $this->isAjaxRequest();

        $title = input('title');
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->newsModel->newsPageList($title,$page,$limit);
        if (!empty($data['data'])) {
            // 转换类型
            $config = array_get(Config::get('Enum.news'),'type');
            foreach ($data['data'] as &$v) {
                $v['type'] = $config[$v['type']];
                if ($v['city_id'] == 0) {
                    $v['city'] = '全国';
                }
            }
        }
        return $this->jsonData('',0,$data);
    }

    public function delete() {
        // 验证是否为ajax请求
        $this->isAjaxRequest();
        // 数据验证
        $id = $this->request->param('id');
        $ret = $this->newsModel->update(['status' => 0],['id' => $id]);
        if(empty($ret)) {
            return $this->jsonData('update_error');
        }
        return $this->jsonData('update_success',0);
    }
}