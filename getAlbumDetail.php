<?php
/*
 * @Author: 苏晓晴 - www.toubiec.cn
 * @Date: 2025-07-04
 * @LastEditTime: 2025-07-04
 * @LastEditors: 苏晓晴
 * @Description: 专辑解析实例
 * @FilePath: getAlbumDetail.php
 */

require_once 'getMusicapi.php';

// 创建API实例
$api = new NeteaseMusicAPI();

$singleId = '2532181'; // 专辑ID 支持单ID 例如2532181

$rawResult = $api->getAlbumDetail($singleId);
echo json_encode($rawResult,480);
?>