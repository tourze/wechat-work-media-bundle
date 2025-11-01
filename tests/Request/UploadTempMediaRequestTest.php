<?php

namespace WechatWorkMediaBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatWorkBundle\Entity\Agent;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkMediaBundle\Request\UploadTempMediaRequest;

/**
 * @internal
 */
#[CoversClass(UploadTempMediaRequest::class)]
final class UploadTempMediaRequestTest extends RequestTestCase
{
    private UploadTempMediaRequest $request;

    private Agent $agent;

    protected function setUp(): void
    {
        $this->request = new UploadTempMediaRequest();
        /*
         * 使用具体类进行Mock测试的原因：
         * 1) 必须使用Agent具体类，因为该实体未提供接口抽象
         * 2) 这种使用是合理和必要的，用于单元测试中模拟企业微信应用实体
         * 3) 理想情况下应该提供接口，但当前架构下Mock具体类是可接受的替代方案
         */
        $agent = $this->createMock(Agent::class);
        $this->agent = $agent;
    }

    public function testRequestExtendsApiRequest(): void
    {
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }

    public function testGetRequestPathReturnsCorrectPath(): void
    {
        $path = $this->request->getRequestPath();

        $this->assertSame('/cgi-bin/media/upload', $path);
    }

    public function testSetTypeSetsTypeCorrectly(): void
    {
        $type = MediaType::IMAGE;
        $this->request->setType($type);

        $this->assertSame($type, $this->request->getType());
    }

    public function testGetTypeReturnsSetType(): void
    {
        $type = MediaType::VIDEO;
        $this->request->setType($type);

        $result = $this->request->getType();

        $this->assertSame($type, $result);
    }

    public function testSetMediaFileSetsMediaFileCorrectly(): void
    {
        $mediaFile = '/tmp/test_file.jpg';
        $this->request->setMediaFile($mediaFile);

        $this->assertSame($mediaFile, $this->request->getMediaFile());
    }

    public function testGetMediaFileReturnsSetMediaFile(): void
    {
        $mediaFile = '/tmp/another_test_file.mp4';
        $this->request->setMediaFile($mediaFile);

        $result = $this->request->getMediaFile();

        $this->assertSame($mediaFile, $result);
    }

    public function testSetAgentSetsAgentCorrectly(): void
    {
        $this->request->setAgent($this->agent);

        $this->assertSame($this->agent, $this->request->getAgent());
    }

    public function testGetAgentReturnsSetAgent(): void
    {
        $this->request->setAgent($this->agent);

        $result = $this->request->getAgent();

        $this->assertSame($this->agent, $result);
    }

    public function testGetRequestOptionsWithTypeAndMediaFile(): void
    {
        $type = MediaType::FILE;
        $mediaFile = '/tmp/document.pdf';

        $this->request->setType($type);
        $this->request->setMediaFile($mediaFile);

        // 这里我们需要创建一个临时文件来测试
        $tempFile = tempnam(sys_get_temp_dir(), 'test_media');
        file_put_contents($tempFile, 'test content');
        $this->request->setMediaFile($tempFile);

        $options = $this->request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('body', $options);
        $this->assertIsArray($options['query']);
        $this->assertIsArray($options['body']);
        $this->assertArrayHasKey('type', $options['query']);
        $this->assertSame($type->value, $options['query']['type']);
        $this->assertArrayHasKey('media', $options['body']);
        $this->assertIsResource($options['body']['media']);

        // 清理临时文件
        fclose($options['body']['media']);
        unlink($tempFile);
    }

    public function testAllPropertiesWorkTogether(): void
    {
        $type = MediaType::VOICE;
        $mediaFile = '/tmp/audio.wav';

        $this->request->setType($type);
        $this->request->setMediaFile($mediaFile);
        $this->request->setAgent($this->agent);

        $this->assertSame($type, $this->request->getType());
        $this->assertSame($mediaFile, $this->request->getMediaFile());
        $this->assertSame($this->agent, $this->request->getAgent());
    }
}
