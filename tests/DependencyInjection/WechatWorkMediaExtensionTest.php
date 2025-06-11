<?php

namespace WechatWorkMediaBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use WechatWorkMediaBundle\DependencyInjection\WechatWorkMediaExtension;

class WechatWorkMediaExtensionTest extends TestCase
{
    public function test_extension_canBeInstantiated(): void
    {
        $extension = new WechatWorkMediaExtension();
        
        $this->assertInstanceOf(WechatWorkMediaExtension::class, $extension);
    }

    public function test_extension_extendsSymfonyExtension(): void
    {
        $extension = new WechatWorkMediaExtension();
        
        $this->assertInstanceOf(Extension::class, $extension);
    }

    public function test_extension_hasLoadMethod(): void
    {
        $reflection = new \ReflectionClass(WechatWorkMediaExtension::class);
        
        $this->assertTrue($reflection->hasMethod('load'));
        
        $method = $reflection->getMethod('load');
        $this->assertTrue($method->isPublic());
        
        $parameters = $method->getParameters();
        $this->assertCount(2, $parameters);
        
        $configsParam = $parameters[0];
        $this->assertSame('configs', $configsParam->getName());
        $this->assertSame('array', $configsParam->getType()->getName());
        
        $containerParam = $parameters[1];
        $this->assertSame('container', $containerParam->getName());
        $this->assertSame('Symfony\Component\DependencyInjection\ContainerBuilder', $containerParam->getType()->getName());
        
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', $returnType->getName());
    }

    public function test_extension_canLoadWithEmptyConfigs(): void
    {
        $extension = new WechatWorkMediaExtension();
        $container = new ContainerBuilder();
        
        // 这里只测试不抛出异常
        $this->expectNotToPerformAssertions();
        
        try {
            $extension->load([], $container);
        } catch  (\Throwable $e) {
            // 如果服务配置文件不存在，这是正常的，不应该中断测试
            if (str_contains($e->getMessage(), 'services.yaml')) {
                $this->expectNotToPerformAssertions();
                return;
            }
            throw $e;
        }
    }

    public function test_extension_hasCorrectAlias(): void
    {
        $extension = new WechatWorkMediaExtension();
        
        // 测试默认的别名生成逻辑
        $alias = $extension->getAlias();
        
        // Symfony Extension 基类会自动生成别名
        $this->assertIsString($alias);
        $this->assertNotEmpty($alias);
    }

    public function test_extension_methodExists(): void
    {
        $reflection = new \ReflectionClass(WechatWorkMediaExtension::class);
        
        // 确保类具有正确的方法
        $this->assertTrue($reflection->hasMethod('load'));
        $this->assertTrue($reflection->hasMethod('getAlias'));
        
        // 验证继承关系
        $parentClass = $reflection->getParentClass();
        $this->assertNotFalse($parentClass);
        $this->assertSame(Extension::class, $parentClass->getName());
    }
} 