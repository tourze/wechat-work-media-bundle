<?php

namespace WechatWorkMediaBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatWorkBundle\Entity\Agent;
use WechatWorkMediaBundle\Request\UploadRequest;

class UploadRequestTest extends TestCase
{
    /**
     * 测试 UploadRequest 配置
     * 
     * 注意：此测试无法完全测试 UploadRequest 的功能，因为它依赖于 fopen 函数
     */
    public function testRequestConfiguration(): void
    {
        $request = new UploadRequest();
        $agent = new Agent();
        $type = 'image';
        $path = __FILE__; // 使用当前文件作为测试文件
        
        $request->setAgent($agent);
        $request->setType($type);
        $request->setPath($path);
        
        // 测试请求路径
        $this->assertEquals('/cgi-bin/media/upload', $request->getRequestPath());
        
        // 测试请求方法
        $this->assertEquals('POST', $request->getRequestMethod());
        
        // 测试查询参数
        $options = $request->getRequestOptions();
        
        // 检查查询参数
        $this->assertIsArray($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertArrayHasKey('type', $options['query']);
        $this->assertEquals($type, $options['query']['type']);
        
        // 检查头信息
        $this->assertArrayHasKey('headers', $options);
        $this->assertIsArray($options['headers']);
        $this->assertArrayHasKey('Content-Type', $options['headers']);
        $this->assertEquals('multipart/form-data', $options['headers']['Content-Type']);
        
        // 测试 Getter
        $this->assertEquals($type, $request->getType());
        $this->assertEquals($path, $request->getPath());
    }
} 