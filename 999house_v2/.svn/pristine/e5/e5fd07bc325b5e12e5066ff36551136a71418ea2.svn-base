<?php
namespace app\admin\controller;

use app\common\base\AdminBaseController;
use app\common\MyConst;
use app\server\admin\Admin;
use app\server\admin\City;
use app\server\admin\CitySite;
use app\server\admin\Subway;
use think\facade\Db;
use think\Validate;


class CityController extends AdminBaseController
{
    //获取
    public function sysCitys(){
        $data = $this->request->param();
        $data['pid'] = intval($data['pid']);
        $rs =  (new City())->getList([
            'pid' => $data['pid']
        ])['result'];
        $this->success($rs);
    }

    //=============== 获取开通的城市点 ===============//
    public function siteCitys(){
        $data = $this->request->param();
        $where = [];
        if(isset($data['status'])){
            $where['status'] = intval($data['status']);
        }
        if(isset($data['is_hot'])){
            $where['is_hot'] = intval($data['is_hot']);
        }
        $rs =  (new City())->getSiteCitys($where)['result'];
        $this->success($rs);
    }
    public function siteCitysEdit(){
        $data = $this->request->param();
        if(empty($data['province_name'])||empty($data['province_no'])||empty($data['city_name'])||empty($data['city_no'])){
            $this->error(['code'=>0,'msg'=> '请选择城市']);
        }

        $inData = [
            'cname'=> $data['city_name'],
            'status'=> intval($data['status']),
            'is_hot'=> intval($data['is_hot']),
            'sort'=> intval($data['sort']),
            'pid'=> $data['province_no'],
            'pcname'=> $data['province_name'],
            'lng'=> $data['lng'],
            'lat'=> $data['lat'],
        ];

        if($data['dotype']=='add'){
            $inData['id'] = $data['city_no'];
            $location = $this->getMapGeoByAddress($data['city_name']);
            $inData['lng'] = $location['lng'];
            $inData['lat'] = $location['lat'];

            $rs =  (new City())->siteCitysAdd($inData);
        }else{
            $rs =  (new City())->siteCitysEdit($data['city_no'],$inData);
        }

        $this->success($rs);
    }
    public function siteCitysDel(){
        $data = $this->request->param();
        $rs =  (new City())->siteCitysDel(intval($data['id']))['result'];
        $this->success($rs);
    }

    //站点区域操作
    public function siteAreas(){
        $data = $this->request->param();

        $where['pid'] = intval($data['pid']);
        if(isset($data['status'])){
            $where['status'] = intval($data['status']);
        }
        if(isset($data['is_custom'])){
            $where['is_custom'] = intval($data['is_custom']);
        }
        
        $rs =  (new City())->getSiteAreas($where)['result'];
        $this->success($rs);
    }
    //站点区域操作
    public function siteAreasEdit(){
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['pid'] = intval($data['pid']);
        $data['status'] = intval($data['status']);
        if(empty($data['id'])){
            $this->error(['code'=>0,'msg'=> '请填写id编码']);
        }
        if(empty($data['pid'])){
            $this->error(['code'=>0,'msg'=> '缺失上级参数']);
        }
        $data['cname'] = trim_all($data['cname']);
        if(empty($data['cname'])){
            $this->error(['code'=>0,'msg'=> '请填写区名称']);
        }

        if($data['is_custom']==1){
            $inData['id'] = $data['id'];
            $inData['cname'] = $data['cname'];
            $inData['status'] = $data['status'];
            $inData['pid'] = $data['pid'];
        }
        $inData['lng'] = $data['lng'];
        $inData['lat'] = $data['lat'];

        if($data['dotype']=='add'){
            $inData['is_custom'] = 1;//自定义标识
            $rs =  (new City())->siteAreasAdd($inData);
        }else{
            $rs =  (new City())->siteAreasEdit($data['id'],$inData);
        }

        $this->success($rs);
    }
    //站点区域操作
    public function siteAreasDel(){
        $data = $this->request->param();
        $rs =  (new City())->siteAreasDel(intval($data['id']))['result'];
        $this->success($rs);
    }

