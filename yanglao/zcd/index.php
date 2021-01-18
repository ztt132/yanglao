<?php
/**
 * Created by PhpStorm.
 * User: cesc
 * Date: 2020/12/15
 * Time: 9:43 AM
 */
include_once "init.php";

$token = $_GET['token'];
$index = new Index($token);
$index->run();

class Index
{
    private $db;
    public function __construct($token = '')
    {
        $this->db = new DB();
        $this->validateToken($token);
    }

    public function validateToken($token = '')
    {
        if (empty($token)) {
            return_result(10002);
        }
        $tokenSql = "SELECT * FROM yanglao_token WHERE token = '$token'";
        $token = $this->db->get_one($tokenSql);
        if (!$token) {
            return_result(10003);
        }
        if ($token['expire'] < time()) {
            return_result(10004);
        }
    }

    public function run()
    {
        $act = $_GET['act'];
        switch ($act) {
            case "filter":
                $this->filter();
                break;
            case "zcd":
                $this->zcdList();
                break;
            case "detail":
                $this->detail();
                break;
            case "mapZcd":
                $this->mapZcdList();
                break;
            case "mapDistrict":
                $this->mapDistrictList();
                break;
            default:
                return_result(-1);
                break;

        }
    }

    /**
     * 获取南京城市 区域-街道-社区
     */
    public function filter()
    {
        $citySql = "SELECT * FROM yanglao_city WHERE pinyin = 'nj'";
        $city = $this->db->get_one($citySql);
        $districtSql = "SELECT id,name FROM yanglao_district WHERE status = 1 AND city_id = ".$city['id'];
        $streetSql = "SELECT * FROM yanglao_street WHERE city_id = ".$city['id'];
        $communitySql = "SELECT * FROM yanglao_community WHERE city_id = ".$city['id'];
        $districts = $this->db->get_all($districtSql);
        $streets = $this->db->get_all($streetSql);
        $communitys = $this->db->get_all($communitySql);

        if (!empty($districts)) {
            foreach ($districts as &$district) {
                $district['streets'] = [];
                foreach ($streets as $sk => $street) {
                    if ($street['district_id'] == $district['id']) {
                        $communityArr = [];
                        foreach ($communitys as $ck => $community) {
                            if ($community['street_id'] == $street['id']) {
                                $communityArr[] = [
                                    'id'   => $community['id'],
                                    'name' => $community['name']
                                ];
                                unset($communitys[$ck]);
                            }
                        }
                        $district['streets'][] = [
                            'id'   => $street['id'],
                            'name' => $street['name'],
                            'communitys' => $communityArr
                        ];
                        unset($streets[$sk]);
                    }
                }
            }
        }
        return_result(0,$districts);
    }

