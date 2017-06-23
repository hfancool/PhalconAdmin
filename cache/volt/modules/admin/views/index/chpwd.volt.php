<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <!-- css 加载-->

    <link rel="stylesheet" href="/source/plugins/layui/css/layui.css" media="all" />
    <link rel="stylesheet" href="/source/css/login.css" />
    <link rel="stylesheet" href="/source/css/global.css" media="all">
    <link rel="stylesheet" type="text/css" href="/source/css/font-awesome.4.6.0.css">

</head>

<div style="margin: 15px;">
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>修改密码</legend>
    </fieldset>

    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">管理员</label>
            <span class="layui-form-label" style="text-align: left">
                <?= $user_name ?>
            </span>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label">新密码</label>
            <div class="layui-input-inline">
                <input type="password" name="password" lay-verify="pass" placeholder="请输入密码" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">请填写6到12位密码</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">重复新密码</label>
            <div class="layui-input-inline">
                <input type="password" name="rePassword" lay-verify="rePassword"  placeholder="请重复新密码" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">请重复新密码</div>
        </div>

        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="sub">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="/source/plugins/layui/layui.js"></script>
<script>
    layui.use(['element', 'layer','form'], function() {
        var form = layui.form(),
                element = layui.element(),
                $ = layui.jquery,
                layer = layui.layer;

        //创建一个编辑器
        //自定义验证规则
        form.verify({
            pass: [/(.+){6,12}$/, '密码必须6到12位'],
            rePassword : function (value) {
                if(!/(.+){6,12}$/.test(value)){
                    return '密码必须6到12位';
                }
                var password = $("input[name='password']").val();
                if(password != value){
                    return '两次密码输入不一致';
                }
            }
        });

        //监听提交
        form.on('submit(sub)', function(data) {
            $.ajax({
                type : 'POST',
                url  : '/admin/index/chpwd',
                data : data.field,
                success : function (res) {
                    var resObj = eval('('+res+')');
                    if(resObj.code != 200){
                        layer.alert(resObj.message, {
                            title: '错误提示'
                        });

                        return;
                    }
                    layer.msg('修改成功');
                    $("input[name='password']").val('');
                    $("input[name='rePassword']").val('');
                }
            });

            return false;
        });
    });
</script>
</html>
