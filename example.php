<?php
$url_gas = 'https://script.google.com/macros/s/xxxxxxxxxxxxxxxx/exec';
//Apps Script ウェブアプリURL

$post_data = [
    'name' => replaceFormulaSign(filter_input(INPUT_POST, 'name')),
    'address' => replaceFormulaSign(filter_input(INPUT_POST, 'address')),
    'tel' => filter_input(INPUT_POST, 'tel'),
    'email' => escapeFormulaSign(filter_input(INPUT_POST, 'email')),
    'amount' => filter_input(INPUT_POST, 'amount'),
    'post_at' => (new DateTime())->format('Y/m/d H:i:s'),
    'ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'],
    'useragent' => $_SERVER['HTTP_USER_AGENT']
];

$curl = curl_init($url_gas);
curl_setopt_array(
    $curl,
    [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($post_data),
        CURLOPT_FOLLOWLOCATION => true//これが無いと Moved Temporarily が返ってくる
        //CURLOPT_SSL_VERIFYPEER => false, 必要があれば
    ]
);
$response = curl_exec($curl);
curl_close($curl);

$response_array = json_decode($response, true);
if ($response_array['result'] === 'success') {
    //成功した時の処理
} else {
    //失敗した時の処理
}

function replaceFormulaSign($str)
{
    $search = ['=', '+'];
    $replace = ['＝', '＋'];
    return str_replace($search, $replace, $str);
}

function escapeFormulaSign($email)
{
    $pattern = '/\A([=+])/';
    return preg_replace($pattern, '\'$1', $email);
}