    public function zcdList()
    {
        return_result(0);
        // 过滤部分请求
        if (filter_request()) {
            return_result(0);
        }
        // 随机延迟1-3秒
        sleep(rand(1,3));

        // 条件
        $keyword = $_GET['keyword'];
        $lng = $_GET['lng'] ? $_GET['lng'] : '';
        $lat = $_GET['lat'] ? $_GET['lat'] : '';
        $districtId = intval($_GET['district_id']) ? intval($_GET['district_id']) : 0;
        $streetId = intval($_GET['street_id']) ? intval($_GET['street_id']) : 0;
        $communityId = intval($_GET['community_id']) ? intval($_GET['community_id']) : 0;
        $page = intval($_GET['page']) ? intval($_GET['page']) : 1;
        $pageSize = intval($_GET['pagesize']) ? intval($_GET['pagesize']) : 10;

        $fields = "f.id,f.pic,f.name,f.street_id,f.community_id,f.provide_food,f.opening_hours,f.address,s.name as street_name,c.name as community_name,f.sort";
        if (!empty($lat) && !empty($lng)) {
            $distanceFiled = ",ROUND(6378.138 * 2 * ASIN(SQRT(POW(SIN(($lat * PI() / 180 - f.lat * PI() / 180) / 2),2) + COS(23.163292 * PI() / 180)
         * COS(f.lat * PI() / 180) * POW(SIN(($lng * PI() / 180 - f.lng * PI() / 180) / 2),2))) * 1000) AS distance";
            $fields = $fields.$distanceFiled;
            $order = " ORDER BY distance ASC,sort DESC";
        } else {
            $order = " ORDER BY sort DESC";
        }

        $sql = "SELECT $fields FROM yanglao_food f LEFT JOIN yanglao_street s ON s.id = f.street_id
              LEFT JOIN yanglao_community c ON c.id = f.community_id WHERE 1 = 1";
        $countSql = "SELECT COUNT(*) AS num FROM yanglao_food f LEFT JOIN yanglao_street s ON s.id = f.street_id
              LEFT JOIN yanglao_community c ON c.id = f.community_id WHERE 1 = 1";
        // 筛选条件
        if (!empty($keyword)) {
            $sql .= " AND (f.name like '%$keyword%' or s.name like '%$keyword%' or c.name like '%$keyword%')";
            $countSql .= " AND (f.name like '%$keyword%' or s.name like '%$keyword%' or c.name like '%$keyword%')";
        }
        if ($districtId) {
            $sql .= " AND f.district_id = ".$districtId;
            $countSql .= " AND f.district_id = ".$districtId;
        }
        if ($streetId) {
            $sql .= " AND f.street_id = ".$streetId;
            $countSql .= " AND f.street_id = ".$streetId;
        }
        if ($communityId) {
            $sql .= " AND f.community_id = ".$communityId;
            $countSql .= " AND f.community_id = ".$communityId;
        }

        $njCityId = $this->getNjCityId();
        $sql .= " AND status = 1 AND is_delete = 0 AND f.city_id =".$njCityId;
        $countSql .= " AND status = 1 AND is_delete = 0 AND f.city_id =".$njCityId;
        // 排序
        $sql.= $order;
        // 分页
        $sql.= " LIMIT ".($page-1) * $pageSize .",".$pageSize;
        $zcds = $this->db->get_all($sql);
        $count = $this->db->get_one($countSql);

        return_result(0,[
            'list' => $zcds,
            'total' => $count['num']
        ]);

    }

    public function detail()
    {
        $id = intval($_GET['id']);
        if (empty($id)) {
            return_result(10005);
        }
        $citySql = "SELECT * FROM yanglao_city WHERE pinyin = 'nj'";
        $city = $this->db->get_one($citySql);

        $fields = "id,name,address,area,opening_hours,provide_food,contacts,pic,breakfast_time,breakfast_price,breakfast_sub,lunch_time,lunch_price,lunch_sub,dinner_time,dinner_price,dinner_sub,prefix,phone2 as phone,`natural`,lng,lat ";
        $sql = "SELECT $fields FROM yanglao_food WHERE city_id =".$city['id']." AND id = ".$_GET['id'];
        $zcd = $this->db->get_one($sql);

        return return_result(0,$zcd ? $zcd : new stdClass());
    }

    /**
     * 获取范围内的区域
     */
    public function mapDistrictList()
    {
        // 过滤部分请求
        if (filter_request()) {
            return_result(0);
        }
        // 随机延迟1-3秒
        sleep(rand(1,3));

        $x1 = $_GET['x1'];
        $y1 = $_GET['y1'];
        $x2 = $_GET['x2'];
        $y2 = $_GET['y2'];

        if (empty($x1) || empty($y1) || empty($x2) || empty($y2)) {
            return_result(10005);
        }
        $njCityId = $this->getNjCityId();
        $districtSql = "SELECT * FROM yanglao_district WHERE status = 1 AND lng>= $x1 AND lng<=$x2 AND lat>=$y1 AND lat<=$y2 AND city_id = ".$njCityId;
        $districts = $this->db->get_all($districtSql);
        if (empty($districts)) {
            return_result(0);
        }

        $districtIds = array_column($districts,"id");
        // 查处每个区域下的助餐点数量
        $sql = "SELECT d.lng,d.lat,d.name,count(*) as count,d.id FROM yanglao_food f LEFT JOIN yanglao_district d ON f.district_id = d.id WHERE
                f.status =1 AND f.is_delete = 0 AND f.district_id IN (".implode(',',$districtIds).") AND d.status=1 GROUP BY d.id";

        $ret = $this->db->get_all($sql);
        return_result(0,$ret);
    }

    /**
     * 返回地图范围内的助餐点列表
     */
    public function mapZcdList()
    {
        // 过滤部分请求
        if (filter_request()) {
            return_result(0);
        }
        // 随机延迟1-3秒
        sleep(rand(1,3));

        $x1 = $_GET['x1'];
        $y1 = $_GET['y1'];
        $x2 = $_GET['x2'];
        $y2 = $_GET['y2'];

        if (empty($x1) || empty($y1) || empty($x2) || empty($y2)) {
            return_result(10005);
        }

        $fields = "f.id,f.pic,f.name,f.street_id,f.community_id,f.provide_food,f.opening_hours,f.address,s.name as street_name,c.name as community_name,f.sort";
        $sql = "SELECT $fields FROM yanglao_food f LEFT JOIN yanglao_street s ON s.id = f.street_id
              LEFT JOIN yanglao_community c ON c.id = f.community_id
              WHERE f.lng >= $x1 AND f.lng <= $x2 AND f.lat >= $y1 AND f.lat <= $y2 
              AND f.status = 1 AND is_delete = 0 AND f.city_id = ".$this->getNjCityId();
        $zcds = $this->db->get_all($sql);
        return_result(0,$zcds);
    }

    private function getNjCityId()
    {
        $citySql = "SELECT * FROM yanglao_city WHERE pinyin = 'nj'";
        $city = $this->db->get_one($citySql);

        return $city['id'];
    }
}