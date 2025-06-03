<?php

namespace WechatWorkMediaBundle\Tests\Request;

use HttpClientBundle\Request\ApiRequest;
use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkBundle\Request\RawResponseInterface;
use WechatWorkMediaBundle\Request\MediaGetRequest;

class MediaGetRequestTest extends TestCase
{
    private MediaGetRequest $request;
    private Agent $agent;

    protected function setUp(): void
    {
        $this->request = new MediaGetRequest();
        /** @var Agent $agent */
        $agent = $this->createMock(Agent::class);
        $this->agent = $agent;
    }

    public function test_request_extendsApiRequest(): void
    {
        $this->assertInstanceOf(ApiRequest::class, $this->request);
    }

    public function test_request_implementsRawResponseInterface(): void
    {
        $this->assertInstanceOf(RawResponseInterface::class, $this->request);
    }

    public function test_getRequestPath_returnsCorrectPath(): void
    {
        $path = $this->request->getRequestPath();
        
        $this->assertSame('/cgi-bin/media/get', $path);
    }

    public function test_getRequestMethod_returnsGet(): void
    {
        $method = $this->request->getRequestMethod();
        
        $this->assertSame('GET', $method);
    }

    public function test_setMediaId_setsMediaIdCorrectly(): void
    {
        $mediaId = 'test_media_id_123';
        $this->request->setMediaId($mediaId);
        
        $this->assertSame($mediaId, $this->request->getMediaId());
    }

    public function test_getMediaId_returnsSetMediaId(): void
    {
        $mediaId = 'another_media_id_456';
        $this->request->setMediaId($mediaId);
        
        $result = $this->request->getMediaId();
        
        $this->assertSame($mediaId, $result);
    }

    public function test_getRequestOptions_withMediaId(): void
    {
        $mediaId = 'test_media_id_789';
        $this->request->setMediaId($mediaId);
        
        $options = $this->request->getRequestOptions();
        
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertArrayHasKey('media_id', $options['query']);
        $this->assertSame($mediaId, $options['query']['media_id']);
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
} 