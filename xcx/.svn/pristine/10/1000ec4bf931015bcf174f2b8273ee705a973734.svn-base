<?php
include System . DS . 'Upload.php';
class Xcxmysql extends Controller
{
    function __construct() {
        $this->db = new Query();
		$this->db2 = new Query(new DataBase2());
    }
		//文章数据导入
    public function importArticle(){
        ini_set('max_execution_time', 3600);
        set_time_limit(0);
				
        $ii=Context::Get('page');
        if(empty($ii)){echo "请传入页数";exit;}
        $cidDict=['1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5,'10'=>6,'18'=>7,'6'=>8,'7'=>9,'8'=>10];

        //获取9h数据库文章数据
        $nineData=$this->db2->Name('news')->select()->page($ii,30)->execute();
        if(!empty($nineData)){
//            $sqlStr="INSERT INTO 9h_xcx_article_article (aid,cid,title,cover,content,read_num,province,city,create_time,update_time) VALUES ";
            $sqlArr=[];
            $areasId=[];
            foreach($nineData as $val2){
                $areasId[]=$val2['area_id'];
            }
            $areasId=array_unique($areasId);
            $areasDict=[];
            foreach($areasId as $area_id){
                $areaTemp=$this->getAreaData($area_id);
                $areaTemp=explode(',',$areaTemp);
                if(is_array($areaTemp)){
                    $areasDict[$area_id]=['province'=>$areaTemp[1],'city'=>$areaTemp[0]];
                }
            }
            foreach($nineData as $val){
                $articleRes=$this->db->Name('xcx_article_article')->select()->where_equalTo('title',$val['title'])->firstRow();
                if(empty($articleRes) && !empty($val['title'])){
                    $data=[];
										$data['area_id']=$val['area_id'];
										$data['source']=$val['source'];
                    $data['title']=$val['title'];     //标题
                    $data['aid']=2;     //后台发布人id
                    $cid=$val['type'];
                    if(in_array($cid,[1,2,3,4,5,10,18,6,7,8])){
                        $data['cid']=$cidDict[$cid];     //栏目id
                    }else{
                        continue;
                    }
                    $data['cover']='/'.$val['pic'];   //封面
                    $data['description']=$val['description'];   //描述简介
                    $data['content']=$val['content'];   //内容
                    $data['read_num']=$val['click'];   //阅读数
                    $data['province']=empty($areasDict[$val['area_id']]['province'])?'':$areasDict[$val['area_id']]['province'];
                    $data['city']=empty($areasDict[$val['area_id']]['city'])?'':$areasDict[$val['area_id']]['city'];
                    $data['create_time']=empty($val['addtime'])?time():strtotime($val['addtime']);
                    $data['update_time']=empty($val['addtime'])?time():strtotime($val['addtime']);
                    $sqlArr[]=$this->db->Name('xcx_article_article')->insert($data)->execute();
//                    $sqlArr[]="(".$data['aid'].",".$data['cid'].",'".$data['title']."','".$data['cover']."','".$data['content']."',".$data['read_num'].",'".$data['province']."','".$data['city']."',".$data['create_time'].",".$data['update_time'].")";
                }else{
										$data=[];
										$data['area_id']=$val['area_id'];
										$data['source']=$val['source'];
										$this->db->Name('xcx_article_article')->update($data)->where_equalTo('id',$articleRes['id'])->execute();
								}
            }
//            if(count($sqlArr)>0){
//                DataBase::Insert($sqlStr.implode(',',$sqlArr) ,[]);
//            }
            if(count($sqlArr)>0){
                echo "成功导入:".count($sqlArr).'条数据';
            }
            $ii++;
            $url="http://test.beimiao.com/xiamenyyhoutai/xcxmysql/importArticle?page=".$ii;
            header('Refresh: 1;url='.$url);
        }else{
            echo '导入完成！';
        }

    }
    //数据库导入
    public function importDatabase(){
        ini_set('max_execution_time', 3600);
        set_time_limit(0);
        $ii=Context::Get('page');
        if(empty($ii)){echo "请传入页数";exit;}
        $youxiData=[];

        //获取9h数据库楼盘数据
        $nineData=$this->db2->Name('property')->select()->page($ii,10)->execute();
        if(!empty($nineData)){
            $areasId=[];
            foreach($nineData as $val2){
                $areasId[]=$val2['area_id'];
            }
            $areasId=array_unique($areasId);
            $areasDict=[];
            foreach($areasId as $area_id){
                $areaTemp=$this->getAreaData($area_id);
                $areaTemp=explode(',',$areaTemp);
                if(is_array($areaTemp)){
                    $areasDict[$area_id]=['province'=>$areaTemp[2],'city'=>$areaTemp[1],'area'=>$areaTemp[0]];
                }
            }
            foreach($nineData as $val){
                $buildingRes=$this->db->Name('xcx_building_building')->select()->where_equalTo('name',$val['name'])->firstRow();
                if(empty($buildingRes) && !empty($val['name'])){
                    $data=[];
										$data['area_id']=$val['area_id'];
                    $data['name']=$val['name'];     //楼盘名称
                    $data['sales_status']=in_array($val['sales_status'],['售完','待售','在售'])?$val['sales_status']:'售完';     //销售状态
                    $data['fold']=strpos($val['fold'],'元/')!==false?intval($val['fold']):0;     //参考价格
                    $data['pic']='/'.$val['pic'];   //楼盘封面
//                        if(!empty($val['pic'])){
//                            $picUrl='http://www.999house.com/'.$val['pic'];
//                            $picTemp=$this->getImage($picUrl);
//                            if(empty($picTemp['error'])){
//                                $data['pic']=$picTemp['pic'];
//                            }
//                        }
                    $data['coordinate']="0,0";   //楼盘坐标
                    if(!empty($val['coordinate'])){
                        $coordinate=explode(',',$val['coordinate']);
                        $data['coordinate']=$coordinate[1].','.$coordinate[0];
                    }
                    $data['province']=empty($areasDict[$val['area_id']]['province'])?'':$areasDict[$val['area_id']]['province'];
                    $data['city']=empty($areasDict[$val['area_id']]['city'])?'':$areasDict[$val['area_id']]['city'];
                    $data['area']=empty($areasDict[$val['area_id']]['area'])?'':$areasDict[$val['area_id']]['area'];
                    $data['address']=$val['address'];   //楼盘地址
                    $house_type=$val['project_type'];   //楼盘类型
                    if(strpos($house_type,'别墅')!==false){
                        $data['house_type']='别墅';
                    }else if(strpos($house_type,'商铺')!==false){
                        $data['house_type']='商铺';
                    }else if(strpos($house_type,'写字楼')!==false){
                        $data['house_type']='写字楼';
                    }else{
                        $data['house_type']='普通住宅';
                    }
                    $data['commission']=empty($val['pfp'])?0:$val['pfp'];   //佣金
                    $louchen=$val['building_type']; //楼层
                    if(strpos($louchen,'高层')!==false){
                        $data['louchen']='高楼层';
                    }else{
                        $data['louchen']='低楼层';
                    }
                    $data['developers']=empty($val['developers'])?'未知':$val['developers'];   //开发商
                    $data['kaipang_time']=empty($val['kaipandate'])?0:strtotime($val['kaipandate']);    //开盘时间
                    $data['jiaofang_time']=0;   //交房时间
                    $data['sales_telephone']=empty($val['telephone_sales'])?'暂无':$val['telephone_sales'];   //售楼电话
                    $data['sizelayout']=empty($val['sizelayout'])?'暂无信息':$val['sizelayout'];   //大小户型
                    $data['planning_number']=0;   //规划户数
                    $data['project_type']=empty($val['project_type'])?'暂无信息':$val['project_type'];   //项目类型
                    $data['building_type']=empty($val['building_type'])?'暂无信息':$val['building_type'];   //建筑类型
                    $data['total_area']=empty($val['total_area'])?'暂无信息':$val['total_area'];   //占地总面积
                    $data['total_construction_area']=empty($val['total_construction_area'])?'暂无信息':$val['total_construction_area'];   //建筑总面积
                    $data['floor_condition']=empty($val['floor_condition'])?'暂无信息':$val['floor_condition'];   //楼层状况
                    $data['progress_project']=empty($val['progress_project'])?'暂无信息':$val['progress_project'];   //项目进度
                    $data['floor_height']=0;    //层高
                    $data['pool']=empty($val['pool'])?'暂无信息':$val['pool'];   //公摊
                    $data['decoration']=empty($val['decoration'])?'暂无信息':$val['decoration'];   //装修情况
                    $data['property_company']=empty($val['property_company'])?'暂无信息':$val['property_company'];   //物业公司
                    $data['property_type']='暂无信息';   //物业类型
                    $data['property_charges']=empty($val['property_charges'])?'暂无信息':$val['property_charges'];   //物业费
                    $data['volume_rate']=empty($val['volume_rate'])?'暂无信息':$val['volume_rate'];   //容积率
                    $data['greening_rate']=empty($val['greening_rate'])?'暂无信息':$val['greening_rate'];   //绿化率
                    $data['parking_space_number']=0;   //车位数
                    $data['parking_space_proportion']='暂无信息';   //车位比
                    $data['traffic_complete']=empty($val['bus'])?'暂无信息':$val['bus'];   //交通配套
                    $data['education_resources']=empty($val['school'])?'暂无信息':$val['school'];   //教育资源
                    $data['medical_health']=empty($val['hospital'])?'暂无信息':$val['hospital'];   //医疗健康
                    $data['shopping_mall']=empty($val['shopping_mall'])?'暂无信息':$val['shopping_mall'];   //商城购物
                    $data['live_entertainment']=empty($val['matching'])?'暂无信息':$val['matching'];   //生活娱乐
                    $data['create_time']=empty($val['addtime'])?time():strtotime($val['addtime']);
                    $data['update_time']=empty($val['addtime'])?time():strtotime($val['addtime']);
                    $tempId=$this->db->Name('xcx_building_building')->insert($data)->execute();
                    $youxiData[]=$tempId;
                    if($tempId){
                        if($data['coordinate']!='0,0'){     //生成周边信息
                            $this->set_building_map($data['coordinate'],$tempId);
                        }
                        if(!empty($val['pic'])){    //将封面插入轮播图数据
                            $this->db->Name('xcx_building_shuffle')->insert(['building_id'=>$tempId,'img'=>'/'.$val['pic'],'create_time'=>time(),'update_time'=>time()])->execute();
                        }
                    }
                }else{
										$data=[];
										$data['area_id']=$val['area_id'];
										$this->db->Name('xcx_building_building')->update($data)->where_equalTo('id',$buildingRes['id'])->execute();
								}
            }
            if(count($youxiData)>0){
                echo "成功导入:".count($youxiData).'条数据';
            }
            $ii++;
            $url="http://test.beimiao.com/xiamenyyhoutai/xcxmysql/importDatabase?page=".$ii;
            header('Refresh: 1;url='.$url);
        }else{
            echo '导入完成！';
        }

    }
    /**
     * 求两个已知经纬度之间的距离,单位为米
     *
     * @param lng1 $ ,lng2 经度
     * @param lat1 $ ,lat2 纬度
     * @return float 距离，单位米
     * @author www.Alixixi.com
     */
    private function getdistance($lng1, $lat1, $lng2, $lat2) {
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return $s;
    }
    //生成对应的周边信息
    private function set_building_map($location="",$id){
				$location=explode(',',$location);
        $keyword=["公交","学校","医院","购物","美食"];
        $boundary="nearby(".$location[0].",".$location[1].",1000)";
        $key="7VABZ-GKERX-R5K4U-ZNGQ6-6Z5B7-BZFC7";
        foreach($keyword as $value){
            $res=$this->sendGet("https://apis.map.qq.com/ws/place/v1/search?keyword=".urlencode($value)."&boundary=".$boundary."&key=".$key);
            $res=json_decode($res,true);
            if(empty($res['status'])){
                $dataInfo=$res['data'];
                if(!empty($dataInfo)){
                    foreach($dataInfo as $val){
                        $data=[];
                        $data['building_id']=$id;
                        $data['keyword']=$value;
                        $data['title']=$val['title'];
                        $data['address']=$val['address'];
                        $data['tel']=$val['tel'];
                        $data['category']=$val['category'];
                        $data['lat']=$val['location']['lat'];
                        $data['lng']=$val['location']['lng'];
                        $data['province']=$val['ad_info']['province'];
                        $data['city']=$val['ad_info']['city'];
                        $data['district']=$val['ad_info']['district'];
                        $data['distance']=$this->getdistance($location[1],$location[0],$data['lng'],$data['lat']);
                        $data['create_time']=time();
                        $data['update_time']=time();
                        $this->db->Name('xcx_building_map')->insert($data)->execute();
                    }
                }
            }
        }
        return true;
    }
    /*
    *功能：php完美实现下载远程图片保存到本地
    *参数：文件url,保存文件目录,保存文件名称，使用的下载方式
    *当保存文件名称为空时则使用远程文件原来的名称
    */
    private function getImage($url,$save_dir=BasePath.DS.'upload'.DS.'importdata',$filename='',$type=0)
    {
        if (trim($url) == '') {
            return array('file_name' => '', 'save_path' => '', 'error' => 1);
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (trim($filename) == '') {//保存文件名
            $ext = strrchr($url, '.');
            if ($ext != '.gif' && $ext != '.jpg') {
                return array('file_name' => '', 'save_path' => '', 'error' => 3);
            }
            $filename = time() . mt_rand(1000,9999) . $ext;
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir .= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return array('file_name' => '', 'save_path' => '', 'error' => 5);
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $img = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $img = ob_get_contents();
            ob_end_clean();
        }
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $img);
        fclose($fp2);
        unset($img, $url);
        return array('file_name' => $filename, 'save_path' => $save_dir . $filename,'pic'=>'/upload/importdata/'.$filename, 'error' => 0);
    }

    /**
     * 根据area_id获取省市区
     * @param $area_id
     * @return array
     */
    private function getAreaData($area_id){
        $temp=$this->db2->Name('area')->select()->where_equalTo('id',$area_id)->firstRow();
        $res=$temp['name'].',';
        if(!empty($temp['parentid'])){
            $res.=$this->getAreaData($temp['parentid']);
        }
        return $res;
    }
    protected function sendGet($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    protected function sendPost($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}