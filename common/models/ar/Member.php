<?php

namespace common\models\ar;

use yii\db\ActiveRecord;

class Member extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%member}}';
    }

    public function rules()
    {
        return [
            [['balance','parent_id','g_id','h_id','member_grade'],'integer'],
            [['member_phone','member_pwd','member_realname','birthday','id_card','member_note','sex','email','is_credits','add_time','last_time','status'],'string'],
        ];
    }

    //通过手机号码和g_id判断是否存在
    /*
     * $member_phone 注册的手机号码
     * $g_id         酒店id或集团id
     *
     * */
    public function isexistsmember($member_phone,$g_id){
        return $this->find()
                     ->where("member_phone=:member_phone and g_id=:g_id",[":member_phone"=>$member_phone,":g_id"=>$g_id])// and status=:status   ,':status'=>'1'
                     ->one();
    }

    //通过member_id判断会员是否存在
    public function existsmember($member_id){
        return $this->findOne($member_id);
    }

    //新增数据
    public function add($post){
        //判断之前是否是软删除过，如果是将软删除状态改成正常就行，不需要再添加
        /*$result=$this->find()
                     ->where('member_phone=:member_phone and g_id=:g_id',[':member_phone'=>$post['member_phone'],':g_id'=>$post['g_id']])
                     ->one();
        if($result){
            $result->member_pwd=md5($post["member_pwd"]);
            $result->member_realname=$post["member_realname"];
            $result->add_time=date("Y-m-d H:i:s");
            $result->status='1';
            if($result->save()){
                return $result;
            }else{
                return false;
            }
        }else{*/
            $this->g_id=$post["g_id"];
            $this->h_id=$post["h_id"];
            $this->member_phone=(string)$post["member_phone"];
            $this->member_pwd=md5($post["member_pwd"]);
            $this->member_realname=$post["member_realname"];
            $this->add_time=date("Y-m-d H:i:s");
            //$this->parent_id=$post["p_id"];
            if($this->save()){
                return $this;
            }else{
                return false;
            }
        //}
    }

    //更新最后的登入时间
    public function update_last_time($member_id){
        $result=$this->findOne($member_id);
        $result->last_time=date("Y-m-d H:i:s");
        return $result->save();
    }

    //更新会员表会员等级id
    public function update_member_grade($member_id,$member_grade){
        $result=$this->findOne($member_id);
        $result->member_grade=$member_grade;
        return $result->save();
    }

    //验证登入
    public function verify($member_phone,$member_pwd,$g_id){
        return $this->find()
                     ->where("member_phone=:member_phone and member_pwd=:member_pwd and g_id=:g_id and status=:status",[":member_phone"=>$member_phone,":member_pwd"=>$member_pwd,":g_id"=>$g_id,':status'=>'1'])
                     ->one();
    }

    //获取所有用户
    public function allmember(){
        return $this->find()->all();
    }

    //修该会员姓名
    public function editname($user_name,$member_id){
        $result=$this->findOne($member_id);
        $result->member_realname=$user_name;
        return $result->save();
    }

    //修改会员密码
    public function editpwd($old_pwd,$member_pwd,$member_id){
        $pwd=md5($old_pwd);
        $new_pwd=md5($member_pwd);

        $member=$this->existsmember($member_id);

        if($pwd!=$member->member_pwd){
            return "pwd_error";
        }else{
            $member->member_pwd=$new_pwd;

            if($member->save()){
                return "success";
            }else{
                return "error";
            }
        }
    }

    //修改邮箱
    public function editemail($member_email,$member_id){
        $result=$this->findOne($member_id);
        $result->email=$member_email;
        return $result->save();
    }

    //修改性别和生日
    public function editsex($sex,$birthday,$member_id){
        $result=$this->findOne($member_id);

        $result->sex=$sex;
        $result->birthday=$birthday;
        return $result->save();
    }

    //忘记密码时设置
    public function setpwd($g_id,$member_phone,$member_pwd){
        $result=$this->find()
                     ->where("member_phone=:member_phone and g_id=:g_id",[":member_phone"=>$member_phone,":g_id"=>$g_id])
                     ->one();
        $result->member_pwd=$member_pwd;
        return $result->save();
    }

    //修改会员身份证
    public function editidcard($id_card,$member_id){
        $result=$this->findOne($member_id);

        $result->id_card=$id_card;
        return $result->save();
    }

}

?>