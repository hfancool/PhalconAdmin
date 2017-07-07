{% include "common/header.volt" %}
<body>
    <div style="margin: 15px;">
        <fieldset class="layui-elem-field">
            <legend>管理员列表</legend>
            <div class="layui-field-box">
                <div>
                    <form method="get">
                        <div class="layui-input-inline">
                            <input type="text" name="user_name" placeholder="管理员名称" autocomplete="off" class="layui-input">
                        </div>
                        <button class="layui-btn" id="search">搜索</button>
                    </form>
                    <table class="site-table table-hover">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="selected-all"></th>
                            <th>序号</th>
                            <th>用户名</th>
                            <th>最后登录时间</th>
                            <th>最后登录ip</th>
                            <th>登录次数</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <!--内容容器-->
                        <tbody id="con">
                        {% set index = sequence + 1 %}
                        {% for item in lists %}
                            <tr>
                                <td><input type="checkbox" class="chBox" name="batchDel" value="{{item['user_id']}}"></td>
                                <td>{{index}}</td>
                                <td>{{item['user_name']}}</td>
                                <td>{{item['last_login']}}</td>
                                <td>{{item['last_ip']}}</td>
                                <td>{{item['logins']}}</td>
                                <td data-field="{{ item['user_id'] }}">
                                    <a href="javascript:;" class="layui-btn layui-btn-mini modify-perm">修改权限</a>
                                        {% if item['status'] == 1 %}
                                        <a href="javascript:;" class="layui-btn layui-btn-mini layui-btn-warm dealHandle">禁用</a>
                                        {% else %}
                                            <a href="javascript:;" class="layui-btn layui-btn-mini dealHandle">启用</a>
                                        {% endif %}
                                    <a href="javascript:;" data-id="1" data-opt="del" class="layui-btn layui-btn-danger layui-btn-mini delete">删除</a>
                                </td>
                            </tr>
                        {% set index += 1 %}
                        {% endfor %}
                        </tbody>
                    </table>
                    <div class="layui-btn-group">
                        <button id="handle" class="layui-btn layui-btn-disabled batchButton">禁用/启用</button>
                        <button id="batchDelete" class="layui-btn layui-btn-disabled batchButton">删除</button>
                    </div>
                    <!--分页容器-->
                    <div id="paged" style="float: right;"></div>
                </div>
            </div>
        </fieldset>
    </div>
</body>

<!--模板-->
<script type="text/javascript" src="/source/plugins/layui/layui.js"></script>
<script type="text/javascript">

    var pages = "{{totalPage}}";

    layui.config({
        base: '/source/js/'
    }).extend({
        common: 'common'
    }).use(['laypage','common','element','form'], function() {
        var $ = layui.jquery,
            laypage = layui.laypage,
            element = layui.element(),
            common = layui.common,
            form = layui.form();
            var page = common.getParams('page') || 1;

            laypage({
                cont : 'paged',
                pages: pages //总页数
                ,curr: page
                ,groups: 5 //连续显示分页数
                ,jump: function(obj, first){
                    if(!first){
                        location.href = '/admin/Administrators/adminList?page='+obj.curr;
                    }
                }
            });

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
                html += '<div style="width: 99%">';
                html += '<div id="perm" class="layui-tab layui-tab-card" style="width: 100%">';
                html += '<ul class="layui-tab-title">';
                $.each(objRes.title,function (index,cur) {
                    if(index == 0){
                        html += '<li class="layui-this">'+cur.text+'</li>';
                    }else{
                        html += '<li>'+cur.text+'</li>';
                    }
                });
                html += '</ul>';
                html += '<div class="layui-tab-content" style="height: 100px;">';
                
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
                    ,offset : '120px'
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
    });

</script>
<script type="text/javascript" src="/source/js/global.js"></script>
{% include "common/footer.volt" %}