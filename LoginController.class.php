<?php
/**
 * @Author: anchen
 * @Date:   2016-03-12 22:34:23
 * @Last Modified by:   anchen
 * @Last Modified time: 2016-03-29 14:50:09
 */
namespace Home\Controller;
use Think\Controller;

class LoginController extends Controller{
    public function index(){
        $this->display();
    }

    //生成验证码
    public function verify(){ 
        //清楚缓冲区，不然验证码显示不出来      
        ob_clean();
         
        $Verify = new \Think\Verify();
        $Verify->imageW = 165;
        $Verify->imageH = 40;
        $Verify->fontSize = 20;
        $Verify->length   = 4;
        $Verify->entry(1);
    }
}