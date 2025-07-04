<?php
/*
 * @Author: 苏晓晴 - www.toubiec.cn
 * @Date: 2025-07-04
 * @LastEditTime: 2025-07-04
 * @LastEditors: 苏晓晴
 * @Description: 歌单解析实例
 * @FilePath: getPlaylistDetail.php
 */

require_once 'getMusicapi.php';

// 创建API实例
$api = new NeteaseMusicAPI();

$singleId = '5202687076'; // 歌单ID 支持单ID 例如5202687076

$rawResult = $api->getPlaylistDetail($singleId);
$getPlaylistDetail['id'] = $rawResult['id'] ?? ''; // 歌单ID
$getPlaylistDetail['name'] = $rawResult['name'] ?? ''; // 歌单名称
$getPlaylistDetail['coverImgUrl'] = $rawResult['coverImgUrl'] ?? ''; // 歌单封面
$getPlaylistDetail['trackCount'] = $rawResult['trackCount'] ?? ''; // 歌单歌曲数量
$getPlaylistDetail['creator'] = $rawResult['creator'] ?? ''; // 歌单创建者
$getPlaylistDetail['tracks'] = $rawResult['tracks'] ?? ''; // 歌曲列表
$list['data'] = $getPlaylistDetail;
echo json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>