    //站点商圈操作
    public function siteBusinessAreas(){
        $data = $this->request->param();

        if(isset($data['status'])){
            $where['status'] = intval($data['status']);
        }
        if(isset($data['city_no'])){
            if($data['city_no']==-1){
                unset($data['city_no']);
            }else{
                $where['city_no'] = intval($data['city_no']);
            }
        }
        if(isset($data['area_no'])){
            $where['area_no'] = intval($data['area_no']);
        }

        $rs =  (new City())->getSiteBusinessAreas($where)['result'];
        if(empty($rs)){
            $rs = [];
        }else{
            foreach ($rs as &$item){
                if(!empty($item['area_no'])){
                    $area = $this->db->name('site_city_area')->field('cname,pcname')->where([
                        'id' => $item['area_no']
                    ])->find();
                    $item['area_name'] = $area['cname'];
                    $item['city_name'] = $area['pcname'];
                }
            }
            unset($item);
        }

        $this->success($rs);
    }
    //站点商圈操作
    public function siteBusinessAreasEdit(){
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['city_no'] = intval($data['city_no']);
        $data['area_no'] = intval($data['area_no']);
        $data['status'] = intval($data['status']);

        if(empty($data['city_no'])||empty($data['area_no'])){
            $this->error(['code'=>0,'msg'=> '缺失城市参数']);
        }
        $data['cname'] = trim_all($data['cname']);
        if(empty($data['cname'])){
            $this->error(['code'=>0,'msg'=> '请填写商圈名称']);
        }

        $inData = [
            'cname'=> $data['cname'],
            'status'=> $data['status'],
            'area_no'=> $data['area_no'],
            'city_no'=> $data['city_no'],
        ];

        if($data['dotype']=='add'){
            $rs =  (new City())->siteBusinessAreasAdd($inData);
        }else{
            $rs =  (new City())->siteBusinessAreasEdit([
                'id'=>$data['id'],
                'area_no'=>$data['area_no'],
            ],$inData);
        }

        $this->success($rs);
    }
    //站点商圈操作
    public function siteBusinessAreasDel(){
        $data = $this->request->param();
        $rs =  (new City())->siteBusinessAreasDel(intval($data['id']))['result'];
        $this->success($rs);
    }

    //站点街道操作
    public function siteStreet(){
        $data = $this->request->param();

        if(isset($data['status'])){
            $where['status'] = intval($data['status']);
        }
        if(empty($data['city_no'])||$data['city_no']==-1){
            $regionRes = $this->getMyCity();
            $cityIds = !empty($regionRes['data']) ? array_column($regionRes['data'], 'id') : [];
            $where['city_no'] = ['in', $cityIds];
        }else{
            $where['city_no'] = intval($data['city_no']);
        }

        if(!empty($data['area_no'])){
            $where['area_no'] = intval($data['area_no']);
        }

        $rs =  (new City())->getsiteStreet($where)['result'];
        if(empty($rs)){
            $rs = [];
        }else{
            foreach ($rs as &$item){
                if(!empty($item['area_no'])){
                    $area = $this->db->name('site_city_area')->field('cname,pcname')->where([
                        'id' => $item['area_no']
                    ])->find();
                    $item['area_name'] = $area['cname'];
                    $item['city_name'] = $area['pcname'];
                }
            }
            unset($item);
        }

        $this->success($rs);
    }
    //站点街道操作
    public function siteStreetEdit(){
        $data = $this->request->param();
        $data['id'] = intval($data['id']);
        $data['city_no'] = intval($data['city_no']);
        $data['area_no'] = intval($data['area_no']);
        $data['status'] = intval($data['status']);

        if(empty($data['city_no'])||empty($data['area_no'])){
            $this->error(['code'=>0,'msg'=> '缺失城市参数']);
        }
        $data['cname'] = trim_all($data['cname']);
        if(empty($data['cname'])){
            $this->error(['code'=>0,'msg'=> '请填写商圈名称']);
        }

        $inData = [
            'cname'=> $data['cname'],
            'status'=> $data['status'],
            'area_no'=> $data['area_no'],
            'city_no'=> $data['city_no'],
        ];

        if($data['dotype']=='add'){
            $rs =  (new City())->siteStreetAdd($inData);
        }else{
            $rs =  (new City())->siteStreetEdit([
                'id'=>$data['id'],
                'area_no'=>$data['area_no'],
            ],$inData);
        }

        $this->success($rs);
    }
    //站点街道操作
    public function siteStreetDel(){
        $data = $this->request->param();
        $rs =  (new City())->siteStreetDel(intval($data['id']))['result'];
        $this->success($rs);
    }
    //=============== 获取开通的城市点end ===============//


    //===================站点的配置操作===================//
    public function siteInfo(){
        $data = $this->request->param();

        if(is_array($data['key'])){
            $data['key'] = ['in',$data['key']];
        }
        $rs = (new CitySite())->setInfo([
            'key'=> $data['key'],
            'region_no'=> $data['region_no']
        ])['result'];

        $this->success($rs);
    }

    /**
     * 创建编辑
     */
    public function siteEdit(){
        $data = $this->request->param();
        if(!is_array($data['key'])){
            $data['key'] = [$data['key']];
        }

        $data['key']['average_area'] = json_encode($data['areaItems']);
        $data['key']['average_price'] = json_encode($data['cityItems']);
        $data['key']['total_price'] = json_encode($data['totalItems']);
        $data['key']['subway'] = json_encode($data['subway']);
        $key= $data['key'];
        foreach ($key as $k=>$val){
            if(is_array($val)){
                $val = json_encode($val,JSON_UNESCAPED_UNICODE);
            }

            $rs = (new CitySite())->setEdit([
                'key'=> $k,
                'val'=> $val,
                'region_no'=> $data['region_no'],
            ])['result'];
        }
        $redis = $this->getReids();
        $key = MyConst::ESTATES_CONDITION . $data['region_no'];
        $redis->del($key);
        $this->success($rs,'操作成功');
    }
    //===================站点的配置操作 end===================//

    //===================地铁的站点操作 start===================//

