<?php

namespace WechatWorkMediaBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkMediaBundle\Request\UploadRequest;

class UploadRequestTest extends TestCase
{
    private UploadRequest $request;
    private Agent $agent;

    protected function setUp(): void
    {
        $this->request = new UploadRequest();        $agent = $this->createMock(Agent::class);
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

    public function test_getRequestMethod_returnsPost(): void
    {
        $method = $this->request->getRequestMethod();
        
        $this->assertSame('POST', $method);
    }

    public function test_setType_setsTypeCorrectly(): void
    {
        $type = 'video';
        $this->request->setType($type);
        
        $this->assertSame($type, $this->request->getType());
    }

    public function test_getType_returnsSetType(): void
    {
        $type = 'voice';
        $this->request->setType($type);
        
        $result = $this->request->getType();
        
        $this->assertSame($type, $result);
    }

    public function test_setPath_setsPathCorrectly(): void
    {
        $path = '/tmp/test_video.mp4';
        $this->request->setPath($path);
        
        $this->assertSame($path, $this->request->getPath());
    }

    public function test_getPath_returnsSetPath(): void
    {
        $path = '/tmp/another_test_file.wav';
        $this->request->setPath($path);
        
        $result = $this->request->getPath();
        
        $this->assertSame($path, $result);
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

    public function test_getRequestOptions_withFileUpload(): void
    {
        $type = 'image';
        
        // 创建临时文件进行测试
        $tempFile = tempnam(sys_get_temp_dir(), 'test_upload');
        file_put_contents($tempFile, 'test image content');
        
        $this->request->setType($type);
        $this->request->setPath($tempFile);
        
        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('multipart', $options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('headers', $options);
        
        // 检查 query 参数
        $this->assertSame($type, $options['query']['type']);
        
        // 检查 headers
        $this->assertSame('multipart/form-data', $options['headers']['Content-Type']);
        
        // 检查 multipart 数据
        $this->assertArrayHasKey('name', $options['multipart']);
        $this->assertArrayHasKey('contents', $options['multipart']);
        $this->assertSame('media', $options['multipart']['name']);
        $this->assertIsResource($options['multipart']['contents']);
        
        // 清理临时文件
        fclose($options['multipart']['contents']);
        unlink($tempFile);
    }

    public function test_allPropertiesWorkTogether(): void
    {
        $type = 'file';
        $path = '/tmp/document.pdf';
        
        $this->request->setType($type);
        $this->request->setPath($path);
        $this->request->setAgent($this->agent);
        
        $this->assertSame($type, $this->request->getType());
        $this->assertSame($path, $this->request->getPath());
        $this->assertSame($this->agent, $this->request->getAgent());
    }
} 