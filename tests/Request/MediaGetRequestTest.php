<?php

namespace WechatWorkMediaBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Request\RawResponseInterface;
use WechatWorkMediaBundle\Request\MediaGetRequest;

/**
 * @internal
 */
#[CoversClass(MediaGetRequest::class)]
final class MediaGetRequestTest extends RequestTestCase
{
    private MediaGetRequest $request;

    private Agent $agent;

    protected function setUp(): void
    {
        $this->request = new MediaGetRequest();
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

    public function testRequestImplementsRawResponseInterface(): void
    {
        $this->assertInstanceOf(RawResponseInterface::class, $this->request);
    }

    public function testGetRequestPathReturnsCorrectPath(): void
    {
        $path = $this->request->getRequestPath();

        $this->assertSame('/cgi-bin/media/get', $path);
    }

    public function testGetRequestMethodReturnsGet(): void
    {
        $method = $this->request->getRequestMethod();

        $this->assertSame('GET', $method);
    }

    public function testSetMediaIdSetsMediaIdCorrectly(): void
    {
        $mediaId = 'test_media_id_123';
        $this->request->setMediaId($mediaId);

        $this->assertSame($mediaId, $this->request->getMediaId());
    }

    public function testGetMediaIdReturnsSetMediaId(): void
    {
        $mediaId = 'another_media_id_456';
        $this->request->setMediaId($mediaId);

        $result = $this->request->getMediaId();

        $this->assertSame($mediaId, $result);
    }

    public function testGetRequestOptionsWithMediaId(): void
    {
        $mediaId = 'test_media_id_789';
        $this->request->setMediaId($mediaId);

        $options = $this->request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertArrayHasKey('media_id', $options['query']);
        $this->assertSame($mediaId, $options['query']['media_id']);
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
}
