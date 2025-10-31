# WechatWork Media Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-work-media-bundle.svg)](https://packagist.org/packages/tourze/wechat-work-media-bundle)
[![Build Status](https://img.shields.io/github/workflow/status/tourze/wechat-work-media-bundle/CI)](https://github.com/tourze/wechat-work-media-bundle/actions)
[![Coverage](https://img.shields.io/codecov/c/github/tourze/wechat-work-media-bundle)](https://codecov.io/gh/tourze/wechat-work-media-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-work-media-bundle.svg)](https://packagist.org/packages/tourze/wechat-work-media-bundle)

A Symfony bundle for managing media resources in WeChat Work (WeCom) applications, 
providing seamless integration for uploading, downloading, and managing temporary media files.

## Features

- Upload media files to WeChat Work and get media IDs
- Download media files from WeChat Work
- Support for multiple media types (image, voice, video, file)
- Temporary media management with expiration tracking
- Caching support for improved performance
- Filesystem integration with Flysystem
- JSON-RPC procedure for file transformation
- Entity-based media tracking with Doctrine ORM

## System Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM 3.0 or higher

## Installation

```bash
composer require tourze/wechat-work-media-bundle
```

## Quick Start

### Basic Usage

```php
<?php

use WechatWorkMediaBundle\Service\MediaService;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkBundle\Entity\Agent;

// Inject MediaService through dependency injection
public function __construct(private MediaService $mediaService)
{
}

// Upload a media file and get media ID
$agent = $this->agentRepository->find($agentId);
$mediaId = $this->mediaService->uploadAndGetMediaId(
    $agent,
    '/path/to/your/file.jpg',
    MediaType::IMAGE
);

// Download a media file
$filePath = $this->mediaService->downloadMedia($agent, $mediaId, 'jpg');
```

### Entity Usage

```php
<?php

use WechatWorkMediaBundle\Entity\TempMedia;
use WechatWorkMediaBundle\Enum\MediaType;

// Create a temporary media entity
$tempMedia = new TempMedia();
$tempMedia->setMediaId($mediaId);
$tempMedia->setType(MediaType::IMAGE);
$tempMedia->setFileUrl('/uploads/image.jpg');
$tempMedia->setAgent($agent);
$tempMedia->setExpireTime(new \DateTimeImmutable('+3 days'));

$entityManager->persist($tempMedia);
$entityManager->flush();
```

### JSON-RPC Procedure

```php
<?php

// Transform a file to WeChat Work material via JSON-RPC
$result = $jsonRpcClient->call('TransformFileToWechatWorkMaterial', [
    'corpId' => 'your_corp_id',
    'agentId' => 'your_agent_id',
    'fileUrl' => 'uploads/document.pdf',
    'mediaType' => 'file'
]);

$mediaId = $result['media_id'];
```

## Configuration

The bundle registers services automatically. Make sure you have the following dependencies configured:

- `tourze/wechat-work-bundle` for WeChat Work API integration
- `tourze/symfony-temp-file-bundle` for temporary file management
- A cache service implementing `Psr\SimpleCache\CacheInterface`
- A filesystem service implementing `League\Flysystem\FilesystemOperator`

## API Reference

### MediaService

#### uploadAndGetMediaId(AgentInterface $agent, string $path, MediaType $type): string

Uploads a media file to WeChat Work and returns the media ID.

**Parameters:**
- `$agent`: WeChat Work agent instance
- `$path`: Local file path to upload
- `$type`: Media type (IMAGE, VOICE, VIDEO, FILE)

**Returns:** Media ID string (valid for 3 days)

#### downloadMedia(AgentInterface $agent, string $mediaId, ?string $ext = null): string

Downloads a media file from WeChat Work.

**Parameters:**
- `$agent`: WeChat Work agent instance
- `$mediaId`: Media ID from WeChat Work
- `$ext`: Optional file extension

**Returns:** File path in the storage system

### MediaType Enum

- `MediaType::IMAGE` - Image files
- `MediaType::VOICE` - Voice files
- `MediaType::VIDEO` - Video files
- `MediaType::FILE` - General files

## Advanced Usage

### Custom Cache Configuration

```yaml
# config/packages/wechat_work_media.yaml
parameters:
    wechat_work_media.cache_ttl: 7200  # 2 hours
```

### Error Handling

```php
<?php

use WechatWorkMediaBundle\Exception\MediaUploadFailedException;
use WechatWorkMediaBundle\Exception\FileNotFoundException;

try {
    $mediaId = $mediaService->uploadAndGetMediaId($agent, $filePath, MediaType::IMAGE);
} catch (MediaUploadFailedException $e) {
    $logger->error('Media upload failed: ' . $e->getMessage());
    // Handle upload error appropriately
} catch (FileNotFoundException $e) {
    $logger->error('File not found: ' . $e->getMessage());
    // Handle file not found error
}
```

### Batch Operations

```php
<?php

// Upload multiple files
$mediaIds = [];
foreach ($filePaths as $filePath) {
    $mediaIds[] = $mediaService->uploadAndGetMediaId($agent, $filePath, MediaType::IMAGE);
}
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.