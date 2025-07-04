<?php
/*
 * @Author: 苏晓晴 - www.toubiec.cn
 * @Date: 2025-07-04
 * @LastEditTime: 2025-07-04
 * @LastEditors: 苏晓晴
 * @Description: 歌曲解析实例
 * @FilePath: getMusicUrl.php
 */

require_once 'getMusicapi.php';

// 创建API实例
$api = new NeteaseMusicAPI();

// 从cookie.txt文件读取cookie
$cookieFile = 'cookie.txt'; // cookie文件路径
$cookies = $api->loadCookieFromFile($cookieFile);

$singleId = '26608738,212233'; // 歌曲ID 支持单ID/多ID 例如30500857  1315196858,1888667593,30500857
$quality = 'jymaster'; // 音质支持 standard, exhigh, lossless, hires, jyeffect, sky, jymaster
$rawResult = $api->getMusicUrl($singleId, $quality, $cookies);
foreach ($rawResult['data'] as $urls) {
    $temp = [];
    $temp['id'] = $urls['id'];
    $temp['url'] = str_replace("http://", "https://", $urls['url']);
    $temp['br'] = $urls['br'];
    $temp['level'] = $urls['level'];
    $temp['size'] = $urls['size'];
    $temp['md5'] = $urls['md5'];
    $list[] = $temp;
}
echo json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
?>