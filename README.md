# 网易云音乐 API - PHP 版本

![PHP](https://img.shields.io/badge/PHP-7.0%2B-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![Version](https://img.shields.io/badge/version-1.0.0-orange.svg)

一个功能完整的网易云音乐 API PHP 实现，支持获取音乐播放链接、歌曲详情、歌词、搜索、歌单和专辑信息等功能。

## ✨ 特性

- 🎵 **音乐播放链接获取** - 支持多种音质（标准、高品质、无损、Hi-Res等）
- 🔍 **音乐搜索** - 支持关键词搜索歌曲
- 📝 **歌词获取** - 支持普通歌词、翻译歌词、罗马音歌词
- 📀 **歌曲详情** - 获取歌曲完整信息
- 📋 **歌单解析** - 获取歌单详情及所有歌曲列表
- 💿 **专辑解析** - 获取专辑详情及所有歌曲列表
- 🍪 **Cookie 支持** - 支持登录状态获取更多权限
- 🔐 **加密请求** - 使用网易云官方加密算法

## 📋 系统要求

- PHP 7.0 或更高版本
- cURL 扩展
- OpenSSL 扩展

## 🚀 快速开始

### 1. 下载项目

```bash
git clone https://github.com/your-username/netease-music-api-php.git
cd netease-music-api-php/demophp
```

### 2. 配置 Cookie（可选）

创建 `cookie.txt` 文件并添加你的网易云音乐 Cookie：

```
MUSIC_U=your_music_u_value; __csrf=your_csrf_value; ...
```

### 3. 使用示例

```php
<?php
require_once 'getMusicapi.php';

// 创建 API 实例
$api = new NeteaseMusicAPI();

// 加载 Cookie（可选）
$cookies = $api->loadCookieFromFile('cookie.txt');

// 获取音乐播放链接
$musicUrl = $api->getMusicUrl('1315196858', 'standard', $cookies);
echo json_encode($musicUrl, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
```

## 📖 API 文档

### 核心类：NeteaseMusicAPI

#### 构造函数

```php
$api = new NeteaseMusicAPI($cookies = []);
```

#### 主要方法

##### 1. 获取音乐播放链接

```php
$result = $api->getMusicUrl($id, $level = 'standard', $cookies = []);
```

**参数：**
- `$id` (string|array): 歌曲ID，支持单个ID或多个ID数组
- `$level` (string): 音质等级
  - `standard`: 标准音质
  - `exhigh`: 极高音质
  - `lossless`: 无损音质
  - `hires`: Hi-Res音质
  - `jyeffect`: 高清环绕声
  - `sky`: 沉浸环绕声
  - `jymaster`: 超清母带
- `$cookies` (array): Cookie数组

**示例：**
```php
// 单个歌曲
$result = $api->getMusicUrl('1315196858', 'lossless', $cookies);

// 多个歌曲
$result = $api->getMusicUrl(['1315196858', '119667185'], 'standard', $cookies);
```

##### 2. 搜索音乐

```php
$result = $api->getSearchMusic($keywords, $limit = 10, $offset = 0, $cookies = []);
```

**参数：**
- `$keywords` (string): 搜索关键词
- `$limit` (int): 每页数量
- `$offset` (int): 偏移量
- `$cookies` (array): Cookie数组

##### 3. 获取歌曲详情

```php
$result = $api->getSongDetail($id, $cookies = []);
```

##### 4. 获取歌词

```php
$result = $api->getLyric($id, $cookies = []);
```

##### 5. 获取歌单详情

```php
$result = $api->getPlaylistDetail($playlistId, $cookies = []);
```

##### 6. 获取专辑详情

```php
$result = $api->getAlbumDetail($albumId, $cookies = []);
```

##### 7. Cookie 管理

```php
// 从文件加载 Cookie
$cookies = $api->loadCookieFromFile('cookie.txt');

// 设置 Cookie
$api->setCookies($cookies);

// 获取当前 Cookie
$currentCookies = $api->getCookies();
```

## 📁 项目结构

```
demophp/
├── getMusicapi.php          # 核心 API 类
├── getMusicUrl.php          # 获取音乐链接示例
├── getSearchMusic.php       # 搜索音乐示例
├── getSongDetail.php        # 获取歌曲详情示例
├── getLyric.php            # 获取歌词示例
├── getPlaylistDetail.php   # 获取歌单详情示例
├── getAlbumDetail.php      # 获取专辑详情示例
├── cookie.txt              # Cookie 配置文件
└── README.md               # 项目说明文档
```

## 🔧 使用示例

### 获取音乐播放链接

```php
<?php
require_once 'getMusicapi.php';

$api = new NeteaseMusicAPI();
$cookies = $api->loadCookieFromFile('cookie.txt');

// 获取无损音质
$result = $api->getMusicUrl('1315196858', 'lossless', $cookies);

foreach ($result['data'] as $song) {
    echo "歌曲ID: " . $song['id'] . "\n";
    echo "播放链接: " . $song['url'] . "\n";
    echo "音质: " . $song['level'] . "\n";
    echo "比特率: " . $song['br'] . "\n";
    echo "文件大小: " . $song['size'] . "\n";
}
?>
```

### 搜索音乐

```php
<?php
require_once 'getMusicapi.php';

$api = new NeteaseMusicAPI();
$cookies = $api->loadCookieFromFile('cookie.txt');

$result = $api->getSearchMusic('薛之谦', 20, 0, $cookies);

echo "搜索结果总数: " . $result['total'] . "\n";
foreach ($result['songs'] as $song) {
    echo "歌曲: " . $song['name'] . " - " . $song['artists'] . "\n";
    echo "专辑: " . $song['album'] . "\n";
    echo "ID: " . $song['id'] . "\n\n";
}
?>
```

### 获取歌词

```php
<?php
require_once 'getMusicapi.php';

$api = new NeteaseMusicAPI();
$cookies = $api->loadCookieFromFile('cookie.txt');

$result = $api->getLyric('1315196858', $cookies);

echo "原文歌词:\n" . $result['lrc']['lyric'] . "\n";
echo "翻译歌词:\n" . $result['tlyric']['lyric'] . "\n";
?>
```

## ⚠️ 注意事项

1. **Cookie 获取**：部分功能需要登录状态，请从浏览器开发者工具中获取 Cookie
2. **请求频率**：请合理控制请求频率，避免被服务器限制
3. **版权声明**：本项目仅供学习交流使用，请尊重音乐版权
4. **稳定性**：网易云音乐 API 可能会发生变化，如遇问题请及时更新

## 🤝 贡献

欢迎提交 Issue 和 Pull Request 来改进这个项目！

## 📄 许可证

本项目采用 MIT 许可证 - 查看 [LICENSE](LICENSE) 文件了解详情。

## 👨‍💻 作者

- **苏晓晴** - [www.toubiec.cn](https://www.toubiec.cn)

## 🙏 致谢

感谢网易云音乐提供的优质音乐服务，本项目仅用于技术学习和交流。

---

⭐ 如果这个项目对你有帮助，请给它一个 Star！