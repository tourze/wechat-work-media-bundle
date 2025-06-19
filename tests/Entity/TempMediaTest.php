<?php

namespace WechatWorkMediaBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Tourze\WechatWorkContracts\AgentInterface;
use WechatWorkMediaBundle\Entity\TempMedia;
use WechatWorkMediaBundle\Enum\MediaType;

class TempMediaTest extends TestCase
{
    private TempMedia $tempMedia;
    private AgentInterface $agent;

    protected function setUp(): void
    {
        $this->tempMedia = new TempMedia();        $agent = $this->createMock(AgentInterface::class);
        $this->agent = $agent;
    }

    public function test_getId_returnsInitialNullValue(): void
    {
        $result = $this->tempMedia->getId();
        
        $this->assertNull($result);
    }

    public function test_setCreateTime_setsCreateTimeCorrectly(): void
    {
        $createTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $result = $this->tempMedia->setCreateTime($createTime);
        
        $this->assertSame($this->tempMedia, $result);
        $this->assertSame($createTime, $this->tempMedia->getCreateTime());
    }

    public function test_setCreateTime_withNull(): void
    {
        $result = $this->tempMedia->setCreateTime(null);
        
        $this->assertSame($this->tempMedia, $result);
        $this->assertNull($this->tempMedia->getCreateTime());
    }

    public function test_getCreateTime_returnsSetValue(): void
    {
        $createTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $this->tempMedia->setCreateTime($createTime);
        
        $result = $this->tempMedia->getCreateTime();
        
        $this->assertSame($createTime, $result);
    }

    public function test_setType_setsTypeCorrectly(): void
    {
        $type = MediaType::IMAGE;
        $result = $this->tempMedia->setType($type);
        
        $this->assertSame($this->tempMedia, $result);
        $this->assertSame($type, $this->tempMedia->getType());
    }

    public function test_getType_returnsInitialNullValue(): void
    {
        $result = $this->tempMedia->getType();
        
        $this->assertNull($result);
    }

    public function test_setFileKey_setsFileKeyCorrectly(): void
    {
        $fileKey = 'test_file_key_123';
        $result = $this->tempMedia->setFileKey($fileKey);
        
        $this->assertSame($this->tempMedia, $result);
        $this->assertSame($fileKey, $this->tempMedia->getFileKey());
    }

    public function test_setFileKey_withNull(): void
    {
        $result = $this->tempMedia->setFileKey(null);
        
        $this->assertSame($this->tempMedia, $result);
        $this->assertNull($this->tempMedia->getFileKey());
    }

    public function test_getFileKey_returnsInitialNullValue(): void
    {
        $result = $this->tempMedia->getFileKey();
        
        $this->assertNull($result);
    }

    public function test_setFileUrl_setsFileUrlCorrectly(): void
    {
        $fileUrl = 'https://example.com/test.jpg';
        $result = $this->tempMedia->setFileUrl($fileUrl);
        
        $this->assertSame($this->tempMedia, $result);
        $this->assertSame($fileUrl, $this->tempMedia->getFileUrl());
    }

    public function test_setFileUrl_withNull(): void
    {
        $result = $this->tempMedia->setFileUrl(null);
        
        $this->assertSame($this->tempMedia, $result);
        $this->assertNull($this->tempMedia->getFileUrl());
    }

    public function test_getFileUrl_returnsInitialNullValue(): void
    {
        $result = $this->tempMedia->getFileUrl();
        
        $this->assertNull($result);
    }

    public function test_setMediaId_setsMediaIdCorrectly(): void
    {
        $mediaId = 'media_id_456';
        $result = $this->tempMedia->setMediaId($mediaId);
        
        $this->assertSame($this->tempMedia, $result);
        $this->assertSame($mediaId, $this->tempMedia->getMediaId());
    }

    public function test_setExpireTime_setsExpireTimeCorrectly(): void
    {
        $expireTime = new \DateTimeImmutable('2024-01-04 12:00:00');
        $result = $this->tempMedia->setExpireTime($expireTime);
        
        $this->assertSame($this->tempMedia, $result);
        $this->assertSame($expireTime, $this->tempMedia->getExpireTime());
    }

    public function test_setExpireTime_withNull(): void
    {
        $result = $this->tempMedia->setExpireTime(null);
        
        $this->assertSame($this->tempMedia, $result);
        $this->assertNull($this->tempMedia->getExpireTime());
    }

    public function test_getExpireTime_returnsInitialNullValue(): void
    {
        $result = $this->tempMedia->getExpireTime();
        
        $this->assertNull($result);
    }

    public function test_setAgent_setsAgentCorrectly(): void
    {
        $result = $this->tempMedia->setAgent($this->agent);
        
        $this->assertSame($this->tempMedia, $result);
        $this->assertSame($this->agent, $this->tempMedia->getAgent());
    }

    public function test_setAgent_withNull(): void
    {
        $result = $this->tempMedia->setAgent(null);
        
        $this->assertSame($this->tempMedia, $result);
        $this->assertNull($this->tempMedia->getAgent());
    }

    public function test_getAgent_returnsInitialNullValue(): void
    {
        $result = $this->tempMedia->getAgent();
        
        $this->assertNull($result);
    }

    public function test_entityProperties_workTogether(): void
    {
        $createTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $expireTime = new \DateTimeImmutable('2024-01-04 12:00:00');
        $type = MediaType::VIDEO;
        $fileKey = 'file_key_789';
        $fileUrl = 'https://example.com/video.mp4';
        $mediaId = 'media_id_789';

        $this->tempMedia
            ->setCreateTime($createTime)
            ->setType($type)
            ->setFileKey($fileKey)
            ->setFileUrl($fileUrl)
            ->setMediaId($mediaId)
            ->setExpireTime($expireTime)
            ->setAgent($this->agent);

        $this->assertSame($createTime, $this->tempMedia->getCreateTime());
        $this->assertSame($type, $this->tempMedia->getType());
        $this->assertSame($fileKey, $this->tempMedia->getFileKey());
        $this->assertSame($fileUrl, $this->tempMedia->getFileUrl());
        $this->assertSame($mediaId, $this->tempMedia->getMediaId());
        $this->assertSame($expireTime, $this->tempMedia->getExpireTime());
        $this->assertSame($this->agent, $this->tempMedia->getAgent());
    }
} 