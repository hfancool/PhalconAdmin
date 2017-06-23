layui.use(['element', 'layer', 'tab'], function() {
    var element = layui.element(),
        $ = layui.jquery,
        layer = layui.layer;
    $('#chpwd').click(function(){

        var html = '<div class="layui-input-block" style="margin-left:0px;padding: 15px 4px 0 4px">';
            html += '<input type="password" name="password"  placeholder="请输入旧密码" autocomplete="on" class="layui-input"> </div>';
        /*验证旧密码是否正确*/
        layer.open({
            title: '身份验证',
            type: 1,
            area: ['240px', '160px'], //宽高
            content: html,
            btn: ['确&nbsp;&nbsp;定'],
            btnAlign: 'c',
            yes: function(index, layero){
                var password = $("input[name='password']").val();
                if($.trim(password) == ''){
                    $("input[name='password']").focus();
                    layer.tips('请输入旧密码', $("input[name='password']"),{tips: 3});
                    return;
                }
                $.ajax({
                    url:'/admin/index/checkAuth',
                    data : {oldPassword:password},
                    type : 'POST',
                    success : function (res) {
                        var resObj = eval('('+res+')');
                        if(resObj.code != 200){
                            layer.msg(resObj.message,{time: 800}, function(){});
                            return;
                        }
                        layer.close(index);
                        tab.tabAdd({title: '修改密码',href: '/admin/index/chpwd', icon : 'fa fa-gear',id:'chpwd'});
                    }
                });
            },
            success: function(layero, index){
                $("input[name='password']").focus();

                $(layero).keydown(function(event){
                    if(event.keyCode==13){
                        $(layero).find('.layui-layer-btn0').click();
                    }
                });

            }
        });

    });

    $('#logOut').click(function(){
        $.ajax({
            type : "GET",
            url  : "/admin/Index/logout",
            success : function (data) {
                var resObj = eval('('+data+')');
                if(resObj.code == 200){
                    window.location.href = "/admin/Index/index"
                }else{
                    console.log(data);
                }
            }
        });
    });

    $('#selected-all').click(function(){
        if($('#selected-all').get(0).checked){
            $("input[type='checkbox']").each(function (i) {
                if(i >0){
                    $(this).get(0).checked = true;
                }
            });
        }else{
            $("input[type='checkbox']").each(function (i) {
                if(i >0){
                    $(this).get(0).checked = false;
                }
            });
        }
    });

    $("input[type='checkbox']").click(function () {
        var flag = false;
        $(".chBox").each(function (i) {
            if( $(this).get(0).checked){
                flag = true;
            }
        });

        if(!flag){
            $('.batchButton').attr({'class':'layui-btn layui-btn-disabled batchButton'});
        }else{
            $('.batchButton').attr({'class':'layui-btn layui-btn-primary batchButton'});
        }

    });


});