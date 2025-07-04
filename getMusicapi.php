<?php
/*
 * @Author: 苏晓晴 - www.toubiec.cn
 * @Date: 2025-07-04
 * @LastEditTime: 2025-07-04
 * @LastEditors: 苏晓晴
 * @Description: 网易云音乐API
 * @FilePath: getMusicapi.php
 */

class NeteaseMusicAPI {
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Safari/537.36 Chrome/91.0.4472.164 NeteaseMusicDesktop/2.10.2.200154';
    private $aesKey = 'e82ckenh8dichen8';
    private $cookies = [];
    
    public function __construct($cookies = []) {
        $this->cookies = array_merge([
            'os' => 'pc',
            'appver' => '',
            'osver' => '',
            'deviceId' => 'pyncm!'
        ], $cookies);
    }
    
    /**
     * 十六进制编码
     */
    private function hexDigest($data) {
        return bin2hex($data);
    }
    
    /**
     * MD5哈希
     */
    private function hashDigest($text) {
        return md5($text, true);
    }
    
    /**
     * MD5哈希十六进制
     */
    private function hashHexDigest($text) {
        return $this->hexDigest($this->hashDigest($text));
    }
    
    /**
     * PKCS7填充
     */
    private function pkcs7Pad($data, $blockSize) {
        $pad = $blockSize - (strlen($data) % $blockSize);
        return $data . str_repeat(chr($pad), $pad);
    }
    
