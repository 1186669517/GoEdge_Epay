<?php
// 加载配置
require_once 'config.php';

// 从token.php获取refreshToken函数
require_once 'token.php';

// 获取GET请求参数
$getParams = $_GET;

// 校验并获取签名
if (!verifySign($getParams, PAY_KEY)) {
    die('签名验证失败');
}

// 获取访问令牌
$accessToken = refreshToken();
if (!$accessToken) {
    die('获取访问令牌失败');
}

// 准备API请求
$apiUrl = API_URL ."UserOrderService/finishUserOrder";
$postData = json_encode(["code" => $getParams['out_trade_no']]);

// 发送POST请求到API
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Edge-Access-Token: ' . $accessToken
]);

$response = curl_exec($ch);
curl_close($ch);

// 输出API响应
echo $response;

// 签名校验函数
function verifySign($params, $key) {
    $sign = $params['sign'];
    // 移除不参与签名的参数
    unset($params['sign'], $params['sign_type']);
    // 过滤空值
    $params = array_filter($params, function($value) { return !empty($value); });

    // 按照参数名ASCII码从小到大排序
    ksort($params);
    $string = '';
    foreach ($params as $k => $v) {
        $string .= $k . '=' . $v . '&';
    }
    // 移除最后一个'&'
    $string = rtrim($string, '&');
    // 拼接商户密钥并进行MD5加密
    $calculatedSign = md5($string . $key);
    return $sign === $calculatedSign;
}