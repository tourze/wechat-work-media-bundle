<?php

namespace WechatWorkMediaBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatWorkBundle\Entity\Agent;
use WechatWorkMediaBundle\Request\UploadRequest;

/**
 * @internal
 */
#[CoversClass(UploadRequest::class)]
final class UploadRequestTest extends RequestTestCase
{
    private UploadRequest $request;

    private Agent $agent;

    protected function setUp(): void
    {
        $this->request = new UploadRequest();
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

    public function testGetRequestMethodReturnsPost(): void
    {
        $method = $this->request->getRequestMethod();

        $this->assertSame('POST', $method);
    }

    public function testSetTypeSetsTypeCorrectly(): void
    {
        $type = 'video';
        $this->request->setType($type);

        $this->assertSame($type, $this->request->getType());
    }

    public function testGetTypeReturnsSetType(): void
    {
        $type = 'voice';
        $this->request->setType($type);

        $result = $this->request->getType();

        $this->assertSame($type, $result);
    }

    public function testSetPathSetsPathCorrectly(): void
    {
        $path = '/tmp/test_video.mp4';
        $this->request->setPath($path);

        $this->assertSame($path, $this->request->getPath());
    }

    public function testGetPathReturnsSetPath(): void
    {
        $path = '/tmp/another_test_file.wav';
        $this->request->setPath($path);

        $result = $this->request->getPath();

        $this->assertSame($path, $result);
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

    public function testGetRequestOptionsWithFileUpload(): void
    {
        $type = 'image';

        // 创建临时文件进行测试
        $tempFile = tempnam(sys_get_temp_dir(), 'test_upload');
        file_put_contents($tempFile, 'test image content');

        $this->request->setType($type);
        $this->request->setPath($tempFile);

        $options = $this->request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('multipart', $options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('headers', $options);
        $this->assertIsArray($options['query']);
        $this->assertIsArray($options['headers']);

        // 检查 query 参数
        $this->assertSame($type, $options['query']['type']);

        // 检查 headers
        $this->assertSame('multipart/form-data', $options['headers']['Content-Type']);

        // 检查 multipart 数据
        $this->assertIsArray($options['multipart']);
        $this->assertArrayHasKey('name', $options['multipart']);
        $this->assertArrayHasKey('contents', $options['multipart']);
        $this->assertSame('media', $options['multipart']['name']);
        $this->assertIsResource($options['multipart']['contents']);

        // 清理临时文件
        fclose($options['multipart']['contents']);
        unlink($tempFile);
    }

    public function testAllPropertiesWorkTogether(): void
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
