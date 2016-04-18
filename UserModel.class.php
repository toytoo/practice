<?php
/**
 * @Author: anchen
 * @Date:   2016-03-25 16:33:20
 * @Last Modified by:   anchen
 * @Last Modified time: 2016-04-18 15:09:43
 */
namespace Home\Model;
use Think\Model;

class UserModel extends Model {
    //开启批量验证
    // protected $patchValidate = true;


    //后台自动验证，增加安全性
    protected $_validate = Array(
        //存在user字段就验证它的长度是2-20之间且不能包含@正则表达式进行验证，-1=>'用户名格式错误'
        array('username', '/^[^@]{2,20}$/i', -1, self::EXISTS_VALIDATE),
        //验证密码的长度, -2=>'密码长度不正确'
        array('password', '6,20', -2, self::EXISTS_VALIDATE, 'length'),
        //重复验证密码，验证两个字段是否相等, -3=>'重复密码与密码不匹配'
        array('repassword', 'password', -3, self::EXISTS_VALIDATE, 'confirm'),
        //验证邮箱的格式, -4=>'邮箱格式不正确'
        array('email', 'email', -4, self::EXISTS_VALIDATE),

        //验证用户名的唯一性, -5=>'用户名已被占用'
        array('username', '', -5, self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        //验证邮箱的唯一性, -6=>'邮箱已注册'
        array('email', '', -6, self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        //用户名不能为空, -7=>'用户名不能为空'
        array('username', 'require', -7, self::EXISTS_VALIDATE ),
        //验证验证码是否正确， -8=>验证码输入不正确
        array('verify', 'check_verify', -8, self::EXISTS_VALIDATE,
'function'),

        //单独验证登录时的用户名, -9
        array('login_user', '2,50', -9, self::EXISTS_VALIDATE, 'length'),
        //当登录名不是用邮件登录时
        array('login_user', 'email', 'noemail', self::EXISTS_VALIDATE),
    );

    //自动完成加密密码和注册时间功能
    protected $_auto = Array(
        // 对password字段在新增和编辑的时候使md5函数处理
        array('password', 'md5', self::MODEL_BOTH, 'function'),
        // 对createTime字段在更新的时候写入当前时间戳
        array('createTime', 'time', self::MODEL_INSERT, 'function')
    );

    //从User控制器接收数据，进行数据的处理完成登录的功能
    public function login ($user, $pass){
        $data = Array(
            'login_user' => $user,
            'password' => $pass,
        );

        //查询条件
        $condition = array();

        if($this->create($data)){
        //采用邮箱登录
            $condition['email'] = $user;
            $username = $this -> field('id,password')-> where($condition) -> find();
            // print_r($username);这条打印出来的是根据用户名找到的一条id和密码
            
            if($username['password'] == md5($pass)){
                return $username['id'];//验证正确返回id
            }else{
                return -10; //密码错误
            }
        }else{
            if($this -> getError() == 'noemail'){
                //采用的是用户名登录方法
                $condition['username'] = $user;
                $username = $this -> where($condition) -> find();

                if($username['password'] == md5($pass)){
                    return $username['id'];//验证正确返回id
                }else{
                    return -10; //密码错误
                }
            }else{
                return $this -> getError();
            }
        }
    }
    
    //从User控制器接收数据，进行数据的处理完成注册的功能
    public function register ($user, $pass, $repass, $email){
        $data = Array(
            'username' => $user,
            'password' => $pass,
            'repassword' => $repass,
            'email' => $email,
            // 'createTime' => time(),
        );
        if ($this->create($data)) {
            $uid = $this->add();
            return $uid ? $uid : 0;
        } else {
            return $this->getError(); 
        }
    }

    //从checkusername/checkEmail/checkVerify方法接收数据，验证用户名和邮箱是否被占用以及验证码是否输入正确
    public function checkField ($field, $type){
        $data = array();
        switch ($type) {
            case 'username':
                $data['username'] = $field;
                break;
            
            case 'email':
                $data['email'] = $field;
                break;

            case 'verify':
                $data['verify'] = $field;
                break;

            default:
                return 0;               
        }
        //如果能注册，返回1，不能注册返回错误代码
        return $this->create($data) ? 1 : $this->getError();
        // $this->create($data) ? 1 : $this->getError();
    }
    

    
}