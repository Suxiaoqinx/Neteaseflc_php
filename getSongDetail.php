<?php
/*
 * @Author: 苏晓晴 - www.toubiec.cn
 * @Date: 2025-07-04
 * @LastEditTime: 2025-07-04
 * @LastEditors: 苏晓晴
 * @Description: 歌曲信息解析实例
 * @FilePath: getSongDetail.php
 */

require_once 'getMusicapi.php';

// 创建API实例
$api = new NeteaseMusicAPI();

// 从cookie.txt文件读取cookie
$cookieFile = 'cookie.txt'; // cookie文件路径
$cookies = $api->loadCookieFromFile($cookieFile);

$singleId = '2006535009'; // 歌曲ID 支持单ID 例如2006535009

$rawResult = $api->getSongDetail($singleId, $cookies);

$songdetail['id'] = $rawResult['songs'][0]['id'] ?? ''; // 歌曲ID
$songdetail['name'] = $rawResult['songs'][0]['name'] ?? ''; // 歌曲名
$songdetail['album'] = $rawResult['songs'][0]['al']['name'] ?? ''; // 专辑
foreach ($rawResult['songs'][0]['ar'] as $artist) {
    $artists[] = $artist['name'];
}
$artistString = implode('/', $artists);
$songdetail['singer'] = $artistString ?? ''; // 歌手
$songdetail['picimg'] = $rawResult['songs'][0]['al']['picUrl'] ?? ''; // 专辑图片
$list['data'] = $songdetail;
echo json_encode($list, 480);
?>