/* 
* @Author: anchen
* @Date:   2016-03-12 23:21:22
* @Last Modified by:   anchen
* @Last Modified time: 2016-04-18 12:31:03
*/
$(function(){
    //取得一个随机数
    var rand = Math.floor(Math.random() * 9) + 1;
    // alert(rand);
    //设置背景图片随机
    $("body").css('background','url('+ThinkPHP['IMG']+'/login_bg'+rand+'.jpg)').css('background-size','100%');

    //验证码刷新
    $('.changeimg').click(function (){
        var verifyimg = $('.verifyimg').attr('src');
        $(".verifyimg").attr("src",verifyimg+'?random='+Math.random());
    });

    //自定义验证规则，用户名不能包含@防止与邮箱冲突
    $.validator.addMethod('testAt', function(value, element){
        var text = /^[^@]+$/i;
        return this.optional(element) || (text.test(value));
    }, '存在@');

    //登录以及对登录进行验证
    $('#login').validate({
        submitHandler : function (form){
            $(form).ajaxSubmit({
                url : ThinkPHP['MODULE'] + '/User/login',
                type : 'POST'
            });
        },
    });

    //注册窗口以及对注册的前台验证
    $('#register').dialog({
        width : 400,
        height : 480,
        modal: true,//给屏幕覆盖一个保护罩，用户不能进行其它操作
        title : '微博注册',
        autoOpen : false,
        resizable : false,
        closeText: '关闭',
        buttons : [{
            text : '注 册',
            click : function(e){
                $(this).submit();//提交表单
            },
        }],
    }).validate({//对表单进行验证(注册)
        submitHandler : function (form){//这是一个表单提交句柄，为一回调函数，带一个参数form
            $(form).ajaxSubmit({
                url : ThinkPHP['MODULE'] + '/User/register',
                type : 'POST' , 
                //点击注册按钮之后 数据提交中提示框会提示
                beforeSubmit : function(){
                    $('#loading').dialog('open');
                    $('#register').dialog('widget').find('button').eq(1).button('disable');
                },
                //注册成功之后的回调函数，当注册成功是responseText=true,进行对注册成功之后的一系列操作
                success : function(responseText){
                    if(responseText){
                        $('#loading').css('background', 'url('+ThinkPHP['IMG']+'/001_06.png) no-repeat 10px center').html('注册成功');
                        $('#register').dialog('widget').find('button').eq(1).button('enable');
                        //延迟1S进行如下操作，增加用户体验
                        setTimeout(function() {
                            $('#loading').dialog('close');
                            $('#register').dialog('close');
                            $('#register span.star').removeClass('succ');
                            $('#register').resetForm();
                            $('#loading').css('background', 'url('+ThinkPHP['IMG']+'/loading5.gif) no-repeat 10px center').html('数据提交中...');
                        }, 1000);
                    }
                },              
            });
        },

        //动态的添加一个<p>元素，用来显示错误信息
        errorPlacement : function(error, element) {
            var p = $("<p />").append(error);
            p.appendTo(element.parent());
        },

        //当填写表单错误时，边框颜色为红色
        highlight :function(element, errorClass){
            $(element).css('border', '1px solid red');
            $(element).parent().find('span').removeClass('succ');
        },

        //填写正确回复默认
        unhighlight :function(element, errorClass){
            $(element).css('border', '1px solid #ccc');
            $(element).parent().find('span').addClass('succ');
        },
        //用validate插件规则进行验证（前台进行验证）
        rules : {
            user : {
                //验证非空
                required : true,
                minlength : 2,
                maxlength : 20,
                testAt : true,
                //自定义ajax验证，与数据库中的数据比较，看账号是否存在
                remote : {
                    type : 'POST',
                    url : ThinkPHP['MODULE'] + '/User/checkUserName',
                }
            },
            pass : {
                required : true,
                minlength : 6,
                maxlength : 30,
            },
            repass : {
                required : true,
                equalTo : '#pass',
            },
            email : {
                required : true,
                email : true,
                remote : {
                    type : 'POST',
                    url : ThinkPHP['MODULE'] + '/User/checkEmail',
                }
            },
            verify : {
                required : true,
                remote : {
                    type : 'POST',
                    url : ThinkPHP['MODULE'] + '/User/checkVerify',
                }
            },
        },

        //自定义错误信息提示
        messages : {
            user: {
                required : '账号不能为空',
                minlength : $.format('账号长度至少是{0}位'),
                maxlength : $.format('账号长度不能超过{0}位'),
                testAt : '账号中不能包含@符号',
                remote : '账号已存在'
            },
            pass : {
                required : '密码不能为空',
                minlength : $.format('密码长度至少是{0}位'),
                maxlength : $.format('密码长度不能超过{0}位'),
            },
            repass : {
                required : '请输入重复密码',
                equalTo : '重复密码与密码不相等',
            },
            email : {
                required : '请输入邮箱地址',
                email : '请输入正确的邮箱格式',
                remote : '邮箱已被注册'
            },
            verify : {
                required : '请输入验证码',
                remote : '验证码不正确'
            },
        }
    });
    //提交之后的弹框
    $('#loading').dialog({
        width : 180,
        height : 40,
        modal: true,//给屏幕覆盖一个保护罩，用户不能进行其它操作
        autoOpen : false,
        resizable : false,
        closeOnEscape : false,//防止按esc键退出
        draggable : false,
    }).parent().find('.ui-widget-header').hide();

    //点击注册弹窗
    $('#reg_link').click(function () {
        $('#register').dialog('open');
    });
});