<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/7/1
 * Time: 2:41 PM
 */

namespace app\api\controller;
use app\model\News as NewsModel;
use think\Request;

class News extends Base
{
    public $newsModel;
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->newsModel = new NewsModel();
    }

    /**
     * 资讯列表接口
     * @return array
     */
    public function index() {
        $type = input('type');
        if (empty($type)) {
            return apiReturn(10001);
        }
        $title = input('keyword');
        list($page,$limit) = $this->getPaginateInfo();
        $data = $this->newsModel->getNewsList($title,$type,$this->cityId,$page,$limit);
        $data['list'] = $data['data'];
        //对时间做处理
        if (!empty($data['list'])) {
            $data['list'] = array_map(function (&$v) {
                $v['create_time'] = $this->formatCreateTime($v['create_time']);
                return $v;
            },$data['list']);
        }
        unset($data['data']);
        return ['code' => 1, 'data' => $data, 'msg'  => 'success'];
    }

    /**
     * 资讯详情接口
     * @return array
     */
    public function detail() {
        $id = input('id');
        if (empty($id)) {
            return ['code' => 0, 'data' => [], 'msg'  => '缺少参数'];
        }
        $news = $this->newsModel->where('id',$id)
            ->field('id,publisher,title,content,create_time,city_id')->find();
        if (empty($news)) {
            return ['code' => 0, 'data' => [], 'msg'  => '对象不存在'];
        }
        $this->cityStatusValidate($news->city_id);
        $news = $news->toArray();
        $news['create_time'] = $this->formatCreateTime($news['create_time']);
        $news['content'] = content_img_add_class($news['content']);
        return ['code' => 1, 'data' => $news, 'msg'  => 'success'];
    }

    /**
     * 处理数据库创建时间，前端展示样式
     * @param string $date
     */
    private function formatCreateTime($date = '') {
        // 不足1天的显示小时  超过1天的展示发布日期 年月日
        $diffSeconds = time() - strtotime($date);
        if ($diffSeconds > 3600*24) {
            $newDate = show_date_format($date);
        } else {
            $hour = floor($diffSeconds/3600);
            $newDate = $hour > 0 ? $hour.'小时前' : '刚刚';
        }
        return $newDate;
    }
}