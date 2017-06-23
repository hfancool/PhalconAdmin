{% include "common/header.volt" %}
<body class="beg-login-bg">
    <div class="beg-login-box">
        <header>
            <h1>后台登录</h1>
        </header>
        <div class="beg-login-main">
            <form action="" class="layui-form" method="post">
                <input name="__RequestVerificationToken" type="hidden" value="fkfh8D89BFqTdrE2iiSdG_L781RSRtdWOH411poVUWhxzA5MzI8es07g6KPYQh9Log-xf84pIR2RIAEkOokZL3Ee3UKmX0Jc8bW8jOdhqo81" />
                <div class="layui-form-item">
                    <label class="beg-login-icon">
                    <i class="layui-icon">&#xe612;</i>
                </label>
                    <input type="text" name="userName" lay-verify="userName" autocomplete="off" placeholder="这里输入登录名" value="{{user_name}}" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <label class="beg-login-icon">
                    <i class="layui-icon">&#xe642;</i>
                </label>
                    <input type="password" name="password" lay-verify="required" autocomplete="off" placeholder="这里输入密码" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <div class="beg-pull-left beg-login-remember">
                        <label>记住帐号？</label>
                        <input type="checkbox" name="rememberMe" value="true" lay-skin="switch" checked title="记住帐号">
                    </div>
                    <div class="beg-pull-right">
                        <button class="layui-btn layui-btn-primary" lay-submit lay-filter="login">
                        <i class="layui-icon">&#xe650;</i> 登录
                    </button>
                    </div>
                    <div class="beg-clear"></div>
                </div>
            </form>
        </div>
        <footer>
            <p>copyright © www.3tiworld.com</p>
        </footer>
    </div>
    <script type="text/javascript" src="/source/plugins/layui/layui.js"></script>
    <script>
        layui.use(['layer', 'form' , 'element'], function() {
            var layer = layui.layer,
                $ = layui.jquery,
                form = layui.form();

            /*表单验证*/
            form.verify({
                userName : function (value) {
                    if(value == ''){
                        return '用户名不能为空'
                    }
                }
            });

            form.on('submit(login)', function (data) {
                /*登录加载*/
                layer.msg('正在登录...', {icon: 16,shade: 0.01,time: 2000});
                $.ajax({
                    url : "/admin/Index/index",
                    data : {userName:data.field.userName,password:data.field.password,rememberMe:data.field.rememberMe},
                    type : 'POST',
                    success : function (res) {
                        var objRes = eval('('+res+')');
                        if(objRes.code == 200){
                            /*登录成功*/
                            window.location.href="/admin/Index/loginSuccess";
                        }else{
                            layer.msg(objRes.message, {icon: 5,time: 2000});
                        }
                    }
                });
                return false;
            })
        });
    </script>
</body>
{% include "common/footer.volt" %}
