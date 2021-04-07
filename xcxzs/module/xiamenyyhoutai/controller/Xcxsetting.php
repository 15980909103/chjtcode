<?php
include 'AdminController.php';
include System . DS . 'Upload.php';
class Xcxsetting extends AdminController
{
    public function setting_index(){
        //获取系统配置信息
        $data=[];
        $settingRow=$this->db->Name('xcx_setting')->select()->execute();
        if(!empty($settingRow)){
            foreach($settingRow as $val){
                if($val['key']=='system_name'){$data['system_name']=$val['value'];}
                if($val['key']=='system_logo'){$data['system_logo']=$val['value'];}
                if($val['key']=='wechat_logo'){$data['wechat_logo']=$val['value'];}
                if($val['key']=='max_article'){$data['max_article']=$val['value'];}
                if($val['key']=='system_wechat'){$data['system_wechat']=$val['value'];}
                if($val['key']=='system_company'){$data['system_company']=$val['value'];}
                if($val['key']=='report_protect_date'){$data['report_protect_date']=$val['value'];}
            }
        }
        return $this->render('setting_index',$data);
    }
    public function systemLogo(){
        $upfile = new UploadFiles(array('filepath'=>BasePath . DS .'upload' . DS .'setting'));
        if($upfile->uploadeFile('file')){
            $arrfile = $upfile->getnewFile();
            $oldImg=(new Query())->Name('xcx_setting')->select()->where_equalTo('`key`','system_logo')->firstRow()['value'];
            $data['value']='/upload/setting/'.$arrfile;
            $res=(new Query())->Name('xcx_setting')->update($data)->where_equalTo('`key`','system_logo')->execute();
            if($res){
                if(file_exists(BasePath .$oldImg)){
                    unlink(BasePath .$oldImg);
                }
                echo json_encode(['success'=>true,'img'=>$data['value']]);
            }else{
                echo json_encode(['success'=>false,'msg'=>"保存失败"]);
            }
        }else{
            $err = $upfile->gteerror();
            echo json_encode(['success'=>false,'msg'=>$err]);
        }
    }
    public function wechatLogo(){
        $upfile = new UploadFiles(array('filepath'=>BasePath . DS .'upload' . DS .'setting'));
        if($upfile->uploadeFile('file')){
            $arrfile = $upfile->getnewFile();
            $oldImg=(new Query())->Name('xcx_setting')->select()->where_equalTo('`key`','wechat_logo')->firstRow()['value'];
            $data['value']='/upload/setting/'.$arrfile;
            $res=(new Query())->Name('xcx_setting')->update($data)->where_equalTo('`key`','wechat_logo')->execute();
            if($res){
                if(file_exists(BasePath .$oldImg)){
                    unlink(BasePath .$oldImg);
                }
                echo json_encode(['success'=>true,'img'=>$data['value']]);
            }else{
                echo json_encode(['success'=>false,'msg'=>"保存失败"]);
            }
        }else{
            $err = $upfile->gteerror();
            echo json_encode(['success'=>false,'msg'=>$err]);
        }
    }
    //修改系统名称
    public function updateSystemName(){
        $systemName=Context::Post('systemName');
        if(!empty($systemName)){
            $res=$this->db->Name('xcx_setting')->update(['value'=>$systemName])->where_equalTo('`key`','system_name')->execute();
            if($res){
                echo json_encode(['success'=>true]);
            }else{
                echo json_encode(['success'=>false]);
            }
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    public function updateSystemWechat(){
        $systemWechat=Context::Post('systemWechat');
        if(!empty($systemWechat)){
            $res=$this->db->Name('xcx_setting')->update(['value'=>$systemWechat])->where_equalTo('`key`','system_wechat')->execute();
            if($res){
                echo json_encode(['success'=>true]);
            }else{
                echo json_encode(['success'=>false]);
            }
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    public function updateSystemCompany(){
        $systemCompany=Context::Post('systemCompany');
        if(!empty($systemCompany)){
            $res=$this->db->Name('xcx_setting')->update(['value'=>$systemCompany])->where_equalTo('`key`','system_company')->execute();
            if($res){
                echo json_encode(['success'=>true]);
            }else{
                echo json_encode(['success'=>false]);
            }
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    public function updateMaxArticle(){
        $maxArticle=Context::Post('maxArticle');
        if(!empty($maxArticle)){
            $res=$this->db->Name('xcx_setting')->update(['value'=>$maxArticle])->where_equalTo('`key`','max_article')->execute();
            if($res){
                echo json_encode(['success'=>true]);
            }else{
                echo json_encode(['success'=>false]);
            }
        }else{
            echo json_encode(['success'=>false]);
        }
    }
    public function updateReportProtectDate(){
        $reportProtectDate=Context::Post('reportProtectDate');
        if(!empty($reportProtectDate)){
            $res=$this->db->Name('xcx_setting')->update(['value'=>$reportProtectDate])->where_equalTo('`key`','report_protect_date')->execute();
            if($res){
                echo json_encode(['success'=>true]);
            }else{
                echo json_encode(['success'=>false]);
            }
        }else{
            echo json_encode(['success'=>false]);
        }
    }
}