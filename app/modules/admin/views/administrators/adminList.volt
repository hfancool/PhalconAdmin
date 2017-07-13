{% include "common/header.volt" %}
<body>
    <div style="margin: 15px;">
        <fieldset class="layui-elem-field">
            <legend>管理员列表</legend>
            <div class="layui-field-box">
                <div>
                    <div class="layui-btn-group">
                        <button class="layui-btn" onclick="return false;" id="add">添加</button>
                        <!--筛选条件按钮-->
                        <button class="layui-btn layui-btn-primary" onclick="return false;" id="search-filter">
                            <i class="layui-icon" pointer="up">&#xe602;</i>
                            <span>筛选条件</span>
                        </button>
                    </div>

                    <!--筛选条件内容框-->
                    <fieldset class="layui-elem-field" id="search-filter-content">
                        <div class="layui-field-box">
                            <form method="get">
                                <div class="layui-input-inline">
                                    <input type="text" name="user_name" placeholder="管理员名称" autocomplete="off" class="layui-input">
                                </div>
                                <button class="layui-btn" id="search">搜索</button>
                            </form>
                        </div>
                    </fieldset>

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
                                    {% if item['user_name'] == 'admin' %}
                                    <span>超级管理员</span>
                                    {% else %}
                                    <a href="javascript:;" class="layui-btn layui-btn-mini modify-perm">修改权限</a>
                                        {% if item['status'] == 1 %}
                                        <a href="javascript:;" class="layui-btn layui-btn-mini layui-btn-warm dealHandle">禁用</a>
                                        {% else %}
                                            <a href="javascript:;" class="layui-btn layui-btn-mini dealHandle">启用</a>
                                        {% endif %}
                                    <a href="javascript:;" data-id="1" data-opt="del" class="layui-btn layui-btn-danger layui-btn-mini delete">删除</a>
                                    {% endif %}
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
    }).use(['laypage','common'], function() {
        var $ = layui.jquery,
                laypage = layui.laypage,
                common = layui.common;
        var page = common.getParams('page') || 1;

        laypage({
            cont: 'paged',
            pages: pages //总页数
            , curr: page
            , groups: 5 //连续显示分页数
            , jump: function (obj, first) {
                if (!first) {
                    location.href = '/admin/Administrators/adminList?page=' + obj.curr;
                }
            }
        });

    });
</script>
<script type="text/javascript" src="/source/js/admin.js"></script>
<script type="text/javascript" src="/source/js/global.js"></script>
{% include "common/footer.volt" %}