<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <title><?= $title ?></title>
        <!-- css 加载-->
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="format-detection" content="telephone=no">


        <link rel="stylesheet" href="/source/plugins/layui/css/layui.css" media="all" />
        <link rel="stylesheet" href="/source/css/login.css" />

        <link rel="stylesheet" href="/source/plugins/layui/css/layui.css" media="all" />
        <link rel="stylesheet" href="/source/css/global.css" media="all">
        <link rel="stylesheet" type="text/css" href="http://www.jq22.com/jquery/font-awesome.4.6.0.css">
    </head>
    <!--body begin-->
        <?= $this->getContent() ?>
    <!--body end-->
    <footer>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    </footer>
</html>
