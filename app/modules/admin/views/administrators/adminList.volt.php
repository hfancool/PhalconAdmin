<div style="margin: 15px;">
    <fieldset class="layui-elem-field">
        <legend>管理员列表</legend>
        <div class="layui-field-box">
            <div>
                <form method="get">
                    <div class="layui-input-inline">
                        <input type="text" name="user_name" required  lay-verify="required" placeholder="管理员名称" autocomplete="off" class="layui-input">
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
                    <?php $index = $sequence + 1; ?>
                    <?php foreach ($lists as $item) { ?>
                        <tr>
                            <td><input type="checkbox" name=""></td>
                            <td><?= $index ?></td>
                            <td><?= $item['user_name'] ?></td>
                            <td><?= $item['last_login'] ?></td>
                            <td><?= $item['last_ip'] ?></td>
                            <td><?= $item['logins'] ?></td>
                            <td>
                                <a href="javascript:;" class="layui-btn layui-btn-mini">修改权限</a>
                                <a href="javascript:;" class="layui-btn layui-btn-mini layui-btn-warm">禁用</a>
                                <a href="delete/<?= $item['user_id'] ?>" data-id="1" data-opt="del" class="layui-btn layui-btn-danger layui-btn-mini">删除</a>
                            </td>
                        </tr>
                    <?php $index += 1; ?>
                    <?php } ?>
                    </tbody>
                </table>
                <!--分页容器-->
                <div class="layui-btn-group">
                </div>
                <div id="paged" style="float: right;"></div>
            </div>
        </div>
    </fieldset>
</div>
<!--模板-->
<script type="text/javascript" src="/source/plugins/layui/layui.js"></script>
<script type="text/javascript">

    var pages = "<?= $totalPage ?>";

    layui.config({
        base: '/source/js/'
    }).extend({
        common: 'common'
    }).use(['laypage','common'], function() {
        var $ = layui.jquery,
            laypage = layui.laypage,
            common = layui.common      ;
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


    });

</script>
<script type="text/javascript" src="/source/js/global.js"></script>