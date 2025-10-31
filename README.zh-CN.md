# 企业微信媒体资源包

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-work-media-bundle.svg)](https://packagist.org/packages/tourze/wechat-work-media-bundle)
[![Build Status](https://img.shields.io/github/workflow/status/tourze/wechat-work-media-bundle/CI)](https://github.com/tourze/wechat-work-media-bundle/actions)
[![Coverage](https://img.shields.io/codecov/c/github/tourze/wechat-work-media-bundle)](https://codecov.io/gh/tourze/wechat-work-media-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-work-media-bundle.svg)](https://packagist.org/packages/tourze/wechat-work-media-bundle)

一个用于管理企业微信应用中媒体资源的 Symfony Bundle，
提供上传、下载和管理临时媒体文件的无缝集成。

## 功能特性

- 上传媒体文件到企业微信并获取媒体 ID
- 从企业微信下载媒体文件
- 支持多种媒体类型（图片、语音、视频、文件）
- 临时媒体管理，支持过期时间跟踪
- 缓存支持，提高性能
- 与 Flysystem 文件系统集成
- 提供 JSON-RPC 程序用于文件转换
- 基于 Doctrine ORM 的实体媒体跟踪

## 系统要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM 3.0 或更高版本

## 安装

```bash
composer require tourze/wechat-work-media-bundle
```

## 快速开始

### 基本用法

```php
<?php

use WechatWorkMediaBundle\Service\MediaService;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkBundle\Entity\Agent;

// 通过依赖注入获取 MediaService
public function __construct(private MediaService $mediaService)
{
}

// 上传媒体文件并获取媒体 ID
$agent = $this->agentRepository->find($agentId);
$mediaId = $this->mediaService->uploadAndGetMediaId(
    $agent,
    '/path/to/your/file.jpg',
    MediaType::IMAGE
);

// 下载媒体文件
$filePath = $this->mediaService->downloadMedia($agent, $mediaId, 'jpg');
```

### 实体用法

```php
<?php

use WechatWorkMediaBundle\Entity\TempMedia;
use WechatWorkMediaBundle\Enum\MediaType;

// 创建临时媒体实体
$tempMedia = new TempMedia();
$tempMedia->setMediaId($mediaId);
$tempMedia->setType(MediaType::IMAGE);
$tempMedia->setFileUrl('/uploads/image.jpg');
$tempMedia->setAgent($agent);
$tempMedia->setExpireTime(new \DateTimeImmutable('+3 days'));

$entityManager->persist($tempMedia);
$entityManager->flush();
```

### JSON-RPC 程序

```php
<?php

// 通过 JSON-RPC 将文件转换为企业微信素材
$result = $jsonRpcClient->call('TransformFileToWechatWorkMaterial', [
    'corpId' => 'your_corp_id',
    'agentId' => 'your_agent_id',
    'fileUrl' => 'uploads/document.pdf',
    'mediaType' => 'file'
]);

$mediaId = $result['media_id'];
```

## 配置

该包会自动注册服务。请确保已配置以下依赖：

- `tourze/wechat-work-bundle` - 用于企业微信 API 集成
- `tourze/symfony-temp-file-bundle` - 用于临时文件管理
- 实现了 `Psr\SimpleCache\CacheInterface` 的缓存服务
- 实现了 `League\Flysystem\FilesystemOperator` 的文件系统服务

## API 参考

### MediaService

#### uploadAndGetMediaId(AgentInterface $agent, string $path, MediaType $type): string

上传媒体文件到企业微信并返回媒体 ID。

**参数：**
- `$agent`: 企业微信应用实例
- `$path`: 要上传的本地文件路径
- `$type`: 媒体类型（IMAGE、VOICE、VIDEO、FILE）

**返回值：** 媒体 ID 字符串（有效期 3 天）

#### downloadMedia(AgentInterface $agent, string $mediaId, ?string $ext = null): string

从企业微信下载媒体文件。

**参数：**
- `$agent`: 企业微信应用实例
- `$mediaId`: 来自企业微信的媒体 ID
- `$ext`: 可选的文件扩展名

**返回值：** 存储系统中的文件路径

### MediaType 枚举

- `MediaType::IMAGE` - 图片文件
- `MediaType::VOICE` - 语音文件
- `MediaType::VIDEO` - 视频文件
- `MediaType::FILE` - 普通文件

## 高级用法

### 自定义缓存配置

```yaml
# config/packages/wechat_work_media.yaml
parameters:
    wechat_work_media.cache_ttl: 7200  # 2 小时
```

### 错误处理

```php
<?php

use WechatWorkMediaBundle\Exception\MediaUploadFailedException;
use WechatWorkMediaBundle\Exception\FileNotFoundException;

try {
    $mediaId = $mediaService->uploadAndGetMediaId($agent, $filePath, MediaType::IMAGE);
} catch (MediaUploadFailedException $e) {
    $logger->error('媒体上传失败: ' . $e->getMessage());
    // 适当处理上传错误
} catch (FileNotFoundException $e) {
    $logger->error('文件未找到: ' . $e->getMessage());
    // 适当处理文件未找到错误
}
```

### 批量操作

```php
<?php

// 上传多个文件
$mediaIds = [];
foreach ($filePaths as $filePath) {
    $mediaIds[] = $mediaService->uploadAndGetMediaId($agent, $filePath, MediaType::IMAGE);
}
```

## 贡献

请查看 [CONTRIBUTING.md](CONTRIBUTING.md) 了解详情。

## 许可证

MIT 许可证。请查看 [许可证文件](LICENSE) 了解更多信息。