<?php

namespace WechatWorkMediaBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkMediaBundle\Request\MediaGetRequest;

class MediaGetRequestTest extends TestCase
{
    public function testRequestConfiguration(): void
    {
        $request = new MediaGetRequest();
        $agent = new Agent();
        $mediaId = 'test_media_id_123';
        
        $request->setAgent($agent);
        $request->setMediaId($mediaId);
        
        // 测试请求路径
        $this->assertEquals('/cgi-bin/media/get', $request->getRequestPath());
        
        // 测试请求方法
        $this->assertEquals('GET', $request->getRequestMethod());
        
        // 测试查询参数
        $options = $request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertArrayHasKey('media_id', $options['query']);
        $this->assertEquals($mediaId, $options['query']['media_id']);
        
        // 测试 Getter
        $this->assertEquals($mediaId, $request->getMediaId());
    }
} 