<?php


namespace app\model;


use think\Model;

class User extends Model
{
    protected $table ='9h_user';
    protected $autoWriteTimestamp='int';
    //获取用户所有群
   public function groups(){
       return $this->hasMany(GroupUser::class,'user_id');
   }

}