    /**
     * 地铁各站点列表
     */
    public function getSubwayList()
    {
        $param = $this->request->param();

        $where = [];

        // 状态
        if(isset($param['status']) && in_array($param['status'], [0, 1])) {
            $where[] = ['status', '=', $param['status']];
        }

        // 地铁线
        if(!empty($param['subway']) && -1 != $param['subway']) {
            $where[] = ['', 'exp', Db::raw("FIND_IN_SET({$param['subway']}, subway)")];
        }

        // 名称
        if(!empty($param['name'])) {
            $where[] = ['name', '=', $param['name']];
        }
        
        // 城市
        if(-1 == $param['region_no']||empty($param['region_no'])) {// 搜索当前全部城市
            $regionRes = $this->getMyCity();

            $cityIds = !empty($regionRes['data']) ? array_column($regionRes['data'], 'id') : [];

            $where[] = ['region_no', 'in', $cityIds];
        } else {
            $where[] = ['region_no', '=', $param['region_no']];
        }

         $res = (new Subway())->getSubwayPage($where);

         if(empty($res['code'])) {
            $this->error($res);
         }

         if(!empty($res['result']['list'])) {
            foreach($res['result']['list'] as &$v) {
                $v['subway'] = !empty($v['subway']) ? explode(',', $v['subway']) : [];
                $subwayName = [];
                if(!empty($v['subway'])) {
                    foreach($v['subway'] as $item) {
                        $subwayName[] = "{$item}号线";
                    }
                }
                $v['subway_name'] = !empty($subwayName) ? implode(',', $subwayName) : "";
            }
         }

         $this->success($res['result']);
    }

    /**
     * 地铁站点列表
     */
    public function getSubwaySites()
    {
        $param = $this->request->param();

        $where = [
            ['status', '=', 1], // 状态
        ];
        
        // 城市
        if(!empty($param['region_no'])) {
            $where[] = ['region_no', '=', $param['region_no']];
        }

        $fields = 'id, name, subway';

        $res = (new Subway())->getSubwayList($where, $fields);

        if(empty($res['code'])) {
            $this->error($res);
        }

        $result = [];

        if(!empty($res['result'])) {
            foreach($res['result'] as &$v) {
                $v['subway'] = !empty($v['subway']) ? explode(',', $v['subway']) : [];
                // 拼接站点名和几号线
                if(!empty($v['subway'])) {
                    $subwayName = [];
                    foreach($v['subway'] as $item) {
                        $subwayName[] = "{$item}号线";
                    }
                    $subwayName = !empty($subwayName) ? implode(',', $subwayName) : "";
                    $v['name'] = "{$v['name']}({$subwayName})";
                }
            }
            $result = $res['result'];
        }

        $this->success($result);
    }

    /**
     * 添加/编辑 地铁站点
     */
    public function editSubway()
    {
        $param = $this->request->param();

        if(empty($param['name'])) {
            $this->error('站点名称不能为空');
        }
        if(empty($param['region_no'])) {
            $this->error('地区不能为空');
        }
        if(empty($param['subway'])) {
            $this->error('所属地铁不能为空');
        }

        $id = $param['id'] ?? 0;

        $data = [
            'name' => $param['name'],
            'region_no' => $param['region_no'] ?? "",
            'region_name' => $param['region_name'] ?? "",
            'status' => (int)$param['status'],
        ];

        $data['subway'] = !empty($param['subway']) ? implode(',', $param['subway']) : "";

        $server = new Subway();

        if($id) {
            $res = $server->edit($id, $data);
        } else {
            $res = $server->add($data);
        }

        if(empty($res['code'])) {
            $this->error($res);
        }

        $this->success();
    }

    /**
     * 修改状态
     */
    public function setSubwayStatus()
    {
        $param = $this->request->param();

        if(empty($param['id'])) {
            $this->error('缺少必要参数');
        }
        $id = $param['id'];

        $data = [
            'status' => (int)$param['status'],
        ];

        $server = new Subway();

        $res = $server->edit($id, $data);

        if(empty($res['code'])) {
            $this->error($res);
        }

        $this->success();
    }

    /**
     * 删除地铁站点
     */
    public function delSubway()
    {
        $param = $this->request->param();

        if(empty($param['id'])) {
            $this->error('缺少必要参数');
        }

        $res = (new Subway)->delete($param['id']);

        if(empty($res['code'])) {
            $this->error($res);
        }

        $this->success();
    }

    //===================地铁的站点操作 end===================//



    private function getMapGeoByAddress($address=''){
        $rs = doCoHttp([
            'url' => 'http://api.map.baidu.com/geocoding/v3/',
            'http_type' => 'post',
            'data'=>[
                'address'=> $address,//'厦门市思明区',
                'ak' => 'SDuv1MnquxGaLdP9zCcBu7pFI8A2cfYq',//'QBGa5QO0dackR4MfegYkaeN8UdcaxEti',//SDuv1MnquxGaLdP9zCcBu7pFI8A2cfYq
                'output'=> 'json',
                'callback'=> 'showLocation',
                'ret_coordtype' => 'gcj02ll'
            ]
        ]);

        if(!empty($rs['body'])){
            $rs = $rs['body'];
            return $rs['result']['location'];
        }
    }
}
