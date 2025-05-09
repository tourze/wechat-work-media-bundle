<?php

namespace WechatWorkMediaBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkMediaBundle\Entity\TempMedia;
use WechatWorkMediaBundle\Enum\MediaType;

class TempMediaTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $media = new TempMedia();
        
        // 测试创建时间
        $createTime = new \DateTime();
        $media->setCreateTime($createTime);
        $this->assertSame($createTime, $media->getCreateTime());
        
        // 测试媒体类型
        $type = MediaType::IMAGE;
        $media->setType($type);
        $this->assertSame($type, $media->getType());
        
        // 测试文件键
        $fileKey = 'test_file_key_123';
        $media->setFileKey($fileKey);
        $this->assertEquals($fileKey, $media->getFileKey());
        
        // 测试文件 URL
        $fileUrl = 'https://example.com/test.jpg';
        $media->setFileUrl($fileUrl);
        $this->assertEquals($fileUrl, $media->getFileUrl());
        
        // 测试媒体 ID
        $mediaId = 'test_media_id_123';
        $media->setMediaId($mediaId);
        $this->assertEquals($mediaId, $media->getMediaId());
        
        // 测试过期时间
        $expireTime = new \DateTime('+3 days');
        $media->setExpireTime($expireTime);
        $this->assertSame($expireTime, $media->getExpireTime());
        
        // 测试代理
        $agent = new Agent();
        $media->setAgent($agent);
        $this->assertSame($agent, $media->getAgent());
    }
    
    /**
     * 测试所有枚举媒体类型
     */
    public function testAllMediaTypes(): void
    {
        $media = new TempMedia();
        
        // 测试所有有效的媒体类型
        foreach ([MediaType::IMAGE, MediaType::VOICE, MediaType::VIDEO, MediaType::FILE] as $type) {
            $media->setType($type);
            $this->assertSame($type, $media->getType());
        }
    }
    
    /**
     * 测试设置空值
     */
    public function testNullableFields(): void
    {
        $media = new TempMedia();
        
        // ID 通常不应为空，但如果未设置则默认为 null
        $this->assertNull($media->getId());
        
        // 创建时间可为空
        $media->setCreateTime(null);
        $this->assertNull($media->getCreateTime());
        
        // 文件键可为空
        $media->setFileKey(null);
        $this->assertNull($media->getFileKey());
        
        // 文件 URL 可为空
        $media->setFileUrl(null);
        $this->assertNull($media->getFileUrl());
        
        // 过期时间可为空
        $media->setExpireTime(null);
        $this->assertNull($media->getExpireTime());
        
        // Agent 可为空
        $media->setAgent(null);
        $this->assertNull($media->getAgent());
    }
} 