    /**
     * AES加密
     */
    private function aesEncrypt($data, $key = null) {
        if ($key === null) {
            $key = $this->aesKey;
        }
        $padded = $this->pkcs7Pad($data, 16);
        return openssl_encrypt($padded, 'AES-128-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
    }
    
    /**
     * HTTP POST请求
     */
    private function post($url, $params, $cookies = []) {
        $allCookies = array_merge($this->cookies, $cookies);
        
        $cookieStr = '';
        foreach ($allCookies as $key => $value) {
            $cookieStr .= $key . '=' . $value . '; ';
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['params' => $params]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: ' . $this->userAgent,
            'Referer: ',
            'Cookie: ' . rtrim($cookieStr, '; ')
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false || $httpCode !== 200) {
            throw new Exception('HTTP请求失败: ' . $httpCode);
        }
        
        return $response;
    }
    
    /**
     * 普通HTTP POST请求
     */
    private function simplePost($url, $data, $cookies = []) {
        $allCookies = array_merge($this->cookies, $cookies);
        
        $cookieStr = '';
        foreach ($allCookies as $key => $value) {
            $cookieStr .= $key . '=' . $value . '; ';
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: ' . $this->userAgent,
            'Referer: https://music.163.com/',
            'Cookie: ' . rtrim($cookieStr, '; ')
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }
    
    /**
     * 普通HTTP GET请求
     */
    private function simpleGet($url, $cookies = []) {
        $allCookies = array_merge($this->cookies, $cookies);
        
        $cookieStr = '';
        foreach ($allCookies as $key => $value) {
            $cookieStr .= $key . '=' . $value . '; ';
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: ' . $this->userAgent,
            'Referer: https://music.163.com/',
            'Cookie: ' . rtrim($cookieStr, '; ')
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false || $httpCode !== 200) {
            throw new Exception('HTTP GET请求失败: ' . $httpCode);
        }
        
        return $response;
    }
    
    /**
     * 获取音乐播放URL-V1
     * @param string|array $id 单个ID或多个ID数组，例如: '1315196858' 或 ['1315196858', '119667185']
     * @param string $level 音质等级
     * @param array $cookies Cookie数组
     */
    public function getMusicUrl($id, $level = 'standard', $cookies = []) {
        $url = 'https://interface3.music.163.com/eapi/song/enhance/player/url/v1';
        
        $config = [
            'os' => 'pc',
            'appver' => '',
            'osver' => '',
            'deviceId' => 'pyncm!',
            'requestId' => (string)rand(20000000, 30000000)
        ];
        // 处理单个ID或多个ID
        $ids = is_array($id) ? $id : [$id];
        
        $payload = [
            'ids' => $ids,
            'level' => $level,
            'encodeType' => 'flac',
            'header' => json_encode($config)
        ];
        
        if ($level === 'sky') {
            $payload['immerseType'] = 'c51';
        }
        
        $url2 = str_replace('/eapi/', '/api/', parse_url($url, PHP_URL_PATH));
        $digest = $this->hashHexDigest('nobody' . $url2 . 'use' . json_encode($payload) . 'md5forencrypt');
        $params = $url2 . '-36cd479b6b5-' . json_encode($payload) . '-36cd479b6b5-' . $digest;
        
        $encrypted = $this->aesEncrypt($params);
        $encryptedHex = $this->hexDigest($encrypted);
        
        $response = $this->post($url, $encryptedHex, $cookies);
        return json_decode($response, true);
    }
    
    /**
     * 获取歌曲详情-获取
     * @param string $id 歌曲ID
     * @param array $cookies Cookie数组
     */
    public function getSongDetail($id, $cookies = []) {
        $url = 'https://interface3.music.163.com/eapi/v3/song/detail';
        
        $config = [
            'os' => 'pc',
            'appver' => '',
            'osver' => '',
            'deviceId' => 'pyncm!',
            'requestId' => (string)rand(20000000, 30000000)
        ];
        
        $payload = [
            'c' => json_encode([['id' => (int)$id, 'v' => 0]]),
            'header' => json_encode($config)
        ];
        
        $url2 = str_replace('/eapi/', '/api/', parse_url($url, PHP_URL_PATH));
        $digest = $this->hashHexDigest('nobody' . $url2 . 'use' . json_encode($payload) . 'md5forencrypt');
        $params = $url2 . '-36cd479b6b5-' . json_encode($payload) . '-36cd479b6b5-' . $digest;
        
        $encrypted = $this->aesEncrypt($params);
        $encryptedHex = $this->hexDigest($encrypted);
        
        $response = $this->post($url, $encryptedHex, $cookies);
        return json_decode($response, true);
    }
    
    /**
     * 获取歌词-获取
     * @param string $id 歌曲ID
     * @param array $cookies Cookie数组
     */
    public function getLyric($id, $cookies = []) {
        $url = 'https://interface3.music.163.com/eapi/song/lyric';
        
        $config = [
            'os' => 'pc',
            'appver' => '',
            'osver' => '',
            'deviceId' => 'pyncm!',
            'requestId' => (string)rand(20000000, 30000000)
        ];
        
        $payload = [
            'id' => $id,
            'cp' => 'false',
            'tv' => '0',
            'lv' => '0',
            'rv' => '0',
            'kv' => '0',
            'yv' => '0',
            'ytv' => '0',
            'yrv' => '0',
            'header' => json_encode($config)
        ];
        
        $url2 = str_replace('/eapi/', '/api/', parse_url($url, PHP_URL_PATH));
        $digest = $this->hashHexDigest('nobody' . $url2 . 'use' . json_encode($payload) . 'md5forencrypt');
        $params = $url2 . '-36cd479b6b5-' . json_encode($payload) . '-36cd479b6b5-' . $digest;
        
        $encrypted = $this->aesEncrypt($params);
        $encryptedHex = $this->hexDigest($encrypted);
        
        $response = $this->post($url, $encryptedHex, $cookies);
        return json_decode($response, true);
    }
    
    /**
     * 搜索音乐-获取
     * @param string $keywords 搜索关键词
     * @param int $limit 每页数量
     * @param int $offset 偏移量
     * @param array $cookies Cookie数组
     */
    public function getSearchMusic($keywords, $limit = 10, $offset = 0, $cookies = []) {
        $url = 'https://interface3.music.163.com/eapi/cloudsearch/pc';
        
        $config = [
            'os' => 'pc',
            'appver' => '',
            'osver' => '',
            'deviceId' => 'pyncm!',
            'requestId' => (string)rand(20000000, 30000000)
        ];
        
        $payload = [
            's' => $keywords,
            'type' => 1,
            'limit' => $limit,
            'offset' => $offset,
            'header' => json_encode($config)
        ];
        
        $url2 = str_replace('/eapi/', '/api/', parse_url($url, PHP_URL_PATH));
        $digest = $this->hashHexDigest('nobody' . $url2 . 'use' . json_encode($payload) . 'md5forencrypt');
        $params = $url2 . '-36cd479b6b5-' . json_encode($payload) . '-36cd479b6b5-' . $digest;
        
        $encrypted = $this->aesEncrypt($params);
        $encryptedHex = $this->hexDigest($encrypted);
        
        $response = $this->post($url, $encryptedHex, $cookies);
        $result = json_decode($response, true);
        
        $songs = [];
        if (isset($result['result']['songs'])) {
            foreach ($result['result']['songs'] as $item) {
                $artists = [];
                foreach ($item['ar'] as $artist) {
                    $artists[] = $artist['name'];
                }
                
                $songs[] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'artists' => implode('/', $artists),
                    'album' => $item['al']['name'],
                    'picUrl' => $item['al']['picUrl'],
                    'duration' => $item['dt'] ?? 0
                ];
            }
        }
        
        return [
            'songs' => $songs,
            'total' => $result['result']['songCount'] ?? 0
        ];
    }
    
    /**
     * 获取歌单详情
     */
    public function getPlaylistDetail($playlistId, $cookies = []) {
        $url = 'https://music.163.com/api/v6/playlist/detail';
        
        $payload = [
            'id' => $playlistId
        ];
        
        $response = $this->simplePost($url, $payload, $cookies);
        $result = json_decode($response, true);
        
        if (!isset($result['playlist'])) {
            return null;
        }
        
        $playlist = $result['playlist'];
        $info = [
            'id' => $playlist['id'] ?? null,
            'name' => $playlist['name'] ?? '',
            'coverImgUrl' => $playlist['coverImgUrl'] ?? '',
            'creator' => $playlist['creator']['nickname'] ?? '',
            'trackCount' => $playlist['trackCount'] ?? 0,
            'description' => $playlist['description'] ?? '',
            'tracks' => []
        ];
        
        // 获取所有trackIds
        $trackIds = [];
        if (isset($playlist['trackIds']) && is_array($playlist['trackIds'])) {
            foreach ($playlist['trackIds'] as $track) {
                $trackIds[] = (string)$track['id'];
            }
        }
        
        // 分批获取详细信息（每批最多100首）
        for ($i = 0; $i < count($trackIds); $i += 100) {
            $batchIds = array_slice($trackIds, $i, 100);
            
            foreach ($batchIds as $id) {
                // 直接调用getSongDetail方法
                $songResult = $this->getSongDetail($id, $cookies);
                
                if (isset($songResult['songs']) && is_array($songResult['songs'])) {
                    foreach ($songResult['songs'] as $song) {
                        $artists = [];
                        if (isset($song['ar']) && is_array($song['ar'])) {
                            foreach ($song['ar'] as $artist) {
                                $artists[] = $artist['name'];
                            }
                        }
                        
                        $info['tracks'][] = [
                            'id' => $song['id'],
                            'name' => $song['name'],
                            'artists' => implode('/', $artists),
                            'album' => $song['al']['name'] ?? '',
                            'picUrl' => $song['al']['picUrl'] ?? ''
                        ];
                    }
                }
            }
        }
        
        return $info;
    }
        
    /**
     * 获取网易云专辑详情及全部歌曲列表
     * @param string $albumId 专辑ID
     * @param array $cookies 登录cookies
     * @return array 专辑基本信息和全部歌曲列表
     */
    public function getAlbumDetail($albumId, $cookies = []) {
        $url = "https://music.163.com/api/v1/album/{$albumId}";
        
        $response = $this->simpleGet($url, $cookies);
        $result = json_decode($response, true);
        
        if (!isset($result['album'])) {
            return null;
        }
        
        $album = $result['album'];
        
        $info = [
            'id' => $album['id'] ?? null,
            'name' => $album['name'] ?? '',
            'coverImgUrl' => isset($album['pic']) ? $this->getPicUrl($album['pic']) : '',
            'artist' => $album['artist']['name'] ?? '',
            'publishTime' => $album['publishTime'] ?? null,
            'description' => $album['description'] ?? '',
            'songs' => []
        ];
        
        // 处理专辑中的歌曲列表
        if (isset($result['songs']) && is_array($result['songs'])) {
            foreach ($result['songs'] as $song) {
                // 提取艺术家信息
                $artists = [];
                if (isset($song['ar']) && is_array($song['ar'])) {
                    foreach ($song['ar'] as $artist) {
                        $artists[] = $artist['name'];
                    }
                }
                
                $info['songs'][] = [
                    'id' => $song['id'],
                    'name' => $song['name'],
                    'artists' => implode('/', $artists),
                    'album' => $song['al']['name'] ?? '',
                    'picUrl' => isset($song['al']['pic']) ? $this->getPicUrl($song['al']['pic']) : ''
                ];
            }
        }
        
        return $info;
    }

    /**
     * 网易云图片ID加密
     */
    public function encryptId($idStr) {
        $magic = '3go8&$8*3*3h0k(2)2';
        $songId = str_split($idStr);
        
        for ($i = 0; $i < count($songId); $i++) {
            $songId[$i] = chr(ord($songId[$i]) ^ ord($magic[$i % strlen($magic)]));
        }
        
        $m = implode('', $songId);
        $md5Bytes = md5($m, true);
        $result = base64_encode($md5Bytes);
        $result = str_replace(['/', '+'], ['_', '-'], $result);
        
        return $result;
    }
    
    /**
     * 获取图片URL
     */
    public function getPicUrl($picId, $size = 300) {
        $encId = $this->encryptId((string)$picId);
        return "https://p3.music.126.net/{$encId}/{$picId}.jpg?param={$size}y{$size}";
    }
    
    /**
     * 从cookie.txt文件读取cookie
     * @param string $cookieFile cookie文件路径
     * @return array cookie数组
     */
    public function loadCookieFromFile($cookieFile) {
        if (!file_exists($cookieFile)) {
            throw new Exception("Cookie文件不存在: {$cookieFile}");
        }
        
        $cookieContent = file_get_contents($cookieFile);
        if ($cookieContent === false) {
            throw new Exception("无法读取Cookie文件: {$cookieFile}");
        }
        
        // 清理cookie字符串，移除换行符和多余空格
        $cookieContent = trim($cookieContent);
        $cookieContent = str_replace(["\r\n", "\n", "\r"], '', $cookieContent);
        // 将cookie字符串解析为数组
        $cookies = [];
        $pairs = explode(';', $cookieContent);
        foreach ($pairs as $pair) {
            $pair = trim($pair);
            if (strpos($pair, '=') !== false) {
                list($key, $value) = explode('=', $pair, 2);
                $cookies[trim($key)] = trim($value);
            }
        }
        return $cookies;
    }
    
    /**
     * 设置Cookie
     */
    public function setCookies($cookies) {
        $this->cookies = array_merge($this->cookies, $cookies);
    }
    
    /**
     * 获取当前Cookie
     */
    public function getCookies() {
        return $this->cookies;
    }
}
?>