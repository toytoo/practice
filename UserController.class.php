<?php
/**
 * @Author: anchen
 * @Date:   2016-03-25 16:06:19
 * @Last Modified by:   anchen
 * @Last Modified time: 2016-04-18 12:29:42
 */
namespace Home\Controller;
use Think\Controller;
// use Home\Model\UserModel;

class UserController extends Controller {

    //用户登录
    public function login() {
        if(IS_AJAX){
            $user = D('User');
            $id = $user -> login(
                I('user'),
                I('pass')
            );
            echo $id;
        }else{
            $this -> error('访问非法');
        }
    }
   
    //注册一条用户
    public function register (){
        if(IS_AJAX){
            // $user = M('User');
            // $data['username'] = I('user');
            // $data['password'] = sha1(I('pass'));
            // $data['email'] = I('email');
            // $data['createTime'] = Date();
            // $user->add($data);
            $user = D('User');
            //控制器接收数据然后传入到模型中
            $id = $user->register(
                I('user'),
                I('pass'),
                I('repass'),
                I('email')
            );
            echo $id;

        }else{
            $this->error('不能访问哟');
        }
    }

    //验证用户名是否被占用
    public function checkUserName (){
        if(IS_AJAX){
            $user = D('User');
            $id = $user->checkField(
                I('user'),
                'username'
            );
            //通过ajax接收来的id大于0时返回真 不然返回假
            echo $id>0 ? 'true' : 'false';
        }else{
            $this->error('非法访问');
        }
    }

    //验证邮箱是否被占用
    public function checkEmail (){
        if(IS_AJAX){
            $user = D('User');
            $id = $user->checkField(
                I('email'),
                'email'
            );
            echo $id>0 ? 'true' : 'false';
        }else{
            $this->error('非法访问');
        }
    }

    //验证码的验证
    public function checkVerify (){
        if(IS_AJAX){
            $user = D('User');
            $id = $user->checkField(
                I('verify'),
                'verify'
            );
            echo $id>0 ? 'true' : 'false';
        }else{
            $this->error('非法访问');
        }
    }
}