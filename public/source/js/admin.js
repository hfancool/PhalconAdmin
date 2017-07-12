/**
 * Created by Administrator on 2017/7/12.
 */

layui.use(['element','form'], function() {
    var $ = layui.jquery,
        element = layui.element(),
        form = layui.form();


    $('.delete').click(function () {
        /*获取管理员用户名*/
        var user_name = $(this).parent('td').siblings('td').eq(2).html();
        var that = $(this);
        layer.confirm('确定删除管理员 <strong>'+user_name+'</strong> 吗 ？', {
            offset: '200px',
            btn: ['确定','取消'] //按钮
        }, function(){
            location.href = '/admin/Administrators/delete/'+that.parent('td').attr('data-field');
        });
    });

    $('.dealHandle').click(function () {
        var user_id = $(this).parent('td').attr('data-field');
        var that = $(this);
        $.ajax({
            url : '/admin/Administrators/handle/'+user_id,
            type : 'GET',
            success : function (res) {
                var objRes = eval('('+res+')')
                if(objRes.code == 200){
                    if(objRes.status == 1){
                        that.html('禁用');
                        that.addClass('layui-btn-warm');
                    }else{
                        that.html('启用');
                        that.removeClass('layui-btn-warm');
                    }
                }
            }

        });
    });

    /**
     * 启用、禁用
     */
    $('#handle').click(function () {

        if($(this).hasClass('layui-btn-disabled')){
            return;
        }

        var checkBox = $("input[name='batchDel']");
        var arrDel = [];
        var arrObj = [];
        checkBox.each(function (index) {
            if($(this).get(0).checked){
                arrDel.push($(this).val());
                arrObj.push(this);
            }
        });

        layer.confirm('确定修改选中的管理员状态吗 ？', {
            offset: '200px',
            btn: ['确定','取消'] //按钮
        }, function(index){
            console.log(arrDel);

            $.ajax({
                url  : '/admin/Administrators/bacthHandle',
                type : 'POST',
                data : {id:arrDel.join()},
                success : function (res) {
                    var objRes = eval('('+res+')');
                    if(objRes.code != 200){
                        layer.alert(objRes.message);
                    }else{
                        layer.closeAll();
                        /*修改状态*/
                        for(var i = 0 ; i<arrObj.length;i++){
//                                    $(arrObj[i]).get(0).checked = false;
                            var that = $(arrObj[i]).parent('td').siblings().eq(5).find('.dealHandle');
                            if(that.hasClass('layui-btn-warm')){
                                that.removeClass('layui-btn-warm');
                                that.html('启用');
                            }else{
                                that.addClass('layui-btn-warm');
                                that.html('禁用');
                            }
                        }
                    }
                }
            });

        });

    });
    /**
     * 批量删除
     */
    $('#batchDelete').click(function () {

        if($(this).hasClass('layui-btn-disabled')){
            return;
        }

        var checkBox = $("input[name='batchDel']");
        var arrDel = [];
        checkBox.each(function (index) {
            if($(this).get(0).checked){
                arrDel.push($(this).val());
            }
        });

        layer.confirm('确定删除选中的管理员吗 ？', {
            offset: '200px',
            btn: ['确定','取消'] //按钮
        }, function(){
            location.href = '/admin/Administrators/delete/'+arrDel.join();
        });

    });

    /*权限管理*/
    $('.modify-perm').click(function () {
        layer.load();
        /*获取当前管理员的id*/
        var admin_id = $(this).parent().attr('data-field');
        var admin_name = $(this).parents('tr').find('td').eq(2).html();
        $.get('/admin/Administrators/adminPerm/'+admin_id, function (res) {
            var objRes = eval('('+res+')');
            var html = "";
            html += '<div style="width: 100%">';
            html += '<div id="perm" class="layui-tab layui-tab-card" style="width: 99%;margin:5px auto">';
            html += '<ul class="layui-tab-title">';
            $.each(objRes.title,function (index,cur) {
                if(index == 0){
                    html += '<li class="layui-this">'+cur.text+'</li>';
                }else{
                    html += '<li>'+cur.text+'</li>';
                }
            });
            html += '</ul>';
            html += '<div class="layui-tab-content" style="height: 370px;">';

            $.each(objRes.perm, function (index, cur) {
                if(index == 0){
                    html += '<div class="layui-tab-item layui-show">';
                }else{
                    html += '<div class="layui-tab-item">';
                }
                html += '<div class="layui-form">';
                $.each(cur.text, function (i,item) {
                    html += '<div style="display: inline-block">';
                    html += '<input type="checkbox" lay-filter="changePerm" admin_id="'+admin_id+'" data-field="'+item.id+'" lay-skin="primary" title="'+item.name+'" '+($.inArray(item.id,objRes.hadPerms)<0 ?  "" :"checked=\"\"")+'>';
                    html += '</div>';
                });
                html += '</div>';
                html += '</div>';
            });
            html += '</div>';
            html += '</div>';
            html += '</div>';
            layer.closeAll('loading');

            layer.open({
                type: 1 //Page层类型
                ,area: ['900px', '500px']
                ,title: '管理员权限 -- 管理员：'+admin_name
                ,scrollbar:false
                ,shade: 0.6 //遮罩透明度
                ,maxmin: true //允许全屏最小化
                ,anim: 0 //0-6的动画形式，-1不开启
                ,content: html
                ,success: function () {
                    form.on('checkbox(changePerm)', function(data){
                        var that = $(data.elem);
                        var perm_id  = that.attr('data-field');
                        var admin_id = that.attr('admin_id');
                        var flag     = that.get(0).checked;
                        $.get('/admin/Administrators/changePerm',{admin_id:admin_id,perm_id:perm_id,flag:flag});
                    });
                    $.getScript('/source/plugins/layui/lay/modules/form.js');
                    element.init();    //表单元素重新绑定事件、样式
                }
            });
        });
    });

    $('#add').click(function () {
        var html = '';
        html += '<form class="layui-form" style="margin-top: 20px">';
        html += '<div class="layui-form-item">';
        html += '<label class="layui-form-label">管理员名称</label>';
        html += '<div class="layui-input-inline">';
        html += '<input type="text" name="user_name" lay-verify="required"  autocomplete="off" placeholder="请输入管理员名称" class="layui-input">';
        html += '</div>';
        html += '<div class="layui-form-mid layui-word-aux">不能输入汉字</div>';
        html += '</div>';
        html += '<div class="layui-form-item">';
        html += '<label class="layui-form-label">管理员密码</label>';
        html += '<div class="layui-input-inline">';
        html += '<input type="password" name="password"  lay-verify="required" placeholder="请输入初始密码"  class="layui-input">';
        html += '</div>';
        html += '<div class="layui-form-mid layui-word-aux">初始密码</div>';
        html += '</div>';
        html += '<div class="layui-form-item">';
        html += '<label class="layui-form-label">启用/禁用</label>';
        html += '<div class="layui-input-block">';
        html += '<input type="checkbox" checked="" name="choice" lay-skin="switch" lay-filter="switchTest">';
        html += '</div>';
        html += '</div>';
        html += '<div class="layui-form-item">';
        html += '<div class="layui-input-block">';
        html += '<button class="layui-btn" onclick="return false;" id="add-admin-sub">立即提交</button>';
        html += '<button type="reset" class="layui-btn layui-btn-primary">重置</button>';
        html += '</div>';
        html += '</div>';
        html += '</form>';

        layer.open({
            type: 1 //Page层类型
            ,area: ['400px', '300px']
            ,title: '新增管理员'
            ,scrollbar:false
            ,shade: 0.6 //遮罩透明度
            ,anim: 0 //0-6的动画形式，-1不开启
            ,content: html
            ,success: function () {

                $.getScript('/source/plugins/layui/lay/modules/form.js');
                element.init();    //表单元素重新绑定事件、样式

                $('#add-admin-sub').click(function () {
                    /*获取用户名*/
                    var user_name = $(this).parents('form').find("input[name='user_name']").val();
                    var password  = $(this).parents('form').find("input[name='password']").val();
                    var choice    = $(this).parents('form').find("input[name='choice']").get(0).checked;
                    if($.trim(user_name) == ''){
                        layer.msg('管理员用户名不能为空', function(){});
                        $(this).parents('form').find("input[name='user_name']").focus();
                        return;
                    }else if(!new RegExp("^[a-zA-Z0-9_]+$").test(user_name)){
                        layer.msg('输入错误', function(){});
                        $(this).parents('form').find("input[name='user_name']").focus();
                        return;
                    }

                    if($.trim(password) == ''){
                        $(this).parents('form').find("input[name='password']").focus();
                        layer.msg('请输入初始密码', function(){});
                        return;
                    }
                    layer.closeAll();
                    layer.load();
                    $.ajax({
                        type : 'POST',
                        url  : '/admin/Administrators/addAdmin',
                        data : {user_name: $.trim(user_name),password: $.trim(password),choice:choice},
                        success : function (res) {
                            layer.closeAll();
                            var objRes = eval('('+res+')');
                            if(objRes.code != 200){
                                layer.msg(objRes.message, function(){});
                                return;
                            }
                            layer.alert('添加成功', {icon: 6},function(){ location.reload();});

                        }
                    });
                });


            }
        });
    });
});