<?php
// config.php

// API 相关常量
//填写GoEdge API网关地址 请开启http Api 不要使用默认的grpc Api 端口
define('API_URL', 'http://1.1.1.1:8002/');
define('ACCESS_KEY_ID', 'DDDD');
define('ACCESS_KEY', 'SSSSS');
define('GOEDGE_PAY_KEY', 'AAAA');

// 支付网关相关常量
define('PAY_URL', 'https://QQQQQ.COM/');
define('PAY_ID', '1003');
define('PAY_KEY', 'JJJJJ');

// 中间件URL常量
//填写本站地址
define('URL', 'https://api.QQQQ.COM/');

// 填写GoEdge用户平台的地址 仅用作跳转
define('GOEDGE_URL', 'https://scdn.QQQQ.COM');
// 异步和同步通知的文件名称
define('NOTIFY_URL', 'notify_url.php');
define('RETURN_URL', 'return_url.php');