<?php

namespace WechatWorkMediaBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkMediaBundle\Entity\TempMedia;
use WechatWorkMediaBundle\Enum\MediaType;

/**
 * @internal
 */
#[CoversClass(TempMedia::class)]
final class TempMediaTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new TempMedia();
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'mediaId' => ['mediaId', 'test_value'],
        ];
    }

    private TempMedia $tempMedia;

    private Agent $agent;

    protected function setUp(): void
    {
        $this->tempMedia = new TempMedia();
        $agent = $this->createMock(Agent::class);
        $this->agent = $agent;
    }

    public function testGetIdReturnsInitialNullValue(): void
    {
        $result = $this->tempMedia->getId();

        $this->assertNull($result);
    }

    public function testSetCreateTimeSetsCreateTimeCorrectly(): void
    {
        $createTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $this->tempMedia->setCreateTime($createTime);

        $this->assertSame($createTime, $this->tempMedia->getCreateTime());
    }

    public function testSetCreateTimeWithNull(): void
    {
        $this->tempMedia->setCreateTime(null);

        $this->assertNull($this->tempMedia->getCreateTime());
    }

    public function testGetCreateTimeReturnsSetValue(): void
    {
        $createTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $this->tempMedia->setCreateTime($createTime);

        $result = $this->tempMedia->getCreateTime();

        $this->assertSame($createTime, $result);
    }

    public function testSetTypeSetsTypeCorrectly(): void
    {
        $type = MediaType::IMAGE;
        $this->tempMedia->setType($type);

        $this->assertSame($type, $this->tempMedia->getType());
    }

    public function testGetTypeReturnsInitialNullValue(): void
    {
        $result = $this->tempMedia->getType();

        $this->assertNull($result);
    }

    public function testSetFileKeySetsFileKeyCorrectly(): void
    {
        $fileKey = 'test_file_key_123';
        $this->tempMedia->setFileKey($fileKey);

        $this->assertSame($fileKey, $this->tempMedia->getFileKey());
    }

    public function testSetFileKeyWithNull(): void
    {
        $this->tempMedia->setFileKey(null);

        $this->assertNull($this->tempMedia->getFileKey());
    }

    public function testGetFileKeyReturnsInitialNullValue(): void
    {
        $result = $this->tempMedia->getFileKey();

        $this->assertNull($result);
    }

    public function testSetFileUrlSetsFileUrlCorrectly(): void
    {
        $fileUrl = 'https://example.com/test.jpg';
        $this->tempMedia->setFileUrl($fileUrl);

        $this->assertSame($fileUrl, $this->tempMedia->getFileUrl());
    }

    public function testSetFileUrlWithNull(): void
    {
        $this->tempMedia->setFileUrl(null);

        $this->assertNull($this->tempMedia->getFileUrl());
    }

    public function testGetFileUrlReturnsInitialNullValue(): void
    {
        $result = $this->tempMedia->getFileUrl();

        $this->assertNull($result);
    }

    public function testSetMediaIdSetsMediaIdCorrectly(): void
    {
        $mediaId = 'media_id_456';
        $this->tempMedia->setMediaId($mediaId);

        $this->assertSame($mediaId, $this->tempMedia->getMediaId());
    }

    public function testSetExpireTimeSetsExpireTimeCorrectly(): void
    {
        $expireTime = new \DateTimeImmutable('2024-01-04 12:00:00');
        $this->tempMedia->setExpireTime($expireTime);

        $this->assertSame($expireTime, $this->tempMedia->getExpireTime());
    }

    public function testSetExpireTimeWithNull(): void
    {
        $this->tempMedia->setExpireTime(null);

        $this->assertNull($this->tempMedia->getExpireTime());
    }

    public function testGetExpireTimeReturnsInitialNullValue(): void
    {
        $result = $this->tempMedia->getExpireTime();

        $this->assertNull($result);
    }

    public function testSetAgentSetsAgentCorrectly(): void
    {
        $this->tempMedia->setAgent($this->agent);

        $this->assertSame($this->agent, $this->tempMedia->getAgent());
    }

    public function testSetAgentWithNull(): void
    {
        $this->tempMedia->setAgent(null);

        $this->assertNull($this->tempMedia->getAgent());
    }

    public function testGetAgentReturnsInitialNullValue(): void
    {
        $result = $this->tempMedia->getAgent();

        $this->assertNull($result);
    }

    public function testEntityPropertiesWorkTogether(): void
    {
        $createTime = new \DateTimeImmutable('2024-01-01 12:00:00');
        $expireTime = new \DateTimeImmutable('2024-01-04 12:00:00');
        $type = MediaType::VIDEO;
        $fileKey = 'file_key_789';
        $fileUrl = 'https://example.com/video.mp4';
        $mediaId = 'media_id_789';

        $this->tempMedia->setCreateTime($createTime);
        $this->tempMedia->setType($type);
        $this->tempMedia->setFileKey($fileKey);
        $this->tempMedia->setFileUrl($fileUrl);
        $this->tempMedia->setMediaId($mediaId);
        $this->tempMedia->setExpireTime($expireTime);
        $this->tempMedia->setAgent($this->agent);

        $this->assertSame($createTime, $this->tempMedia->getCreateTime());
        $this->assertSame($type, $this->tempMedia->getType());
        $this->assertSame($fileKey, $this->tempMedia->getFileKey());
        $this->assertSame($fileUrl, $this->tempMedia->getFileUrl());
        $this->assertSame($mediaId, $this->tempMedia->getMediaId());
        $this->assertSame($expireTime, $this->tempMedia->getExpireTime());
        $this->assertSame($this->agent, $this->tempMedia->getAgent());
    }
}
