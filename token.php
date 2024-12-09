<?php

function refreshToken() {
    // 定义存储token的PHP文件路径
    $tokenFilePath = 'access.php';

    // 尝试从access.php文件中读取token
    if (file_exists($tokenFilePath)) {
        include $tokenFilePath;
        if (isset($tokenData) && isset($tokenData['expiresAt']) && $tokenData['expiresAt'] > time()) {
            // Token未过期，直接返回
            return $tokenData['token'];
        }
    }

    // 引入配置
    require_once 'config.php';

    // 准备POST请求数据
    $postData = json_encode([
        "type" => "admin",
        "accessKeyId" => ACCESS_KEY_ID,
        "accessKey" => ACCESS_KEY
    ]);

    // 发送POST请求获取新token
    $ch = curl_init(API_URL."APIAccessTokenService/getAPIAccessToken");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    // 解析返回的JSON数据
    $responseData = json_decode($response, true);

    // 检查是否成功获取新token
    if (isset($responseData['code']) && $responseData['code'] == 200 && isset($responseData['data'])) {
        // 更新access.php文件
        $newTokenData = "<?php\n";
        $newTokenData .= "\$tokenData = " . var_export([
            "token" => $responseData['data']['token'],
            "expiresAt" => $responseData['data']['expiresAt']
        ], true) . ";\n";
        $newTokenData .= "?>";

        $result = file_put_contents($tokenFilePath, $newTokenData);
        if ($result === false) {
            die("Failed to write to access.php");
        }
        // 返回新token
        return $responseData['data']['token'];
    } else {
        // 获取token失败，返回false
        return false;
    }
}

// 使用示例
$newToken = refreshToken();
if ($newToken !== false) {
    echo "Token refreshed successfully";
} else {
    echo "Failed to refresh token.";
}