<?php
/*
 * @Author: 苏晓晴 - www.toubiec.cn
 * @Date: 2025-07-04
 * @LastEditTime: 2025-07-04
 * @LastEditors: 苏晓晴
 * @Description: 搜索音乐实例
 * @FilePath: getSearchMusic.php
 */

require_once 'getMusicapi.php';

// 创建API实例
$api = new NeteaseMusicAPI();

// 从cookie.txt文件读取cookie
$cookieFile = 'cookie.txt'; // cookie文件路径
$cookies = $api->loadCookieFromFile($cookieFile);

$search = '薛之谦'; // 搜索内容
$limit = 100; // 搜索数量
$offset = 0; // 搜索偏移量

$rawResult = $api->getSearchMusic($search, $limit, $offset, $cookies);
exit(json_encode($rawResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
?>