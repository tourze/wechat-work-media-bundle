<?php

namespace WechatWorkMediaBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkMediaBundle\Enum\MediaType;
use WechatWorkMediaBundle\Request\UploadTempMediaRequest;

class UploadTempMediaRequestTest extends TestCase
{
    private UploadTempMediaRequest $request;
    private Agent $agent;

    protected function setUp(): void
    {
        $this->request = new UploadTempMediaRequest();        $agent = $this->createMock(Agent::class);
        $this->agent = $agent;
    }

    public function test_request_extendsApiRequest(): void
    {
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }

    public function test_getRequestPath_returnsCorrectPath(): void
    {
        $path = $this->request->getRequestPath();
        
        $this->assertSame('/cgi-bin/media/upload', $path);
    }

    public function test_setType_setsTypeCorrectly(): void
    {
        $type = MediaType::IMAGE;
        $this->request->setType($type);
        
        $this->assertSame($type, $this->request->getType());
    }

    public function test_getType_returnsSetType(): void
    {
        $type = MediaType::VIDEO;
        $this->request->setType($type);
        
        $result = $this->request->getType();
        
        $this->assertSame($type, $result);
    }

    public function test_setMediaFile_setsMediaFileCorrectly(): void
    {
        $mediaFile = '/tmp/test_file.jpg';
        $this->request->setMediaFile($mediaFile);
        
        $this->assertSame($mediaFile, $this->request->getMediaFile());
    }

    public function test_getMediaFile_returnsSetMediaFile(): void
    {
        $mediaFile = '/tmp/another_test_file.mp4';
        $this->request->setMediaFile($mediaFile);
        
        $result = $this->request->getMediaFile();
        
        $this->assertSame($mediaFile, $result);
    }

    public function test_setAgent_setsAgentCorrectly(): void
    {
        $this->request->setAgent($this->agent);
        
        $this->assertSame($this->agent, $this->request->getAgent());
    }

    public function test_getAgent_returnsSetAgent(): void
    {
        $this->request->setAgent($this->agent);
        
        $result = $this->request->getAgent();
        
        $this->assertSame($this->agent, $result);
    }

    public function test_getRequestOptions_withTypeAndMediaFile(): void
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
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('body', $options);
        $this->assertArrayHasKey('type', $options['query']);
        $this->assertSame($type->value, $options['query']['type']);
        $this->assertArrayHasKey('media', $options['body']);
        $this->assertIsResource($options['body']['media']);
        
        // 清理临时文件
        fclose($options['body']['media']);
        unlink($tempFile);
    }

    public function test_allPropertiesWorkTogether(): void
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