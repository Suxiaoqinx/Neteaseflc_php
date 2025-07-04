<?php
/*
 * @Author: 苏晓晴 - www.toubiec.cn
 * @Date: 2025-07-04
 * @LastEditTime: 2025-07-04
 * @LastEditors: 苏晓晴
 * @Description: 歌词解析实例
 * @FilePath: getLyric.php
 */

require_once 'getMusicapi.php';

// 创建API实例
$api = new NeteaseMusicAPI();

// 从cookie.txt文件读取cookie
$cookieFile = 'cookie.txt'; // cookie文件路径
$cookies = $api->loadCookieFromFile($cookieFile);

$singleId = '2006535009'; // 歌曲ID 支持单ID 例如2006535009

$rawResult = $api->getLyric($singleId, $cookies);
$lyric['lrc'] = $rawResult['lrc']['lyric'] ?? ''; // 歌词
$lyric['tlyric'] = $rawResult['tlyric']['lyric'] ?? ''; // 翻译歌词
$lyric['romalrc'] = $rawResult['romalrc']['lyric'] ?? ''; // 罗马音歌词
$lyric['klyric'] = $rawResult['klyric']['lyric'] ?? ''; // 滚动歌词
$list['data'] = $lyric;
echo json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>