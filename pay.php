<?php
// 引入配置
require_once 'config.php';

// 获取GET请求参数
$getParams = $_GET;
// 移除签名参数，以便重新计算
//unset($getParams['EdgeOrderSign']);
$EdgeOrderAmount = $getParams['EdgeOrderAmount'] ?? '';
$EdgeOrderCode = $getParams['EdgeOrderCode'] ?? '';
$EdgeOrderMethod = $getParams['EdgeOrderMethod'] ?? '';
$EdgeOrderTimestamp = $getParams['EdgeOrderTimestamp'] ?? '';

// 校验签名
if ($getParams['EdgeOrderSign'] != generateEdgeOrderSign($getParams,GOEDGE_PAY_KEY)) {
    //echo $getParams['EdgeOrderSign']. "*" .generateEdgeOrderSign($getParams,$configData['GoEdgePayKey']) ;
    die('签名验证失败');
}

// 准备GET请求参数，不包括sign_type和sign
$getUrlParams = [
    'pid' => PAY_ID,
    'out_trade_no' => $EdgeOrderCode,
    'notify_url' => URL .NOTIFY_URL,
    'return_url' => URL .RETURN_URL,
    'name' => 'Balance', // 根据您之前的指示，这里改为Balance
    'money' => $EdgeOrderAmount,
];

// 计算签名
uasort($getUrlParams, 'strcmp'); // 使用strcmp进行字符串比较，实现ASCII码排序
$sign = getSign($getUrlParams,PAY_KEY); // 拼接字符串和key后进行MD5加密，结果转小写

// 将签名添加到GET请求参数中
$getUrlParams['sign'] = $sign;

// 构建完整的请求URL
$requestUrl = PAY_URL ."submit.php" . '?' . http_build_query($getUrlParams);
 header('Location: ' . $requestUrl);
exit;


// 签名校验函数
function generateEdgeOrderSign($params, $secretKey) {
    // 检查必要的参数是否齐全
    if (!isset($params['EdgeOrderAmount'], $params['EdgeOrderCode'], $params['EdgeOrderMethod'], $params['EdgeOrderTimestamp'])) {
        return false; // 返回错误或者进行相应的错误处理
    }

    // 构建签名字符串
    $signStr = "EdgeOrderAmount=".$params['EdgeOrderAmount'];
    $signStr .='&EdgeOrderCode='.$params['EdgeOrderCode'];
    $signStr .='&EdgeOrderMethod='.$params['EdgeOrderMethod'];
    $signStr .='&EdgeOrderTimestamp='.$params['EdgeOrderTimestamp'];
    $signStr .='&'.$secretKey;

    // 使用SHA256算法计算签名，并转换为小写
    $sign = hash('sha256', $signStr);
    return strtolower($sign); // 返回签名
}
function getSign($param, $key) {
    ksort($param); // 对数组进行排序
    reset($param); // 重置数组指针到数组的第一个元素

    $signStr = ''; // 初始化签名字符串

    foreach ($param as $k => $v) {
        if ($k != "sign" && $k != "sign_type" && $v != '') {
            $signStr .= $k . '=' . $v . '&';
        }
    }
    $signStr = substr($signStr, 0, -1); // 移除最后一个'&'

    // 拼接密钥并进行MD5加密
    $sign = md5($signStr . $key);
    return $sign; // 返回签名
}
?>